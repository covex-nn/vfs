<?php

declare(strict_types=1);

namespace Covex\Stream\Tests\File;

use Covex\Stream\File\Deleted;
use Covex\Stream\File\Entity;
use PHPUnit\Framework\TestCase;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class DeletedTest extends TestCase
{
    public function testInstance(): void
    {
        $realEntity = Entity::newInstance(__FILE__);

        $deletedEntity = Deleted::newInstance($realEntity);
        $this->assertFalse($deletedEntity->file_exists());

        $this->assertEquals($realEntity, $deletedEntity->getRealEntity());
    }
}
