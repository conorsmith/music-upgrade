<?php

namespace App\Persistence;

use Carbon\Carbon;
use App\Clock;
use App\Models\Album;
use App\Models\AlbumId;
use App\Models\AlbumRepository;
use App\Models\AlbumTitle;
use App\Models\Artist;
use App\Models\ArtistId;
use App\Models\ArtistName;
use App\Models\Discography;
use App\Models\DiscographyRepository;
use App\Models\FirstListenTime;
use App\Models\NullReleaseDate;
use App\Models\Rating;
use App\Models\ReleaseDate;
use Illuminate\Cache\Repository;
use Ramsey\Uuid\Uuid;

class AlbumCacheRepository implements AlbumRepository, DiscographyRepository
{
    private const ALBUM_INDEX_KEY = 'album_index';

    /** @var Repository */
    private $cache;

    /** @var Clock */
    private $clock;

    public function __construct(Repository $cache, Clock $clock)
    {
        $this->cache = $cache;
        $this->clock = $clock;
    }

    public function save(Album $album)
    {
        $albumIndex = $this->cache->get(self::ALBUM_INDEX_KEY);

        if (!is_array($albumIndex)) {
            $albumIndex = [];
        }

        $albumIndex[] = $album->getId()->__toString();

        $this->cache->forever(self::ALBUM_INDEX_KEY, $albumIndex);

        $this->cache->forever($album->getId(), [
            'id'          => $album->getId()->__toString(),
            'title'       => $album->getTitle()->__toString(),
            'artist'      => [
                'id'   => $album->getArtist()->getId()->__toString(),
                'name' => $album->getArtist()->getName()->__toString(),
            ],
            'releaseDate' => $album->getReleaseDate()->getValue(),
            'listenedAt'  => $album->getListenedAt()->getDate()->format("Y-m-d"),
            'rating'      => $album->getRating()->getValue(),
        ]);
    }

    public function destroy()
    {
        $this->cache->flush();
    }

    public function allByFirstListenTime()
    {
        $albumIndex = $this->cache->get(self::ALBUM_INDEX_KEY);

        if (!is_array($albumIndex)) {
            return [];
        }

        $albums = [];

        foreach ($albumIndex as $albumId) {
            $album = $this->reconstituteAlbum(
                $this->cache->get($albumId)
            );

            $albums[] = $album;
        }

        return array_reverse($albums);
    }

    public function findForThisWeek()
    {
        $albumIndex = $this->cache->get(self::ALBUM_INDEX_KEY);

        if (!is_array($albumIndex)) {
            return [];
        }

        $albums = [];

        foreach ($albumIndex as $albumId) {
            $album = $this->reconstituteAlbum(
                $this->cache->get($albumId)
            );

            if ($album->getListenedAt()->isFromListeningPeriod($this->clock->mondayThisWeek())) {
                $albums[] = $album;
            }
        }

        return $albums;
    }

    public function allByArtistName()
    {
        $albumIndex = $this->cache->get(AlbumCacheRepository::ALBUM_INDEX_KEY);

        if (!is_array($albumIndex)) {
            return [];
        }

        $albumsByArtist = [];

        foreach ($albumIndex as $albumId) {
            $album = $this->reconstituteAlbum(
                $this->cache->get($albumId)
            );

            $artistId = $album->getArtist()->getId()->__toString();

            if (!array_key_exists($artistId, $albumsByArtist)) {
                $albumsByArtist[$artistId] = [];
            }

            $albumsByArtist[$artistId][] = $album;
        }

        $discographies = [];

        foreach ($albumsByArtist as $artistId => $albums) {
            $albums = array_reverse($albums);
            usort($albums, function ($a, $b) {
                return strcasecmp(
                    $a->getReleaseDate()->getValue(),
                    $b->getReleaseDate()->getValue()
                );
            });

            $discographies[] = Discography::fromAlbums($albums);
        }

        usort($discographies, function ($a, $b) {
            return strcasecmp(
                $a->getArtist()->getName(),
                $b->getArtist()->getName()
            );
        });

        return $discographies;
    }

    private function reconstituteAlbum(array $albumData): Album
    {
        return new Album(
            new AlbumId(Uuid::fromString($albumData['id'])),
            AlbumTitle::fromString($albumData['title']),
            new Artist(
                new ArtistId(Uuid::fromString($albumData['artist']['id'])),
                ArtistName::fromString($albumData['artist']['name'])
            ),
            is_null($albumData['releaseDate'])
                ? NullReleaseDate::create()
                : new ReleaseDate($albumData['releaseDate']),
            new FirstListenTime(Carbon::createFromFormat("Y-m-d", $albumData['listenedAt'])),
            new Rating($albumData['rating'])
        );
    }
}
