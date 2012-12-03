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
    $realDir = $this->_getFsRoot();
    $realFiles0 = $this->_testDirGetFiles($realDir);

    $rfIndexDot = array_search(".", $realFiles0);
    unset($realFiles0[$rfIndexDot]);
    $rfIndexDotDot = array_search("..", $realFiles0);
    unset($realFiles0[$rfIndexDotDot]);
    $realFiles = array_values($realFiles0);

    $streamDir = $this->protocol . "://";
    $streamFiles = $this->_testDirGetFiles($streamDir);

    $this->assertEquals($realFiles, $streamFiles);
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

