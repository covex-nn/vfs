<?php

namespace JooS\Stream;

class WrapperTest extends \PHPUnit_Framework_TestCase
{

  protected $protocol = null;

  public function testRegister()
  {
    $exceptionsCounter = 0;

    $result2 = Wrapper::unregister($this->protocol);
    $this->assertTrue($result2);

    try {
      Wrapper::unregister($this->protocol);
    } catch (Wrapper_Exception $e) {
      $exceptionsCounter++;
    }
    $this->assertEquals(1, $exceptionsCounter, "Wrapper must not be registered already");

    $result1 = Wrapper::register($this->protocol);
    $this->assertTrue($result1);

    try {
      Wrapper::register($this->protocol);
    } catch (Wrapper_Exception $e) {
      $exceptionsCounter++;
    }
    $this->assertEquals(2, $exceptionsCounter, "Second wrapper could not be registered");

    $this->assertTrue(
      in_array($this->protocol, stream_get_wrappers())
    );
  }

  protected function setUp()
  {
    if (is_null($this->protocol)) {
      $this->protocol = uniqid("stream");
    }

    Wrapper::register($this->protocol);
  }

  protected function tearDown()
  {
    Wrapper::unregister($this->protocol);
  }

}
