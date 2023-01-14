<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlbumRepository;
use App\Models\DiscographyRepository;
use App\Remote\GoogleDrive;
use App\Remote\ImportRepository;

class ImportFromGoogleSheets extends Controller
{
    /** @var AlbumRepository */
    private $albumRepo;

    /** @var ImportRepository */
    private $importRepo;

    /** @var GoogleDrive */
    private $googleDrive;

    public function __construct(
        AlbumRepository $albumRepo,
        DiscographyRepository $discographyRepo,
        ImportRepository $importRepo,
        GoogleDrive $googleDrive
    ) {
        $this->albumRepo = $albumRepo;
        $this->importRepo = $importRepo;
        $this->googleDrive = $googleDrive;
    }

    public function __invoke()
    {
        $this->importRepo->deleteAllImportedAlbums();

        $import = $this->googleDrive->requestAlbums();

        foreach ($import->getAlbums() as $album) {
            $this->albumRepo->save($album);
        }

        $this->importRepo->markAllAlbumsAsImported();

        return redirect('/dashboard');
    }
}
