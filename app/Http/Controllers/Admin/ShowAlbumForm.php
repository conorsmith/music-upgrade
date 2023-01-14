<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\TemplateVariables\Album as AlbumTemplateVariable;
use App\Models\AlbumId;
use App\Models\AlbumRepository;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ShowAlbumForm extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    public function __construct(AlbumRepository $albumRepo)
    {
        $this->albumRepo = $albumRepo;
    }

    public function __invoke(Request $request, string $id)
    {
        return view('admin.album', [
            'album' => AlbumTemplateVariable::present(
                $this->albumRepo->find(
                    new AlbumId(Uuid::fromString($id))
                )
            )
        ]);
    }
}
