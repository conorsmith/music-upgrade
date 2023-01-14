<?php

namespace App\Remote;

use App\Models\Album;
use Illuminate\Support\Arr;

class GoogleSheetsImport
{
    /**
     * @param array $unprocessedRows
     * @return GoogleSheetsImport
     */
    public static function fromUnprocessedRows(array $unprocessedRows)
    {
        $albums = [];
        $artists = [];
        $previousDate = false;

        $unprocessedRows = collect($unprocessedRows)
            ->reject(function ($row) {
                return is_null($row['Album']);
            })
            ->toArray();

        foreach ($unprocessedRows as $row) {
            if ($row['Week']) {
                $previousDate = $row['Week'];
            }

            if (array_key_exists($row['Artist'], $artists)) {
                $row['Artist'] = $artists[$row['Artist']];
            }

            $rating = Arr::get($row, 'Rating');

            if (is_null($rating) && array_key_exists('"Rating"', $row)) {
                $rating = intval($row['"Rating"']) * 2;
            }

            $album = Album::fromGoogleSheetsRow([
                'title'       => $row['Album'],
                'artist'      => $row['Artist'],
                'listened_at' => $previousDate,
                'year'        => Arr::get($row, 'Year'),
                'rating'      => $rating,
                'notes'       => Arr::get($row, 'Notes'),
            ]);

            if (is_string($row['Artist'])) {
                $artists[$row['Artist']] = $album->getArtist();
            }

            $albums[] = $album;
        }

        return new self($albums);
    }

    /**
     * @var Album[]
     */
    private $albums;

    /**
     * @param Album[] $albums
     */
    private function __construct(array $albums)
    {
        $this->albums = $albums;
    }

    /**
     * @return Album[]
     */
    public function getAlbums()
    {
        return $this->albums;
    }
}

