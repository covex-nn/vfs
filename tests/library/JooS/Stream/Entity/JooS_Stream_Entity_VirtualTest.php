<?php

namespace JooS\Stream;

require_once "JooS/Stream/Entity/Virtual.php";

class Entity_VirtualTest extends \PHPUnit_Framework_TestCase
{
  
  public function testInstance()
  {
    require_once "JooS/Stream/Entity.php";
    
    $real = Entity::newInstance(__FILE__);
    $virtual = Entity_Virtual::newInstance($real, "/tmp/qqq");
    
    $this->assertEquals($real->basename(), $virtual->basename());
    $this->assertEquals("/tmp/qqq", $virtual->path());
    $this->assertEquals($real, $virtual->getRealEntity());
  }
  
}
