<?php
use Resto\Common\Helpers as H;

class HelpersTest extends PHPUnit_Framework_TestCase
{
	public $array;

	public function setUp()
	{
		$this->array = array('name' => 'John', 'last_name' => 'Doe', 2, 3);
	}

	public function testArraySet()
	{
		H::arraySet($this->array, 'email', 'johndoe@example.com');
		$this->assertEquals('johndoe@example.com', $this->array['email']);

		H::arraySet($this->array, 'name.first', 'John2');
		$this->assertEquals('John2', $this->array['name']['first']);
		$this->assertNotEquals('John', $this->array['name']);
	}

	public function testArrayGet()
	{
		$name = H::arrayGet($this->array, 'name');
		$this->assertEquals('John', $name);
	}

	public function testArrayIsAssoc()
	{
		$this->assertTrue(H::arrayIsAssoc(array('first' => 'john', 'email' => 'foo@bar.com')));
		$this->assertFalse(H::arrayIsAssoc(array('john', 'foo@bar.com')));
	}

	public function testArrayIsMulti()
	{
		$this->assertTrue(H::arrayIsMulti(array('foo' => 'bar', 'bar' => array('foo' => 'bar'))));
		$this->assertFalse(H::arrayIsMulti(array('foo' => 'bar', 'bar' => 'foo')));
	}
}