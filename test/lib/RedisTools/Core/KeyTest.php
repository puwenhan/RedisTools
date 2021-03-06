<?php

namespace RedisTools\Core;

/**
 * Test class for Key.
 * Generated by PHPUnit on 2011-04-04 at 23:57:19.
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var Key
	 */
	protected $object;

	protected $testKey = 'key';
	
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Key( $this->testKey );
	}

	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testGetKeyWithInvalidStringValue()
	{
		$this->object->setKey('asdf ? asdf');
		$this->object->getKey();
	}


	public function testGetKeyWithNumericStringValue()
	{
		$this->object->setKey('1234');
		$this->assertEquals('1234', $this->object->getKey());
	}

	
	public function testSetKey()
	{
		$this->object->setKey('asdf');
		$this->assertEquals('asdf', $this->object->getKey());
	}
	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testGetKeyWithNullValue()
	{
		$object = new Key();
		$object->getKey();
	}
	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testGetKeyWithIntValue()
	{
		$this->object->setKey(1234);
		$this->object->getKey();
	}

}

?>
