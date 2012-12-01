<?php

require_once "JooS/Stream/Storage.php";

require_once "JooS/Stream/Entity.php";

class JooS_Stream_StorageTest extends PHPUnit_Framework_TestCase
{

  public function testInstance()
  {
    $entityFile = JooS_Stream_Entity::newInstance(__FILE__);
    $storage = new JooS_Stream_Storage($entityFile);

    $this->assertEquals($entityFile, $storage->entity());

    $entityDir = JooS_Stream_Entity::newInstance(__DIR__);
    $storage->setEntity($entityDir);

    $this->assertEquals($entityDir, $storage->entity());
  }

}

