<?php

namespace App\Remote;

interface ImportRepository
{
    public function markAllAlbumsAsImported(): void;
    public function deleteAllImportedAlbums(): void;
}
