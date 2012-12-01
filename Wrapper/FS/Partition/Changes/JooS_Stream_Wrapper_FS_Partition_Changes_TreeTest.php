<?php

require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Tree.php";

class JooS_Stream_Wrapper_FS_Partition_Changes_TreeTest extends PHPUnit_Framework_TestCase
{

  public function testInterface()
  {
    $changes = new JooS_Stream_Wrapper_FS_Partition_Changes_Tree();

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());

    require_once "JooS/Stream/Entity.php";

    $entity = JooS_Stream_Entity::newInstance(__FILE__);

    require_once "JooS/Stream/Storage.php";

    $storage = new JooS_Stream_Storage($entity);

    $changes->add("qqq/www/eee", $storage);

    $this->assertTrue($changes->exists("qqq/www/eee"));
    $this->assertEquals($storage, $changes->get("qqq/www/eee"));
    $this->assertEquals(1, $changes->count());
    
    $expectedChildren = array(
      "qqq/www/eee" => $storage
    );
    $this->assertEquals($expectedChildren, $changes->children());
    $this->assertEquals($expectedChildren, $changes->children("qqq"));
    $this->assertEquals($expectedChildren, $changes->children("qqq/www"));
    $this->assertEquals(array(), $changes->children("qqq/www/eee"));

    $this->assertTrue($changes->delete("qqq/www/eee"));

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());
  }

}
