<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace app;

use Nette;


/**
 * Trivial implementation of IAuthenticator.
 */
class MyAuthenticator implements Nette\Security\IAuthenticator
{
	private $database;
	private $passwords;

	public function __construct(Nette\Database\Context $database, Nette\Security\Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}

	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[$username, $password] = $credentials;

		$row = $this->database->table('users')
			->where('username', $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}

		if (!$this->passwords->verify($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}

		return new Nette\Security\Identity(
			$row->id,
			$row->role, // nebo pole více rolí
			['name' => $row->username]
		);
	}
}
