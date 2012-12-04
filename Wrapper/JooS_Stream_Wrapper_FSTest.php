<?php

require_once "JooS/Stream/Wrapper/FS.php";

class JooS_Stream_Wrapper_FSTest extends PHPUnit_Framework_TestCase
{

  protected $protocol = null;

  public function testRegister()
  {
    $this->assertTrue(
      in_array($this->protocol, stream_get_wrappers())
    );
  }

  public function testStat()
  {
    $realDir = $this->_getFsRoot();
    $realFile = $realDir . "/file1.txt";

    $streamFile1 = $this->protocol . "://file1.txt";
    $this->assertEquals(stat($realFile), stat($streamFile1));

    $streamFile2 = $this->protocol . "://dir_not_exists/file_not_exists.txt";
    $this->assertEquals(false, @stat($streamFile2));

    $streamFile2 = $this->protocol . "://file_not_exists.txt";
    $this->assertEquals(false, @stat($streamFile2));
  }

  public function testMkdir()
  {
    $dir2 = $this->protocol . "://dir2";

    $this->assertFalse(file_exists($dir2));
    mkdir($dir2);

    $this->assertTrue(file_exists($dir2));
    $this->assertTrue(is_dir($dir2));
  }

  public function testDir()
  {
    $streamFiles0 = $this->_testDirGetFiles($this->protocol . "://dir3");
    $this->assertEquals(null, $streamFiles0);

    $streamFiles1 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("file2.txt"), $streamFiles1);

    $mkdir1 = mkdir($this->protocol . "://dir1/dir2");
    $this->assertTrue($mkdir1);

    $streamFiles2 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("dir2", "file2.txt"), $streamFiles2);
    
    $rmdir1 = rmdir($this->protocol . "://dir1/dir2");
    $this->assertTrue($rmdir1);
    
    $streamFiles3 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("file2.txt"), $streamFiles3);
    
    $unlink1 = unlink($this->protocol . "://dir1/file2.txt");
    $this->assertTrue($unlink1);
    
    $streamFiles4 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array(), $streamFiles4);
    
    $rmdir2 = rmdir($this->protocol . "://dir1");
    $this->assertTrue($rmdir2);

    $streamFiles5 = $this->_testDirGetFiles($this->protocol . "://");
    $this->assertEquals(array("file1.txt"), $streamFiles5);
  }
  
  protected function _testDirGetFiles($dir)
  {
    if (is_dir($dir)) {
      $files = array();

      $dh = opendir($dir);
      if ($dh) {
        while (($file = readdir($dh)) !== false) {
          $files[] = $file;
        }

        rewinddir($dh);
        $filesAfterRewind = array();
        while (($file = readdir($dh)) !== false) {
          $filesAfterRewind[] = $file;
        }
        $this->assertEquals($files, $filesAfterRewind);

        closedir($dh);
      }
      sort($files);
    } else {
      $files = null;
    }
    return $files;
  }

  protected function setUp()
  {
    if (is_null($this->protocol)) {
      $this->protocol = $this->_randomValue();
    }

    JooS_Stream_Wrapper_FS::register($this->protocol, $this->_getFsRoot());
  }

  protected function tearDown()
  {
    try {
      JooS_Stream_Wrapper_FS::unregister($this->protocol);
    } catch (JooS_Stream_Wrapper_Exception $e) {
      
    }
  }

  protected function _randomValue($prefix = "stream")
  {
    return uniqid($prefix);
  }

  protected function _getFsRoot()
  {
    return dirname(__FILE__) . "/_root";
  }

}

