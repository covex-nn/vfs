<?php

require_once "JooS/Stream/Wrapper/FS.php";

class JooS_Stream_Wrapper_FSTest extends PHPUnit_Framework_TestCase
{

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
  
  public function testRegister1()
  {
    $this->_streamStart(__DIR__);
    $this->assertTrue(in_array("joos-test", stream_get_wrappers()));

    $this->assertEquals(
      file_get_contents(__FILE__), file_get_contents("joos-test://" . basename(__FILE__))
    );

    $this->_streamStop();
    $this->assertFalse(in_array("joos-test", stream_get_wrappers()));
  }

  public function testFiles()
  {
    $this->_filesStart();

    $this->assertTrue(is_file("joos-test://file1.txt"));
    $this->assertEquals("file1", file_get_contents("joos-test://file1.txt"));
    $this->assertTrue(is_dir("joos-test://dir1"));
    $this->assertTrue(is_file("joos-test://dir1/file2.txt"));
    $this->assertEquals("file2", file_get_contents("joos-test://dir1/file2.txt"));
    $this->assertTrue(is_dir("joos-test://dir1/dir5"));
    $this->assertTrue(is_file("joos-test://dir1/dir5/file5.txt"));
    $this->assertEquals("file5", file_get_contents("joos-test://dir1/dir5/file5.txt"));

    $this->assertFalse(file_exists("joos-test://dir1/dir3/file_not_exists.txt"));
    $this->assertFalse(file_exists("joos-test://dir1/dir5/file_not_exists.txt"));

    $fp1 = @fopen("joos-test://dir1", "r");
    $this->assertFalse($fp1);

    $fp2 = @fopen("joos-test://dir1/dir2/dir3/file_not_exists", "r");
    $this->assertFalse($fp2);

    $od1 = @opendir("joos-test://file1.txt");
    $this->assertFalse($od1);

    $this->_filesStop();
  }

  public function testUnlinkRmdir()
  {
    $this->_filesStart();

    $stat1 = stat("joos-test://dir1/dir5");
    $this->assertTrue(is_array($stat1));

    $mkdir1 = @mkdir("joos-test://dir1/dir5");
    $this->assertFalse($mkdir1);

    $rmdir1 = @rmdir("joos-test://dir1/dir5");
    $this->assertFalse($rmdir1);

    $rmdir2 = @rmdir("joos-test://dir1/dir5/file5.txt");
    $this->assertFalse($rmdir2);

    $unlink1 = @unlink("joos-test://dir1/dir5");
    $this->assertFalse($unlink1);

    $unlink2 = @unlink("joos-test://dir1/dir5/file_not_exists.txt");
    $this->assertFalse($unlink2);

    $unlink3 = @unlink("joos-test://dir1/dir_not_exists/file_not_exists.txt");
    $this->assertFalse($unlink3);

    $unlink4 = unlink("joos-test://dir1/dir5/file5.txt");
    $this->assertTrue($unlink4);

    $rmdir3 = rmdir("joos-test://dir1/dir5");
    $this->assertTrue($rmdir3);

    $this->assertFalse(file_exists("joos-test://dir1/dir5/file5.txt"));
    $this->assertFalse(file_exists("joos-test://dir1/dir5"));

    $this->_filesStop();
  }

  public function testRename()
  {
    $this->_filesStart();

    $rename1 = @rename("joos-test://file1.txt", "joos-test://dir1");
    $this->assertFalse($rename1);

    $rename2 = @rename("joos-test://file2.txt", "joos-test://file3.txt");
    $this->assertFalse($rename2);

    $rename3 = rename("joos-test://file1.txt", "joos-test://file2.txt");
    $this->assertTrue($rename3);
    $this->assertFalse(file_exists("joos-test://file1.txt"));
    $this->assertTrue(file_exists("joos-test://file2.txt"));
    $this->assertEquals("file1", file_get_contents("joos-test://file2.txt"));

    $rename4 = rename("joos-test://dir1", "joos-test://dir2");
    $this->assertTrue($rename4);
    $this->assertEquals("file5", file_get_contents("joos-test://dir2/dir5/file5.txt"));
    $this->assertFalse(file_exists("joos-test://dir1/dir5/file5.txt"));

    mkdir("joos-test://dir1");
    file_put_contents("joos-test://dir1/file7.txt", "file7");
    rename("joos-test://dir1", "joos-test://dir2/dir5/dir7");
    $this->assertEquals("file7", file_get_contents("joos-test://dir2/dir5/dir7/file7.txt"));

    $this->_filesStop();
  }

  public function testRealFS()
  {
    $this->_streamStart(__DIR__ . "/FS_realdir");

    $this->assertTrue(file_exists("joos-test://dir1/dir2/dir3/file3.txt"));
    $this->assertFalse(file_exists("joos-test://dir1/dir2/dir3/dir4/file4.txt"));

    $paths1 = $this->_getAllPaths("joos-test://dir1");
    $expectedPaths1 = array(
      "joos-test://dir1/dir2",
      "joos-test://dir1/dir2/dir3",
      "joos-test://dir1/dir2/dir3/file3.txt",
      "joos-test://dir1/dir2/file2.txt",
      "joos-test://dir1/file1.txt",
    );
    $this->assertEquals($expectedPaths1, $paths1);

    unlink("joos-test://dir1/dir2/dir3/file3.txt");
    rmdir("joos-test://dir1/dir2/dir3");

    $paths2 = $this->_getAllPaths("joos-test://dir1");
    $expectedPaths2 = array(
      "joos-test://dir1/dir2",
      "joos-test://dir1/dir2/file2.txt",
      "joos-test://dir1/file1.txt",
    );
    $this->assertEquals($expectedPaths2, $paths2);

    $this->assertTrue(file_exists(__DIR__ . "/FS_realdir/dir1/dir2/dir3/file3.txt"));
    $this->assertTrue(file_exists(__DIR__ . "/FS_realdir/dir1/dir2/dir3"));

    $fp1 = @fopen("joos-test://dir1/file1.txt", "x+");
    $this->assertFalse($fp1);

    $this->_streamStop();
  }
  
