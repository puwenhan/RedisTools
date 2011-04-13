<?php

namespace RedisTools\Utils;

/**
 * Test class for Queue.
 * Generated by PHPUnit on 2011-04-04 at 23:47:48.
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var Queue
	 */
	protected $object;

	protected $testKey = 'key';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$redis = new \Redis();
		$redis->pconnect('127.0.0.1');
		$list = new \RedisTools\Type\ArrayList($this->testKey, $redis);
		
		$this->object = new Queue();
		$this->object->setArrayList($list);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->object->getArrayList()->delete();
		$this->object = null;
	}

	public function testGetArrayList()
	{
		$this->assertType(
			'\RedisTools\Type\ArrayList', 
			$this->object->getArrayList()
		);
	}
	
	public function testGetArrayListInternalInstance()
	{
		$object = new Queue('asdf');
		$this->assertType(
			'\RedisTools\Type\ArrayList', 
			$object->getArrayList()
		);
	}

	public function testSetArrayList()
	{
		$arrayList = new \RedisTools\Type\ArrayList();
		$object = new Queue();
		$object->setArrayList($arrayList);
		$this->assertEquals($arrayList, $object->getArrayList());
	}

	public function testAddMessageObject()
	{
		$message = new \stdClass();
		$message->type = 'testmessage';
		$message->value = 'value';
		
		$this->assertEquals(1,
			$this->object->addMessage( $message )
		);
		
		$this->assertEquals(2,
			$this->object->addMessage( $message )
		);
	}

	public function testFetchMessageObject()
	{
		$message = new \stdClass();
		$message->type = 'testmessage';
		$message->value = 'value';
		
		$this->object->addMessage($message);
		$stored = $this->object->fetchMessage();
		
		$this->assertEquals($message->type, $stored->type);
		$this->assertEquals($message->value, $stored->value);
	}
	
	public function testFetchMessageFromEmptyQueue()
	{
		$this->assertNull($this->object->fetchMessage());
	}
	
	public function testAddFetchMessageString()
	{
		$string = 'asdf';
		$this->object->addMessage($string);
		$this->assertEquals($string, $this->object->fetchMessage());
	}
	
	public function testAddFetchMessageArray()
	{
		$array = array('key' => 'value');
		$this->object->addMessage($array);
		$result = $this->object->fetchMessage();
		
		$this->assertEquals($array['key'], $result->key);
	}
	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testAddFetchNullMessage()
	{
		$this->assertEquals(1,
			$this->object->addMessage( null )
		);
	}

}

?>
