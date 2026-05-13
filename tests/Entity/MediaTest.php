<?php

namespace App\Tests\Entity;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function testMediaFields(): void
    {
        $media = new Media();
        $user = new User();
        $album = new Album();

        $media->setTitle('Photo test');
        $media->setPath('images/test.jpg');
        $media->setUser($user);
        $media->setAlbum($album);

        $this->assertSame('Photo test', $media->getTitle());
        $this->assertSame('images/test.jpg', $media->getPath());
        $this->assertSame($user, $media->getUser());
        $this->assertSame($album, $media->getAlbum());
    }
}