  public function testCommit()
  {
    $commit1 = JooS_Stream_Wrapper_FS::commit("joos-test-not-a-protocol");
    $this->assertFalse($commit1);
    
    $this->_streamStart(null, "joos1-test");
    mkdir("joos1-test://root");
    file_put_contents("joos1-test://root/file0.txt", "file0");
    mkdir("joos1-test://root/dir1");
    file_put_contents("joos1-test://root/dir1/file1.txt", "file1");
    mkdir("joos1-test://root/dir1/dir2");
    file_put_contents("joos1-test://root/dir1/dir2/file2.txt", "file2");
    mkdir("joos1-test://root/dir3");
    
    /**
     * dir1
     * dir1/dir2
     * dir1/dir2/file2.txt (file2)
     * dir1/file1.txt (file1)
     * dir3
     * file0.txt (file0)
     */
    $this->_streamStart("joos1-test://root", "joos2-test");
    
    file_put_contents("joos2-test://file0-0.txt", "file0-0");
    unlink("joos2-test://file0-0.txt");
    file_put_contents("joos2-test://file0.txt", "file0-0");
    unlink("joos2-test://dir1/dir2/file2.txt");
    rmdir("joos2-test://dir1/dir2");
    rename("joos2-test://dir1/file1.txt", "joos2-test://dir1/dir2");
    rmdir("joos2-test://dir3");
    mkdir("joos2-test://dir4");
    file_put_contents("joos2-test://dir4/file4.txt", "file4");

    $commit2 = JooS_Stream_Wrapper_FS::commit("joos2-test");
    $this->assertTrue($commit2);
    
    $this->_streamStop("joos2-test");
    
    // dir1
    $this->assertTrue(file_exists("joos1-test://root/dir1"));
    $this->assertTrue(is_dir("joos1-test://root/dir1"));
    // dir1/dir2
    $this->assertTrue(file_exists("joos1-test://root/dir1/dir2"));
    $this->assertTrue(is_file("joos1-test://root/dir1/dir2"));
    $this->assertEquals("file1", file_get_contents("joos1-test://root/dir1/dir2"));
    // dir1/dir2/file2.txt
    $this->assertFalse(file_exists("joos1-test://root/dir1/dir2/file2.txt"));
    // dir1/file1.txt
    $this->assertFalse(file_exists("joos1-test://root/dir1/file1.txt"));
    // file0.txt
    $this->assertTrue(file_exists("joos1-test://root/file0.txt"));
    $this->assertTrue(is_file("joos1-test://root/file0.txt"));
    $this->assertEquals("file0-0", file_get_contents("joos1-test://root/file0.txt"));
    // dir3
    $this->assertFalse(file_exists("joos1-test://root/dir3"));
    // dir4
    $this->assertTrue(file_exists("joos1-test://root/dir4"));
    $this->assertTrue(is_dir("joos1-test://root/dir4"));
    // dir4/file4.txt
    $this->assertTrue(file_exists("joos1-test://root/dir4/file4.txt"));
    $this->assertTrue(is_file("joos1-test://root/dir4/file4.txt"));
    $this->assertEquals("file4", file_get_contents("joos1-test://root/dir4/file4.txt"));
    
    $this->_streamStop("joos1-test");
  }

  protected function _getAllPaths($dir)
  {
    $iteratorRd = new RecursiveDirectoryIterator($dir);
    $iteratorRi = new RecursiveIteratorIterator(
        $iteratorRd, RecursiveIteratorIterator::SELF_FIRST
    );
    $paths = array();
    foreach ($iteratorRi as $file) {
      /* @var $file SplFileInfo */
      $pathname = str_replace(
        DIRECTORY_SEPARATOR, "/", $file->getPathname()
      );
      $paths[] = $pathname;
    }
    return $paths;
  }

  protected function _filesStart($dir = null)
  {
    $this->_streamStart($dir);

    file_put_contents("joos-test://file1.txt", "file1");
    mkdir("joos-test://dir1");
    file_put_contents("joos-test://dir1/file2.txt", "file2");
    mkdir("joos-test://dir1/dir5");
    file_put_contents("joos-test://dir1/dir5/file5.txt", "file5");
  }

  protected function _filesStop()
  {
    $this->_streamStop();
  }

  protected function _streamStart($dir = null, $protocol = "joos-test")
  {
    if (in_array($protocol, stream_get_wrappers())) {
      $this->_streamStop();
    }
    JooS_Stream_Wrapper_FS::register($protocol, $dir);
  }

  protected function _streamStop($protocol = "joos-test")
  {
    JooS_Stream_Wrapper_FS::unregister($protocol);
  }

}
