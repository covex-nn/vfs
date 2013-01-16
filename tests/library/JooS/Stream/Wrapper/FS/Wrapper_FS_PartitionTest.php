<?php

namespace JooS\Stream;

require_once "JooS/Stream/Entity.php";

require_once "JooS/Stream/Wrapper/FS/Partition.php";

class Wrapper_FS_PartitionTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @expectedException JooS\Stream\Wrapper_FS_Exception
   */
  public function testWrongRoot1()
  {
    $entity = Entity::newInstance(__FILE__);
    $fs = new Wrapper_FS_Partition($entity);
  }

  /**
   * @expectedException JooS\Stream\Wrapper_FS_Exception
   */
  public function testWrongRoot2()
  {
    $entity = Entity::newInstance(__FILE__ . ".ksdckjsbcajhsc");
    $fs = new Wrapper_FS_Partition($entity);
  }

  public function testInstance()
  {
    $fs = new Wrapper_FS_Partition();
    
    $root = $fs->getRoot();
    $this->assertTrue($root->file_exists());
    $this->assertTrue($root->is_dir());
  }
}
