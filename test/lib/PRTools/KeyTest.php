<?php

namespace PRTools;

/**
 * Test class for Key.
 * Generated by PHPUnit on 2011-03-24 at 23:33:38.
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var Key
	 */
	protected $object;

	protected $testKey = 'testkey';

	protected $testValue = 'testvalue';

	protected $testTtl = 2;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$redis = new \Redis();
		$redis->pconnect('127.0.0.1');
		
		$this->object = new Key(
			$this->testKey, $redis
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->object->delete();
		$this->object = null;
	}
	
	public function testSettingValue()
	{
		$this->assertTrue(
			$this->object->set( $this->testValue ),
			'Value could not be written to Redis. '
		);
	}
	
	public function testSetValueWithExpires()
	{
		$this->assertTrue(
			$this->object->set( 
				$this->testValue,
				$this->testTtl
			),
			'Value with expires time could not be written to Redis. '
		);
		
		$ttl = $this->object->ttl();
		$this->assertEquals(
			$this->testTtl, $ttl,
			'TTl should be '. $this->testTtl . ' but was ' . $ttl . '. '
		);
	}
	
	public function testIfKeyExistsOnEmptyKey()
	{
		$this->assertFalse(
			$this->object->exists(),
			'Key should not exist but did not return false. '
		);
	}


	public function testGettingTtlOnEmptyValue()
	{
		$this->assertEquals(
			-1, $this->object->ttl(),
			'Ttl of empty key should return -1 but was not. '
		);
	}
	
	public function testGettingTtlOfNotExpiringValue()
	{
		$this->object->set($this->testValue);
		
		$this->assertEquals(
			-1, $this->object->ttl(),
			'Ttl of non expiring key should return -1 but was not. '
		);
	}

	public function testSetNonExistingKey()
	{
		$this->assertTrue(
			$this->object->setIfNotExists( $this->testValue ),
			'Setting value on empty key was not successful. '
		);
		
		$this->assertEquals(
			$this->testValue, 
			$this->object->get(),
			'Value should have been set but was not. '
		);
	}

	public function testSetIfNotExistsWithExistingValue()
	{
		$this->object->set($this->testValue);
		
		$this->assertFalse(
			$this->object->setIfNotExists( 'some other' ),
			'Setting value should not be successful but was. '
		);
		
		$this->assertEquals(
			$this->testValue, 
			$this->object->get(),
			'Value has been changed but should not. '
		);
	}

	public function testSettingExpireValue()
	{
		$this->object->set($this->testValue);
		
		$offset = 2;
		
		$this->assertTrue(
			$this->object->expireAt( \time() + $offset ),
			'Setting expire date was not successful. '
		);
		
		$ttl = $this->object->ttl();
		$this->assertEquals(
			$offset,
			$ttl,
			'TTL should be ' . $offset . ' but was ' . $ttl . '. '
		);
	}
	
	public function testSettingExpireValueInThePast()
	{
		$this->object->set($this->testValue);
		$this->assertTrue(
			$this->object->expireAt( 1 ),
			'Setting expire value in the past was not posible. '
		);
		
		$this->assertFalse(
			$this->object->exists(),
			'Setting ttl value in the past should expire key immediately but did not. '
		);
	}
	
	public function testSettingExpireValueOnEmptyKey()
	{
		$this->assertFalse(
			$this->object->expireAt( \time() + 2 ),
			'Setting expire date should not be possible on empty keys. '
		);
	}

	public function testSettingValueWithRedisError()
	{
		$redis = $this->getMock('Redis', array('set'));
		$key = new Key('asdf', $redis);
		$key->set('value');
		
	}
	
	public function testReadingValue()
	{
		$this->assertFalse(
			$this->object->get(), 
			'Key should have been empty but was not. '
		);
		
		$this->object->set($this->testValue);
		
		$this->assertEquals(
			$this->testValue, 
			$this->object->get(),
			'Wrong value read from a Redis key. '
		);
	}
	
	public function testDeletingValue()
	{
		$this->object->set($this->testValue);
		
		$this->assertEquals(
			1, $this->object->delete(), 
			'Value should have been deleted but was not. '
		);
		
		$this->assertEquals(
			0, $this->object->delete(), 
			'Value should have already been deleted but was present. '
		);
	}
	
	
}

?>