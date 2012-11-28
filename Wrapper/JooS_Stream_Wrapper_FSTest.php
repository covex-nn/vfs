<?php

require_once "JooS/Stream/Wrapper/FS.php";

class JooS_Stream_Wrapper_FSTest extends PHPUnit_Framework_TestCase
{

  protected $protocol = null;
  
  public function testRegister() {
    $this->assertTrue(
      in_array($this->protocol, stream_get_wrappers())
    );
  }
  
  public function testSetup() {
    $counter = 0;
    $name = $this->_randomValue();
    JooS_Stream_Wrapper_FS::fsSetup($name, __DIR__);
    
    try {
      JooS_Stream_Wrapper_FS::fsSetup($name, __DIR__);
    }
    catch (JooS_Stream_Wrapper_FS_Exception $e) {
      $counter++;
    }
    $this->assertEquals(1, $counter, "Exception counter == 1");
    
    $this->assertTrue(JooS_Stream_Wrapper_FS::fsExists($name));
    JooS_Stream_Wrapper_FS::fsClear($name);
    $this->assertFalse(JooS_Stream_Wrapper_FS::fsExists($name));
    
    try {
      JooS_Stream_Wrapper_FS::fsSetup($name, __FILE__);
    }
    catch (JooS_Stream_Wrapper_FS_Exception $e) {
      $counter++;
    }
    $this->assertEquals(2, $counter, "Exception counter == 2");
    
    $this->assertFalse(JooS_Stream_Wrapper_FS::fsExists($name));
  }
  
  public function testDir() {
    $fsname = $this->_getFsRoot();
    
    $realDir = $this->_getFsRoot();
    $realFiles0 = $this->_testDirGetFiles($realDir);
    
    $rfIndex = array_search("..", $realFiles0);
    unset($realFiles0[$rfIndex]);
    $realFiles = array_values($realFiles0);

    JooS_Stream_Wrapper_FS::fsSetup($fsname, $realDir);
    
    $streamDir = $this->protocol . "://";
    $streamFiles = $this->_testDirGetFiles($streamDir);
    
    JooS_Stream_Wrapper_Fs::fsClear($fsname);
    
    $this->assertEquals($realFiles, $streamFiles);
  }
  
  protected function _testDirGetFiles($dir) {
    if (is_dir($dir)) {
      $files = array();
      
      $dh = opendir($dir);
      if ($dh) {
        while (($file = readdir($dh)) !== false) {
          $files[] = $file;
        }
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
    if (is_null($this->protocol))
    {
      $this->protocol = $this->_randomValue();
    }

    JooS_Stream_Wrapper_FS::register($this->protocol);
  }

  protected function tearDown()
  {
    try {
      JooS_Stream_Wrapper_FS::unregister($this->protocol);
    }
    catch (JooS_Stream_Wrapper_Exception $e) {
    }
  }
  
  protected function _randomValue() {
    return uniqid("stream");
  }
  
  protected function _getFsRoot() {
    return dirname(__FILE__) . "/_root";
  }

}

