<?php

namespace JooS\Stream;

class Entity_DeletedTest extends \PHPUnit_Framework_TestCase
{

  public function testInstance() {
    $realEntity = Entity::newInstance(__FILE__);
    
    $deletedEntity = Entity_Deleted::newInstance($realEntity);
    $this->assertFalse($deletedEntity->file_exists());
    
    $this->assertEquals($realEntity, $deletedEntity->getRealEntity());
  }

}
