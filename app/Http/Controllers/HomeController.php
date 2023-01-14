<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Infrastructure\TemplateVariables\Album as AlbumTemplateVariable;
use App\Models\Album;
use App\Models\AlbumRepository;
use App\Models\DiscographyRepository;

class HomeController extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    /** @var DiscographyRepository */
    private $discographyRepo;

    public function __construct(AlbumRepository $albumRepo, DiscographyRepository $discographyRepo)
    {
        $this->albumRepo = $albumRepo;
        $this->discographyRepo = $discographyRepo;
    }

    public function home()
    {
        $albums = $this->albumRepo->findForThisWeek();

        return view('home', [
            'albums' => collect($albums)
                ->map(function (Album $album) {
                    return AlbumTemplateVariable::present($album);
                })
                ->toArray(),
        ]);
    }

    public function albums()
    {
        $albums = $this->albumRepo->allByFirstListenTime();

        return view('albums', [
            'thisWeek' => Carbon::now("Europe/Dublin")->dayOfWeek === Carbon::MONDAY
                ? new Carbon("today", "Europe/Dublin")
                : new Carbon("last Monday", "Europe/Dublin"),
            'albums' => collect($albums)
                ->map(function (Album $album) {
                    return AlbumTemplateVariable::present($album);
                })
                ->toArray(),
        ]);
    }

    public function artists()
    {
        $discographies = $this->discographyRepo->allByArtistName();

        return view('artists', [
            'artists' => collect($discographies)
                ->map(function ($discography) {
                    return collect($discography->getAlbums())
                        ->map(function (Album $album) {
                            return AlbumTemplateVariable::present($album);
                        })
                        ->toArray();
                })
                ->toArray(),
        ]);
    }
}
