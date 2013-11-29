<?php

namespace JooS\Stream;

class FilesTest extends \PHPUnit_Framework_TestCase
{

  public function testInstance()
  {
    $files = new Files();
    $this->assertTrue($files instanceof Files);
    
    $dir1 = $files->mkdir();
    $this->assertTrue(file_exists($dir1));
    $this->assertTrue(is_dir($dir1));
    $this->assertTrue(is_writable($dir1));
    
    $file1 = $files->tempnam();
    $this->assertFalse(file_exists($file1));
    $dir2 = dirname($file1);
    $this->assertTrue(is_writable($dir2));
    file_put_contents($file1, "qwerty1");

    $file2 = $files->tempnam();
    file_put_contents($file2, "qwerty2");
    $files->delete($file2);
    $this->assertFalse(file_exists($file2));
    
    unset($files);
    
    clearstatcache();
    $this->assertFalse(file_exists($dir1));
    $this->assertFalse(file_exists($file1));
  }
  
  public function testDeleteSymlink()
  {
    $files = new Files();
    
    $dirStorage = $files->mkdir();
    
    $targetDir = $files->mkdir();
    file_put_contents($targetDir . "/just_a_file", "asdf");
    $targetFile = $files->tempnam();
    file_put_contents($targetFile, "qwerty");
    
    $symlinkDir = $dirStorage . "/linkDir";
    $symlinkFile = $dirStorage . "/linkFile";
    
    symlink($targetDir, $symlinkDir);
    symlink($targetFile, $symlinkFile);
    
    $files->delete($dirStorage);
    
    $this->assertFileNotExists($symlinkDir);
    $this->assertFileExists($targetDir);
    
    $this->assertFileNotExists($symlinkFile);
    $this->assertFileExists($targetFile);
  }

  public function testDeleteBadSymlink()
  {
    $files = new Files();
    
    $dirStorage = $files->mkdir();
    
    $targetDir = $files->mkdir();
    $targetFile = $targetDir . "/qwerty";
    file_put_contents($targetFile, "asdf");
    
    $symlinkDir = $dirStorage . "/linkDir";
    $symlinkFile = $dirStorage . "/linkFile";
    
    symlink($targetDir, $symlinkDir);
    symlink($targetFile, $symlinkFile);
    
    $files->delete($targetDir);

    $files->delete($symlinkDir);
    $files->delete($symlinkFile);
    
    $this->assertFileNotExists($symlinkDir);
    $this->assertFileNotExists($symlinkFile);
  }
  
}
