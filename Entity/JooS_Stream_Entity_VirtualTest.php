<?php

require_once "JooS/Stream/Entity/Virtual.php";

class JooS_Stream_Entity_VirtualTest extends PHPUnit_Framework_TestCase
{
  
  public function testInstance()
  {
    require_once "JooS/Stream/Entity.php";
    
    $real = JooS_Stream_Entity::newInstance(__FILE__);
    $virtual = JooS_Stream_Entity_Virtual::newInstance($real, "/tmp/qqq");
    
    $this->assertEquals($real->basename(), $virtual->basename());
    $this->assertEquals("/tmp/qqq", $virtual->path());
    $this->assertEquals($real, $virtual->getRealEntity());
  }
  
}
