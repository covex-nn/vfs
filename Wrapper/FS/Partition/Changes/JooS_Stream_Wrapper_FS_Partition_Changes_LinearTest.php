<?php

require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Linear.php";

class JooS_Stream_Wrapper_FS_Partition_Changes_LinearTest extends PHPUnit_Framework_TestCase
{

  public function testInterface() {
    $changes = new JooS_Stream_Wrapper_FS_Partition_Changes_Linear();
    
    $this->assertFalse($changes->exists("qqq"));
    $this->assertEquals(null, $changes->get("qqq"));
    $this->assertFalse($changes->delete("qqq"));
    $this->assertEquals(0, $changes->count());
    
    require_once "JooS/Stream/Entity.php";
    
    $entity = JooS_Stream_Entity::newInstance(__FILE__);

    require_once "JooS/Stream/Storage.php";
    
    $storage = new JooS_Stream_Storage($entity);
    
    $changes->add("qqq", $storage);
    
    $this->assertTrue($changes->exists("qqq"));
    $this->assertEquals($storage, $changes->get("qqq"));
    $this->assertEquals(1, $changes->count());
    
    $this->assertTrue($changes->delete("qqq"));
    
    $this->assertFalse($changes->exists("qqq"));
    $this->assertEquals(null, $changes->get("qqq"));
    $this->assertFalse($changes->delete("qqq"));
    $this->assertEquals(0, $changes->count());
  }

}
