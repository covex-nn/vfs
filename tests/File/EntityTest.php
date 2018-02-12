<?php

declare(strict_types=1);

namespace Cove\Stream\Tests\File;

use Covex\Stream\File\Entity;
use PHPUnit\Framework\TestCase;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class EntityTest extends TestCase
{
    /**
     * @dataProvider providerGetRelativePath
     */
    public function testFixPath($path): void
    {
        $this->assertEquals('dir1/dir2', Entity::fixPath($path));
    }

    public function providerGetRelativePath(): array
    {
        return [
            ['dir1/dir2/'],
            ['/dir1//dir2'],
            ['\dir1\dir2'],
            ['\\\\dir1\\dir2\\'],
        ];
    }

    public function testInstance(): void
    {
        $instance = Entity::newInstance(__FILE__);

        $this->assertEquals(basename(__FILE__), $instance->basename());

        $unixPathToFile = str_replace('\\', '/', __FILE__);
        $this->assertEquals($unixPathToFile, $instance->path());

        $this->assertEquals(is_writable(__FILE__), $instance->is_writable());
        $this->assertEquals(is_readable(__FILE__), $instance->is_readable());

        $this->assertFalse($instance->is_dir());
        $this->assertTrue($instance->is_file());
        $this->assertTrue($instance->file_exists());
    }
}
