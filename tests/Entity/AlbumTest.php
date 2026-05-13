<?php

namespace App\Tests\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testAlbumName(): void
    {
        $album = new Album();

        $album->setName('Voyages');

        $this->assertSame('Voyages', $album->getName());
    }
}
