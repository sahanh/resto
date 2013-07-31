<?php
use Resto\Common\Str;

class StrTest extends PHPUnit_Framework_TestCase
{
	public function testCollectionPath()
	{
		$this->assertEquals('users', Str::collectionPath('Foo\\User'));
		$this->assertEquals('users', Str::collectionPath('Foo\\Bar\\User'));
	}
}