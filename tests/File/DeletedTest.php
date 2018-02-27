<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\Tests\File;

use Covex\Stream\File\Deleted;
use Covex\Stream\File\Entity;
use PHPUnit\Framework\TestCase;

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
