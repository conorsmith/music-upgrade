<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\TemplateVariables\Album as AlbumTemplateVariable;
use App\Models\Album;
use App\Models\AlbumRepository;
use App\Models\DiscographyRepository;
use Illuminate\Support\Facades\Session;

class ShowDashboard extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    /** @var DiscographyRepository */
    private $discographyRepo;

    public function __construct(
        AlbumRepository $albumRepo,
        DiscographyRepository $discographyRepo
    ) {
        $this->albumRepo = $albumRepo;
        $this->discographyRepo = $discographyRepo;
    }

    public function __invoke()
    {
        return view('dashboard', [
            'hasAccessToken'  => Session::has('google.access_token'),
            'albumCount'      => count($this->albumRepo->allByFirstListenTime()),
            'artistCount'     => count($this->discographyRepo->allByArtistName()),
            'thisWeeksAlbums' => collect($this->albumRepo->findForThisWeek())
                ->map(function (Album $album) {
                    return AlbumTemplateVariable::present($album);
                })
                ->toArray()
        ]);
    }
}
