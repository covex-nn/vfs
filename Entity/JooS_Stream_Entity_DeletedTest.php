<?php

require_once "JooS/Stream/Entity/Deleted.php";

class JooS_Stream_Entity_DeletedTest extends PHPUnit_Framework_TestCase
{

  public function testInstance() {
    require_once "JooS/Stream/Entity.php";
    
    $realEntity = JooS_Stream_Entity::newInstance(__FILE__);
    
    $deletedEntity = JooS_Stream_Entity_Deleted::newInstance($realEntity);
    $this->assertFalse($deletedEntity->file_exists());
    
    $this->assertEquals($realEntity, $deletedEntity->getRealEntity());
  }

}
