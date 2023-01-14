<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostCreateWeeksAlbums;
use App\Models\Album;
use App\Models\AlbumId;
use App\Models\AlbumRepository;
use App\Models\AlbumTitle;
use App\Models\Artist;
use App\Models\ArtistId;
use App\Models\ArtistName;
use App\Models\ArtistRepository;
use App\Models\FirstListenTime;
use App\Models\Rating;
use App\Models\ReleaseDate;
use Illuminate\Http\Request;

class CreateWeeksAlbums extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    /** @var ArtistRepository */
    private $artistRepo;

    public function __construct(AlbumRepository $albumRepo, ArtistRepository $artistRepo)
    {
        $this->albumRepo = $albumRepo;
        $this->artistRepo = $artistRepo;
    }

    public function __invoke(Request $request)
    {
        $requestPayload = new PostCreateWeeksAlbums(
            $request->input('week'),
            $request->input('artist'),
            $request->input('album'),
            $request->input('releaseDate')
        );

        /** @var \App\Http\Requests\Album $albumPayload */
        foreach ($requestPayload->getAlbums() as $albumPayload) {
            $artist = $this->artistRepo->findByName(
                ArtistName::fromString($albumPayload->getArtistName())
            );

            if (is_null($artist)) {
                $artist = new Artist(
                    ArtistId::generate(),
                    ArtistName::fromString($albumPayload->getArtistName())
                );
            }

            $album = new Album(
                AlbumId::generate(),
                AlbumTitle::fromString($albumPayload->getTitle()),
                $artist,
                new ReleaseDate($albumPayload->getReleaseDate()),
                new FirstListenTime($requestPayload->getWeek()),
                new Rating(0)
            );

            $this->albumRepo->save($album);
        }

        return redirect("/dashboard");
    }
}
