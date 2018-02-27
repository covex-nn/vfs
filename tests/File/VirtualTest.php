<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\Tests\File;

use Covex\Stream\File\Entity;
use Covex\Stream\File\Virtual;

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
