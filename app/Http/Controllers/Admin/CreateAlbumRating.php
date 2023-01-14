<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlbumId;
use App\Models\AlbumRepository;
use App\Models\Rating;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class CreateAlbumRating extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    public function __construct(AlbumRepository $albumRepo)
    {
        $this->albumRepo = $albumRepo;
    }

    public function __invoke(Request $request, string $id)
    {
        $album = $this->albumRepo->find(new AlbumId(Uuid::fromString($id)));

        $album->rate(new Rating(intval($request->input('rating'))));

        $this->albumRepo->update($album);

        return redirect("/dashboard");
    }
}
