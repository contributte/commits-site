<?php

declare(strict_types = 1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;


class User
{

	/** @var string */
	private $id;

	/** @var int */
	private $githubID;

	/** @var string */
	private $login;

	/** @var string|null */
	private $avatarURL;


	public function __construct(int $githubID, string $login, ?string $avatarURL)
	{
		$this->id = Uuid::uuid4()->toString();
		$this->githubID = $githubID;
		$this->login = $login;
		$this->avatarURL = $avatarURL;
	}

}
