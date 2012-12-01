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
    $this->assertEquals($entity, $root->entity());

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

  private function _getRoot()
  {
    return __DIR__ . "/_root";
  }

}
