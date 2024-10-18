<?php
declare(strict_types=1);

namespace Fomo\Tests;

use Fomo\Auth\Auth;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
	public function testSetGetInstance(): void
	{
		Auth::setInstance();
		$this->assertInstanceOf(Auth::class, Auth::getInstance());
	}

	public function testSetUser(): void
	{
		$user = new \stdClass();
		$user->id = 1;
		$Auth = new Auth;
		$Auth->setUser($user);
		$this->assertEquals(1, $Auth->getId());
	}

	public function testCheckExistUser(): void
	{
		$Auth = new Auth;
		$this->assertFalse($Auth->checkExistUser());
		$user = new \stdClass();
		$user->id = 1;
		$Auth->setUser($user);
		$this->assertTrue($Auth->checkExistUser());
	}

	public function testGetUser(): void
	{
		$user = new \stdClass();
		$user->id = 1;
		$Auth = new Auth;
		$Auth->setUser($user);
		$this->assertEquals($user, $Auth->getUser());
	}

	public function testGetId(): void
	{
		$user = new \stdClass();
		$user->id = 1;
		$Auth = new Auth;
		$Auth->setUser($user);
		$this->assertEquals(1, $Auth->getId());
	}

}