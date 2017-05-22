<?php

declare(strict_types = 1);

namespace App\Facade\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


final class UserPersister
{

	/** @var EntityManagerInterface */
	private $em;


	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}


	public function persist(User $user): void
	{
		$this->em->persist($user);
		$this->em->flush();
	}

}
