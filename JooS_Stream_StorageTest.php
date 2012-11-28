<?php

require_once "JooS/Stream/Storage.php";

require_once "JooS/Stream/Entity.php";
    
class JooS_Stream_StorageTest extends PHPUnit_Framework_TestCase
{

  public function testInstance()
  {
    $contentFile = JooS_Stream_Entity::newInstance(__FILE__);
    $storage = new JooS_Stream_Storage($contentFile);
    
    $this->assertEquals($contentFile, $storage->content());
    
    $contentDir = JooS_Stream_Entity::newInstance(__DIR__);
    $storage->setContent($contentDir);

    $this->assertEquals($contentDir, $storage->content());
  }

}

