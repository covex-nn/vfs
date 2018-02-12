<?php

declare(strict_types=1);

namespace Covex\Stream\Tests;

use Covex\Stream\File\Entity;
use Covex\Stream\Partition;
use PHPUnit\Framework\TestCase;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class PartitionTest extends TestCase
{
    public function testWrongRoot1(): void
    {
        $this->expectException(\Covex\Stream\Exception::class);

        $entity = Entity::newInstance(__FILE__);
        $fs = new Partition($entity);
    }

    public function testWrongRoot2(): void
    {
        $this->expectException(\Covex\Stream\Exception::class);

        $entity = Entity::newInstance(__FILE__.'.ksdckjsbcajhsc');
        $fs = new Partition($entity);
    }

    public function testInstance(): void
    {
        $fs = new Partition();

        $root = $fs->getRoot();
        $this->assertTrue($root->file_exists());
        $this->assertTrue($root->is_dir());
    }
}
