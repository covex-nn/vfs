<?php

require_once "JooS/Stream/Entity/PHPUnit/TestingFile.php";

class JooS_Stream_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{

  public function testEntity()
  {
    $entity = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance("www.eee", __FILE__);

    $this->assertEquals(__FILE__, $entity->path());
    $this->assertEquals("www.eee", $entity->basename());
  }

}

