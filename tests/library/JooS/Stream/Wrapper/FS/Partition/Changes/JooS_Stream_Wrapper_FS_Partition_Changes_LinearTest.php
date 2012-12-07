<?php

require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Linear.php";

class JooS_Stream_Wrapper_FS_Partition_Changes_LinearTest extends PHPUnit_Framework_TestCase
{

  public function testInterface()
  {
    $changes = new JooS_Stream_Wrapper_FS_Partition_Changes_Linear();

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());

    require_once "JooS/Stream/Entity.php";

    $entity = JooS_Stream_Entity::newInstance(__FILE__);

    $changes->add("qqq/www/eee", $entity);

    $this->assertTrue($changes->exists("qqq/www/eee"));
    $this->assertEquals($entity, $changes->get("qqq/www/eee"));
    $this->assertEquals(1, $changes->count());

    $this->assertTrue($changes->delete("qqq/www/eee"));

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());
  }

}
