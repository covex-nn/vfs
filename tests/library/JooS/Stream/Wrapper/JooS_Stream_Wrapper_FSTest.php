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

  /**
   * @dataProvider providerGetRelativePath
   */
  public function testGetRelativePath($url)
  {
    $this->assertEquals("dir1/dir2", JooS_Stream_Wrapper_FS::getRelativePath($url));
  }

  public function providerGetRelativePath()
  {
    return array(
      array('test://dir1/dir2/'),
      array('test:///dir1//dir2'),
      array('test://\dir1\dir2'),
      array('test://\\dir1\\dir2'),
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
    $notDirHandler = @opendir($this->protocol . "://file1.txt");
    $this->assertFalse($notDirHandler);

    $streamFiles0 = $this->_testDirGetFiles($this->protocol . "://dir3");
    $this->assertEquals(null, $streamFiles0);

    $streamFiles1 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("dir5", "file2.txt"), $streamFiles1);

    $mkdir1 = mkdir($this->protocol . "://dir1/dir2");
    $this->assertTrue($mkdir1);

    $streamFiles2 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("dir2", "dir5", "file2.txt"), $streamFiles2);

    $rmdir1 = rmdir($this->protocol . "://dir1/dir2");
    $this->assertTrue($rmdir1);

    $streamFiles3 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array("dir5", "file2.txt"), $streamFiles3);

    $unlink1 = unlink($this->protocol . "://dir1/file2.txt");
    $this->assertTrue($unlink1);
    $unlink2 = unlink($this->protocol . "://dir1/dir5/file5.txt");
    $this->assertTrue($unlink2);
    $rmdir5 = rmdir($this->protocol . "://dir1/dir5");
    $this->assertTrue($rmdir5);

    $streamFiles4 = $this->_testDirGetFiles($this->protocol . "://dir1");
    $this->assertEquals(array(), $streamFiles4);

    $rmdir2 = rmdir($this->protocol . "://dir1");
    $this->assertTrue($rmdir2);

    $streamFiles5 = $this->_testDirGetFiles($this->protocol . "://");
    $this->assertEquals(array("file1.txt"), $streamFiles5);
  }

  public function testFileOperations()
  {
    $filename0 = $this->protocol . "://file1.txt";

    $rename2 = rename($this->protocol . "://dir1/dir2/file_not_exists.txt", $this->protocol . "://file2");
    $this->assertFalse($rename2);

    $rename3 = rename($this->protocol . "://dir1/file2.txt", $this->protocol . "://file1.txt");
    $this->assertFalse($rename3);

    $unlink1 = unlink($this->protocol . "://file1_not_exists.txt");
    $this->assertFalse($unlink1);

    $unlink2 = unlink($this->protocol . "://dir1/dir_not_exists/file1_not_exists.txt");
    $this->assertFalse($unlink2);

    $this->assertEquals("file1", file_get_contents($filename0));
    file_put_contents($filename0, "qwerty");
    $this->assertEquals("qwerty", file_get_contents($filename0));

    $filename1 = $this->protocol . "://file1_renamed.txt";
    $rename1 = rename($filename0, $filename1);
    $this->assertTrue($rename1);
    $this->assertEquals("qwerty", file_get_contents($filename1));

    $realFilename = $this->_getFsRoot() . "/file1.txt";
    $this->assertEquals("file1", file_get_contents($realFilename));
  }

  public function testDirRename()
  {
    $dirname0 = $this->protocol . "://dir1";
    $dirname1 = $this->protocol . "://dir1_renamed";

    $not_exists = @file_get_contents($this->protocol . "://dir1/dir_not_exists/file");
    $this->assertFalse($not_exists);

    $this->assertTrue(file_exists($dirname0));
    $this->assertFalse(file_exists($dirname1));
    $this->assertTrue(is_dir($dirname0));

    $rename1 = rename($dirname0, $dirname1);
    $this->assertTrue($rename1);

    $this->assertFalse(file_exists($dirname0));
    $this->assertTrue(file_exists($dirname1));
    $this->assertTrue(is_dir($dirname1));

    $file2 = $this->protocol . "://dir1_renamed/file2.txt";
    $file5 = $this->protocol . "://dir1_renamed/dir5/file5.txt";

    $this->assertEquals("file2", file_get_contents($file2));
    $this->assertEquals("file5", file_get_contents($file5));

    $root = $this->_getFsRoot();
    $this->assertTrue(file_exists($root . "/dir1"));
    $this->assertFalse(file_exists($root . "/dir1_renamed"));
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

