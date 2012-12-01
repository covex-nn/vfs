<?php

require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Tree.php";

class JooS_Stream_Wrapper_FS_Partition_Changes_TreeTest extends PHPUnit_Framework_TestCase
{

  public function testInterface()
  {
    $changes = new JooS_Stream_Wrapper_FS_Partition_Changes_Tree();

    $this->assertFalse($changes->exists("qqq/www"));
    $this->assertEquals(null, $changes->get("qqq/www"));
    $this->assertFalse($changes->delete("qqq/www"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());
    
    require_once "JooS/Stream/Entity.php";

    $entity = JooS_Stream_Entity::newInstance(__FILE__);

    require_once "JooS/Stream/Storage.php";

    $storage = new JooS_Stream_Storage($entity);

    $changes->add("qqq/www", $storage);

    $this->assertTrue($changes->exists("qqq/www"));
    $this->assertEquals($storage, $changes->get("qqq/www"));
    $this->assertEquals(1, $changes->count());
    $this->assertEquals(array($storage), $changes->children());
    $this->assertEquals(array($storage), $changes->children("qqq"));
    $this->assertEquals(array(), $changes->children("qqq/www"));

    $this->assertTrue($changes->delete("qqq/www"));

    $this->assertFalse($changes->exists("qqq/www"));
    $this->assertEquals(null, $changes->get("qqq/www"));
    $this->assertFalse($changes->delete("qqq/www"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());
  }

}
