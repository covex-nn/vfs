<?php

require_once "JooS/Stream/Entity/Abstract.php";

class JooS_Stream_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{

  public function testEntity()
  {
    $rc = new ReflectionClass("JooS_Stream_Entity_Abstract");
    $rm = $rc->getMethod("__construct");
    
    $this->assertTrue($rc->isAbstract());
    $this->assertTrue($rm->isProtected());
  }

}

