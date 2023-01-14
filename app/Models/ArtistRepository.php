<?php

namespace App\Models;

interface ArtistRepository
{
    public function findByName(ArtistName $name): ?Artist;
}
