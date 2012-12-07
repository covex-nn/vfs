<?php

require_once "JooS/Stream/Entity.php";

require_once "JooS/Stream/Wrapper/FS/Partition.php";

class JooS_Stream_Wrapper_FS_PartitionTest extends PHPUnit_Framework_TestCase
{

  /**
   * @expectedException JooS_Stream_Wrapper_FS_Exception
   */
  public function testWrongRoot1()
  {
    $entity = JooS_Stream_Entity::newInstance(__FILE__);
    $fs = new JooS_Stream_Wrapper_FS_Partition($entity);
  }

  /**
   * @expectedException JooS_Stream_Wrapper_FS_Exception
   */
  public function testWrongRoot2()
  {
    $entity = JooS_Stream_Entity::newInstance(__FILE__ . ".ksdckjsbcajhsc");
    $fs = new JooS_Stream_Wrapper_FS_Partition($entity);
  }

  public function testInstance_justFiles()
  {
    $path = $this->_getRoot();
    $entity = JooS_Stream_Entity::newInstance($path);
    $fs = new JooS_Stream_Wrapper_FS_Partition($entity);

    $root = $fs->getRoot();
    $this->assertEquals($entity, $root);

    $file2Entity = $fs->getEntity("dir1/file2.txt");
    $this->assertTrue($file2Entity instanceof JooS_Stream_Entity);
    $this->assertTrue($file2Entity->file_exists());
    $this->assertTrue($file2Entity->is_file());

    $fileNotExistsEntity = $fs->getEntity("dir1/file_not_exists.txt");
    $this->assertTrue($fileNotExistsEntity instanceof JooS_Stream_Entity);
    $this->assertFalse($fileNotExistsEntity->file_exists());

    $fileNoParentDirEntity = $fs->getEntity("dir_not_exists/file_not_exists.txt");
    $this->assertEquals(null, $fileNoParentDirEntity);
  }

  public function testMakeDirectory()
  {
    $path = $this->_getRoot();
    $entity = JooS_Stream_Entity::newInstance($path);
    $fs = new JooS_Stream_Wrapper_FS_Partition($entity);

    $result1 = @$fs->makeDirectory("dir1", 0777, STREAM_REPORT_ERRORS);
    $this->assertEquals(null, $result1);

    $result2 = $fs->makeDirectory("dir2", 0777, STREAM_REPORT_ERRORS);
    $this->assertTrue($result2 instanceof JooS_Stream_Entity_Virtual);
    $result2Path = $result2->path();
    $this->assertTrue(is_dir($result2Path));

    $entityDir2 = $fs->getEntity("dir2");
    $this->assertEquals($result2->path(), $entityDir2->path());

    $entityDir23 = $fs->getEntity("dir2/dir3");
    $this->assertFalse(is_null($entityDir23));
    $this->assertFalse($entityDir23->file_exists());

    $entityDir234 = $fs->getEntity("dir2/dir3/dir4");
    $this->assertTrue(is_null($entityDir234));

    $entityDir5 = $fs->getEntity("file1.txt/dir5/dir5");
    $this->assertTrue(is_null($entityDir5));

    /** @todo протестировать удаление директории */
    /** @todo протестировать удаление директории и создание там файла */
    unset($fs);
    $this->assertFalse(file_exists($result2Path));
  }

  public function testGetList()
  {
    $path = $this->_getRoot();
    $entity = JooS_Stream_Entity::newInstance($path);
    $fs = new JooS_Stream_Wrapper_FS_Partition($entity);

    $list1 = $fs->getList("dir1");
    $this->_checkFilesList($list1, array("file2.txt"));

    $dir2 = $fs->makeDirectory("dir1/dir2", 0777, 0);

    $list2 = $fs->getList("dir1");
    $this->_checkFilesList($list2, array("dir2", "file2.txt"));

    $dir3 = $fs->makeDirectory("dir1/dir2/dir3", 0777, 0);

    $list3 = $fs->getList("dir1/dir2");
    $this->_checkFilesList($list3, array("dir3"));
    
    $rmdir1 = $fs->removeDirectory("dir1/dir2/dir3", 0);
    $this->assertTrue($rmdir1 instanceof JooS_Stream_Entity_Deleted);
    $list3_2 = $fs->getList("dir1/dir2");
    $this->_checkFilesList($list3_2, array());

    $rmdir2 = $fs->removeDirectory("dir1/dir2", 0);
    $this->assertTrue($rmdir2 instanceof JooS_Stream_Entity_Deleted);
    $list2_2 = $fs->getList("dir1");
    $this->_checkFilesList($list2_2, array("file2.txt"));
    
    $rmdir3 = @$fs->removeDirectory("dir1/file2.txt", STREAM_REPORT_ERRORS);
    $this->assertEquals(null, $rmdir3);

    $rmdir4 = $fs->removeDirectory("dir1", 0);
    $this->assertEquals(null, $rmdir4);

    $rmdir5 = $fs->removeDirectory("dir2", 0);
    $this->assertEquals(null, $rmdir5);

    $list4 = $fs->getList("dir2");
    $this->assertTrue(is_null($list4));
  }
  
  public function testRename() {
    $path = $this->_getRoot();
    $root = JooS_Stream_Entity::newInstance($path);
    $fs = new JooS_Stream_Wrapper_FS_Partition($root);
    
    $rename1 = $fs->rename("file1.txt", "dir1/file3.txt");
    $this->assertTrue($rename1 instanceof JooS_Stream_Entity_Virtual);
    $this->assertEquals("file3.txt", $rename1->basename());

    $list1 = $fs->getList("dir1");
    $this->_checkFilesList($list1, array("file2.txt", "file3.txt"));
    $entity1 = $fs->getEntity("file1.txt");
    $this->assertFalse($entity1->file_exists());
    
    $delete1 = $fs->deleteFile("dir1/file2.txt");
    $this->assertTrue($delete1 instanceof JooS_Stream_Entity_Deleted);
    
    $list2 = $fs->getList("dir1");
    $this->_checkFilesList($list2, array("file3.txt"));
  }

  private function _checkFilesList($list, $files)
  {
    $listData = array();
    $listExpected = array();
    foreach ($files as $name) {
      $listData[$name] = 1;
      $listExpected[$name] = 0;
    }

    $actualFiles = array();
    foreach ($list as $key => $file) {
      $this->assertTrue(is_numeric($key));
      /* $var $file JooS_Stream_Entity_Interface */
      $this->assertTrue($file instanceof JooS_Stream_Entity_Interface);
      $listData[$file->basename()]--;

      $actualFiles[] = $file->basename();
    }
    $this->assertEquals($listExpected, $listData);
    $this->assertEquals($files, $actualFiles);
  }

  private function _getRoot()
  {
    return __DIR__ . "/_root";
  }

}
