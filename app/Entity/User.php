<?php

declare(strict_types = 1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;


/** @ORM\Entity(repositoryClass = "App\Repository\UserRepository") */
class User
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $id;

	/**
	 * @ORM\Column(type = "integer", unique = true)
	 * @var int
	 */
	private $githubID;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $login;

	/**
	 * @ORM\Column(type = "string", nullable = true)
	 * @var string|null
	 */
	private $avatarURL;


	public function __construct(int $githubID, string $login, ?string $avatarURL)
	{
		$this->id = Uuid::uuid4()->toString();
		$this->githubID = $githubID;
		$this->login = $login;
		$this->avatarURL = $avatarURL;
	}


	public function merge(string $login, ?string $avatarURL): void
	{
		$this->login = $login;
		$this->avatarURL = $avatarURL;
	}

}
