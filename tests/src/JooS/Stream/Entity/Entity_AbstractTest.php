<?php

namespace JooS\Stream;

use ReflectionClass;

class Entity_AbstractTest extends \PHPUnit_Framework_TestCase
{

  public function testEntity()
  {
    $rc = new ReflectionClass("JooS\\Stream\\Entity_Abstract");
    $rm = $rc->getMethod("__construct");
    
    $this->assertTrue($rc->isAbstract());
    $this->assertTrue($rm->isProtected());
  }

}

