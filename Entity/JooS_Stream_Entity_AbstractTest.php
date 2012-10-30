<?php

  require_once "JooS/Stream/Entity/PHPUnit/TestingFile.php";

  class JooS_Stream_Entity_AbstractTest extends PHPUnit_Framework_TestCase {

    public function testEntity() {
      $entity = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance("qqq/www.eee");

      $this->assertEquals("qqq/www.eee", $entity->path());
      $this->assertEquals("www.eee", $entity->basename());
      $this->assertEquals("qqq", $entity->dirname());

      $this->assertTrue(!$entity->is_virtual());
    }

  }

  