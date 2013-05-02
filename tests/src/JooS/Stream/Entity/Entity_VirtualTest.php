<?php

namespace JooS\Stream;

class Entity_VirtualTest extends \PHPUnit_Framework_TestCase
{
  
  public function testInstance()
  {
    $real = Entity::newInstance(__FILE__);
    $virtual = Entity_Virtual::newInstance($real, "/tmp/qqq");
    
    $this->assertEquals($real->basename(), $virtual->basename());
    $this->assertEquals("/tmp/qqq", $virtual->path());
    $this->assertEquals($real, $virtual->getRealEntity());
  }
  
}
