<?php

declare(strict_types = 1);

namespace App\Entity;


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
		$this->login = $login;
		$this->id = ID::generate();
		$this->githubID = $githubID;
		$this->avatarURL = $avatarURL;
	}

}
