<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait IsIdentifier
{
    public static function generate()
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @param Uuid $uuid
     */
    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uuid->toString();
    }
}
