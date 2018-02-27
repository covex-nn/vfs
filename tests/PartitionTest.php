<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\Tests;

use Covex\Stream\File\Entity;
use Covex\Stream\Partition;
use PHPUnit\Framework\TestCase;

class PartitionTest extends TestCase
{
    /**
     * @expectedException \Covex\Stream\Exception
     * @expectedExceptionMessage Root directory is not valid
     */
    public function testWrongRoot1(): void
    {
        $entity = Entity::newInstance(__FILE__);
        new Partition($entity);
    }

    /**
     * @expectedException \Covex\Stream\Exception
     * @expectedExceptionMessage Root directory is not valid
     */
    public function testWrongRoot2(): void
    {
        $entity = Entity::newInstance(__FILE__.'.ksdckjsbcajhsc');
        new Partition($entity);
    }

    public function testInstance(): void
    {
        $fs = new Partition();

        $root = $fs->getRoot();
        $this->assertTrue($root->file_exists());
        $this->assertTrue($root->is_dir());
    }
}
