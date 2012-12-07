<?php

require_once "JooS/Stream/Entity.php";

class JooS_Stream_EntityTest extends PHPUnit_Framework_TestCase
{

  public function testInstance()
  {
    $instance = JooS_Stream_Entity::newInstance(__FILE__);

    $this->assertEquals(basename(__FILE__), $instance->basename());

    $unixPathToFile = str_replace("\\", "/", __FILE__);
    $this->assertEquals($unixPathToFile, $instance->path());

    $this->assertEquals(is_writable(__FILE__), $instance->is_writable());
    $this->assertEquals(is_readable(__FILE__), $instance->is_readable());

    $this->assertFalse($instance->is_dir());
    $this->assertTrue($instance->is_file());
    $this->assertTrue($instance->file_exists());
  }

}
