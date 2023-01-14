<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
use Ramsey\Uuid\Uuid;

class ModifyAlbum extends Controller
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

    public function __invoke(Request $request, string $id)
    {
        $album = $this->albumRepo->find(
            new AlbumId(Uuid::fromString($id))
        );

        $artist = $this->artistRepo->findByName(
            ArtistName::fromString($request->input('artist'))
        );

        if (is_null($artist)) {
            $artist = new Artist(
                ArtistId::generate(),
                ArtistName::fromString($request->input('artist'))
            );
        }

        $album->setArtist($artist);
        $album->setTitle(AlbumTitle::fromString($request->input('title')));
        $album->setReleaseDate(new ReleaseDate(intval($request->input('releaseDate'))));
        $album->setListenedAt(FirstListenTime::fromDateAsString($request->input('listenedAt')));
        $album->rate(new Rating(intval($request->input('rating'))));

        $this->albumRepo->update($album);

        $request->session()->flash('success', "Album data updated");

        return redirect("/admin/album/{$id}");
    }
}
