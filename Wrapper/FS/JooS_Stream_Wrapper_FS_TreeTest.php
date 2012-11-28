<?php

require_once "JooS/Stream/Entity.php";

require_once "JooS/Stream/Wrapper/FS/Tree.php";

class JooS_Stream_Wrapper_FS_TreeTest extends PHPUnit_Framework_TestCase
{

  /**
   * @expectedException JooS_Stream_Wrapper_FS_Exception
   */
  public function testWrongRoot1() {
    $entity = JooS_Stream_Entity::newInstance(__FILE__);
    $fs = new JooS_Stream_Wrapper_FS_Tree($entity);
  }
  
  /**
   * @expectedException JooS_Stream_Wrapper_FS_Exception
   */
  public function testWrongRoot2() {
    $entity = JooS_Stream_Entity::newInstance(__FILE__ . ".ksdckjsbcajhsc");
    $fs = new JooS_Stream_Wrapper_FS_Tree($entity);
  }
  
  public function testInstance_justFiles() {
    $path = $this->_getRoot();
    $content = JooS_Stream_Entity::newInstance($path);
    $fs = new JooS_Stream_Wrapper_FS_Tree($content);
    
    $root = $fs->getRoot();
    $this->assertEquals($content, $root->content());
    
    $file2Content = $fs->getContent("dir1/file2.txt");
    $this->assertTrue($file2Content instanceof JooS_Stream_Entity);
    $this->assertTrue($file2Content->file_exists());
    $this->assertTrue($file2Content->is_file());
    
    $fileNotExistsContent = $fs->getContent("dir1/file_not_exists.txt");
    $this->assertTrue($fileNotExistsContent instanceof JooS_Stream_Entity);
    $this->assertFalse($fileNotExistsContent->file_exists());
    
    $fileNoParentDirContent = $fs->getContent("dir_not_exists/file_not_exists.txt");
    $this->assertEquals(null, $fileNoParentDirContent);
    
  }
  
  private function _getRoot() {
    return __DIR__ . "/_root";
  }
}
