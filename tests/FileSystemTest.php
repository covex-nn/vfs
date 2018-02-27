<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\Tests;

use Covex\Stream\FileSystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSystemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerGetRelativePath
     */
    public function testGetRelativePath($url): void
    {
        $this->assertEquals('dir1/dir2', FileSystem::getRelativePath($url));
    }

    public function providerGetRelativePath(): array
    {
        return [
            ['test://dir1/dir2/'],
            ['test:///dir1//dir2'],
            ['test://\dir1\dir2'],
            ['test://\\dir1\\dir2'],
        ];
    }

    public function testRegister1(): void
    {
        $this->streamStart(__DIR__);
        $this->assertTrue(in_array('vfs-test', stream_get_wrappers()));

        $this->assertEquals(
            file_get_contents(__FILE__), file_get_contents('vfs-test://'.basename(__FILE__))
        );

        $this->streamStop();
        $this->assertFalse(in_array('vfs-test', stream_get_wrappers()));
    }

    public function testFiles(): void
    {
        $this->filesStart();

        $this->assertTrue(is_file('vfs-test://file1.txt'));
        $this->assertEquals('file1', file_get_contents('vfs-test://file1.txt'));
        $this->assertDirectoryExists('vfs-test://dir1');
        $this->assertTrue(is_file('vfs-test://dir1/file2.txt'));
        $this->assertEquals('file2', file_get_contents('vfs-test://dir1/file2.txt'));
        $this->assertDirectoryExists('vfs-test://dir1/dir5');
        $this->assertTrue(is_file('vfs-test://dir1/dir5/file5.txt'));
        $this->assertEquals('file5', file_get_contents('vfs-test://dir1/dir5/file5.txt'));

        $this->assertFileNotExists('vfs-test://dir1/dir3/file_not_exists.txt');
        $this->assertFileNotExists('vfs-test://dir1/dir5/file_not_exists.txt');

        $fp1 = @fopen('vfs-test://dir1', 'r');
        $this->assertFalse($fp1);

        $fp2 = @fopen('vfs-test://dir1/dir2/dir3/file_not_exists', 'r');
        $this->assertFalse($fp2);

        $od1 = @opendir('vfs-test://file1.txt');
        $this->assertFalse($od1);

        $this->filesStop();
    }

    public function testUnlinkRmdir(): void
    {
        $this->filesStart();

        $stat1 = stat('vfs-test://dir1/dir5');
        $this->assertInternalType('array', $stat1);

        $mkdir1 = @mkdir('vfs-test://dir1/dir5');
        $this->assertFalse($mkdir1);

        $rmdir1 = @rmdir('vfs-test://dir1/dir5');
        $this->assertFalse($rmdir1);

        $rmdir2 = @rmdir('vfs-test://dir1/dir5/file5.txt');
        $this->assertFalse($rmdir2);

        $unlink1 = @unlink('vfs-test://dir1/dir5');
        $this->assertFalse($unlink1);

        $unlink2 = @unlink('vfs-test://dir1/dir5/file_not_exists.txt');
        $this->assertFalse($unlink2);

        $unlink3 = @unlink('vfs-test://dir1/dir_not_exists/file_not_exists.txt');
        $this->assertFalse($unlink3);

        $unlink4 = unlink('vfs-test://dir1/dir5/file5.txt');
        $this->assertTrue($unlink4);

        $rmdir3 = rmdir('vfs-test://dir1/dir5');
        $this->assertTrue($rmdir3);

        $this->assertFileNotExists('vfs-test://dir1/dir5/file5.txt');
        $this->assertFileNotExists('vfs-test://dir1/dir5');

        $this->filesStop();
    }

    public function testRename(): void
    {
        $this->filesStart();

        $rename1 = @rename('vfs-test://file1.txt', 'vfs-test://dir1');
        $this->assertFalse($rename1);

        $rename2 = @rename('vfs-test://file2.txt', 'vfs-test://file3.txt');
        $this->assertFalse($rename2);

        $rename3 = rename('vfs-test://file1.txt', 'vfs-test://file2.txt');
        $this->assertTrue($rename3);
        $this->assertFileNotExists('vfs-test://file1.txt');
        $this->assertFileExists('vfs-test://file2.txt');
        $this->assertEquals('file1', file_get_contents('vfs-test://file2.txt'));

        $rename4 = rename('vfs-test://dir1', 'vfs-test://dir2');
        $this->assertTrue($rename4);
        $this->assertEquals('file5', file_get_contents('vfs-test://dir2/dir5/file5.txt'));
        $this->assertFileNotExists('vfs-test://dir1/dir5/file5.txt');

        mkdir('vfs-test://dir1');
        file_put_contents('vfs-test://dir1/file7.txt', 'file7');
        rename('vfs-test://dir1', 'vfs-test://dir2/dir5/dir7');
        $this->assertEquals('file7', file_get_contents('vfs-test://dir2/dir5/dir7/file7.txt'));

        $this->filesStop();
    }

    public function testRealFS(): void
    {
        $this->streamStart(__DIR__.'/FS_realdir');

        $this->assertFileExists('vfs-test://dir1/dir2/dir3/file3.txt');
        $this->assertFileNotExists('vfs-test://dir1/dir2/dir3/dir4/file4.txt');

        $paths1 = $this->getAllPaths('vfs-test://dir1');
        $expectedPaths1 = [
            'vfs-test://dir1/dir2',
            'vfs-test://dir1/dir2/dir3',
            'vfs-test://dir1/dir2/dir3/file3.txt',
            'vfs-test://dir1/dir2/file2.txt',
            'vfs-test://dir1/file1.txt',
        ];
        $this->assertEquals($expectedPaths1, $paths1);

        unlink('vfs-test://dir1/dir2/dir3/file3.txt');
        rmdir('vfs-test://dir1/dir2/dir3');

        $paths2 = $this->getAllPaths('vfs-test://dir1');
        $expectedPaths2 = [
            'vfs-test://dir1/dir2',
            'vfs-test://dir1/dir2/file2.txt',
            'vfs-test://dir1/file1.txt',
        ];
        $this->assertEquals($expectedPaths2, $paths2);

        $this->assertFileExists(__DIR__.'/FS_realdir/dir1/dir2/dir3/file3.txt');
        $this->assertFileExists(__DIR__.'/FS_realdir/dir1/dir2/dir3');

        $fp1 = @fopen('vfs-test://dir1/file1.txt', 'x+');
        $this->assertFalse($fp1);

        $this->streamStop();
    }

    public function testCommit1(): void
    {
        $this->streamStart();

        $commit = FileSystem::commit('vfs-test');
        $this->assertTrue($commit);

        $this->streamStop();
    }

    public function testCommit2(): void
    {
        $commit1 = FileSystem::commit('vfs-test-not-a-protocol');
        $this->assertFalse($commit1);

        $this->streamStart(null, 'vfs1-test');
        mkdir('vfs1-test://root');
        file_put_contents('vfs1-test://root/file0.txt', 'file0');
        mkdir('vfs1-test://root/dir1');
        file_put_contents('vfs1-test://root/dir1/file1.txt', 'file1');
        mkdir('vfs1-test://root/dir1/dir2');
        file_put_contents('vfs1-test://root/dir1/dir2/file2.txt', 'file2');
        mkdir('vfs1-test://root/dir3');

        /*
         * dir1
         * dir1/dir2
         * dir1/dir2/file2.txt (file2)
         * dir1/file1.txt (file1)
         * dir3
         * file0.txt (file0)
         */
        $this->streamStart('vfs1-test://root', 'vfs2-test');

        file_put_contents('vfs2-test://file0-0.txt', 'file0-0');
        unlink('vfs2-test://file0-0.txt');
        file_put_contents('vfs2-test://file0.txt', 'file0-0');
        unlink('vfs2-test://dir1/dir2/file2.txt');
        rmdir('vfs2-test://dir1/dir2');
        rename('vfs2-test://dir1/file1.txt', 'vfs2-test://dir1/dir2');
        rmdir('vfs2-test://dir3');
        mkdir('vfs2-test://dir4');
        file_put_contents('vfs2-test://dir4/file4.txt', 'file4');

        $commit2 = FileSystem::commit('vfs2-test');
        $this->assertTrue($commit2);

        $this->streamStop('vfs2-test');

        // dir1
        $this->assertFileExists('vfs1-test://root/dir1');
        $this->assertDirectoryExists('vfs1-test://root/dir1');
        // dir1/dir2
        $this->assertFileExists('vfs1-test://root/dir1/dir2');
        $this->assertTrue(is_file('vfs1-test://root/dir1/dir2'));
        $this->assertEquals('file1', file_get_contents('vfs1-test://root/dir1/dir2'));
        // dir1/dir2/file2.txt
        $this->assertFileNotExists('vfs1-test://root/dir1/dir2/file2.txt');
        // dir1/file1.txt
        $this->assertFileNotExists('vfs1-test://root/dir1/file1.txt');
        // file0.txt
        $this->assertFileExists('vfs1-test://root/file0.txt');
        $this->assertTrue(is_file('vfs1-test://root/file0.txt'));
        $this->assertEquals('file0-0', file_get_contents('vfs1-test://root/file0.txt'));
        // dir3
        $this->assertFileNotExists('vfs1-test://root/dir3');
        // dir4
        $this->assertFileExists('vfs1-test://root/dir4');
        $this->assertDirectoryExists('vfs1-test://root/dir4');
        // dir4/file4.txt
        $this->assertFileExists('vfs1-test://root/dir4/file4.txt');
        $this->assertTrue(is_file('vfs1-test://root/dir4/file4.txt'));
        $this->assertEquals('file4', file_get_contents('vfs1-test://root/dir4/file4.txt'));

        $this->streamStop('vfs1-test');
    }

    private function getAllPaths($dir): array
    {
        $iteratorRd = new RecursiveDirectoryIterator($dir);
        $iteratorRi = new RecursiveIteratorIterator(
            $iteratorRd, RecursiveIteratorIterator::SELF_FIRST
        );
        $paths = [];
        foreach ($iteratorRi as $file) {
            /* @var $file \SplFileInfo */
            $pathname = str_replace(
                DIRECTORY_SEPARATOR, '/', $file->getPathname()
            );
            $paths[] = $pathname;
        }

        return $paths;
    }

    private function filesStart($dir = null): void
    {
        $this->streamStart($dir);

        file_put_contents('vfs-test://file1.txt', 'file1');
        mkdir('vfs-test://dir1');
        file_put_contents('vfs-test://dir1/file2.txt', 'file2');
        mkdir('vfs-test://dir1/dir5');
        file_put_contents('vfs-test://dir1/dir5/file5.txt', 'file5');
    }

    private function filesStop(): void
    {
        $this->streamStop();
    }

    private function streamStart($dir = null, $protocol = 'vfs-test'): void
    {
        if (in_array($protocol, stream_get_wrappers())) {
            $this->streamStop();
        }
        FileSystem::register($protocol, $dir);
    }

    private function streamStop($protocol = 'vfs-test'): void
    {
        FileSystem::unregister($protocol);
    }
}
