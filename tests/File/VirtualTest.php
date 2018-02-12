<?php

declare(strict_types=1);

namespace Covex\Stream\Tests\File;

use Covex\Stream\File\Entity;
use Covex\Stream\File\Virtual;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class VirtualTest extends \PHPUnit\Framework\TestCase
{
    public function testInstance(): void
    {
        $real = Entity::newInstance(__FILE__);
        $virtual = Virtual::newInstance($real, '/tmp/qqq');

        $this->assertEquals($real->basename(), $virtual->basename());
        $this->assertEquals('/tmp/qqq', $virtual->path());
        $this->assertEquals($real, $virtual->getRealEntity());
    }
}
