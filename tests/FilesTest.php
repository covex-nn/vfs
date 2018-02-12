<?php

declare(strict_types=1);

namespace Covex\Stream\Tests;

use Covex\Stream\Files;
use PHPUnit\Framework\TestCase;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class FilesTest extends TestCase
{
    public function testInstance(): void
    {
        $files = new Files();
        $this->assertTrue($files instanceof Files);

        $dir1 = $files->mkdir();
        $this->assertFileExists($dir1);
        $this->assertDirectoryExists($dir1);
        $this->assertIsWritable($dir1);

        $file1 = $files->tempnam();
        $this->assertFileNotExists($file1);
        $dir2 = dirname($file1);
        $this->assertIsWritable($dir2);
        file_put_contents($file1, 'qwerty1');

        $file2 = $files->tempnam();
        file_put_contents($file2, 'qwerty2');
        $files->delete($file2);
        $this->assertFileNotExists($file2);

        unset($files);

        clearstatcache();
        $this->assertFileNotExists($dir1);
        $this->assertFileNotExists($file1);
    }

    public function testDeleteSymlink(): void
    {
        $files = new Files();

        $dirStorage = $files->mkdir();

        $targetDir = $files->mkdir();
        file_put_contents($targetDir.'/just_a_file', 'asdf');
        $targetFile = $files->tempnam();
        file_put_contents($targetFile, 'qwerty');

        $symlinkDir = $dirStorage.'/linkDir';
        $symlinkFile = $dirStorage.'/linkFile';

        symlink($targetDir, $symlinkDir);
        symlink($targetFile, $symlinkFile);

        $files->delete($dirStorage);

        $this->assertFileNotExists($symlinkDir);
        $this->assertFileExists($targetDir);

        $this->assertFileNotExists($symlinkFile);
        $this->assertFileExists($targetFile);
    }

    public function testDeleteBadSymlink(): void
    {
        $files = new Files();

        $dirStorage = $files->mkdir();

        $targetDir = $files->mkdir();
        $targetFile = $targetDir.'/qwerty';
        file_put_contents($targetFile, 'asdf');

        $symlinkDir = $dirStorage.'/linkDir';
        $symlinkFile = $dirStorage.'/linkFile';

        symlink($targetDir, $symlinkDir);
        symlink($targetFile, $symlinkFile);

        $files->delete($targetDir);

        $files->delete($symlinkDir);
        $files->delete($symlinkFile);

        $this->assertFileNotExists($symlinkDir);
        $this->assertFileNotExists($symlinkFile);
    }
}
