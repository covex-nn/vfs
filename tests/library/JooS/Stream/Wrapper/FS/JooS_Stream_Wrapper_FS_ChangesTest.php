<?php

require_once "JooS/Stream/Wrapper/FS/Changes.php";

class JooS_Stream_Wrapper_FS_ChangesTest extends PHPUnit_Framework_TestCase
{

  public function testInterface()
  {
    $changes = new JooS_Stream_Wrapper_FS_Changes();

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());

    require_once "JooS/Stream/Entity.php";

    $entity = JooS_Stream_Entity::newInstance(__FILE__);

    $changes->add("qqq/www/eee", $entity);
    
    $sublists1 = $changes->sublists();
    $this->assertEquals(1, sizeof($sublists1["qqq"]));
    $this->assertTrue(isset($sublists1["qqq"]));
    $this->assertTrue($sublists1["qqq"] instanceof JooS_Stream_Wrapper_FS_Changes);

    $this->assertTrue($changes->exists("qqq/www/eee"));
    $this->assertEquals($entity, $changes->get("qqq/www/eee"));
    $this->assertEquals(1, $changes->count());

    $expectedChildren = array(
      "qqq/www/eee" => $entity
    );
    $this->assertEquals($expectedChildren, $changes->children());
    $this->assertEquals($expectedChildren, $changes->children("qqq"));
    $this->assertEquals($expectedChildren, $changes->children("qqq/www"));
    $this->assertEquals(array(), $changes->children("qqq/www/eee"));

    $this->assertEquals(array(), $changes->own());
    $this->assertEquals(array(), $changes->own("qqq"));
    $this->assertEquals(array("qqq/www/eee" => $entity), $changes->own("qqq/www"));
    $this->assertEquals(array(), $changes->own("qqq/www/eee"));

    $this->assertTrue($changes->delete("qqq/www/eee"));

    $this->assertFalse($changes->exists("qqq/www/eee"));
    $this->assertEquals(null, $changes->get("qqq/www/eee"));
    $this->assertFalse($changes->delete("qqq/www/eee"));
    $this->assertEquals(0, $changes->count());
    $this->assertEquals(array(), $changes->children());
  }

}
