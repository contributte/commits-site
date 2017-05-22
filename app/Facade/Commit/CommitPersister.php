<?php

declare(strict_types = 1);

namespace App\Facade\Commit;

use App\Entity\Commit;
use Doctrine\ORM\EntityManagerInterface;


final class CommitPersister
{

	/** @var EntityManagerInterface */
	private $em;


	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}


	public function persist(Commit $commit): void
	{
		$this->em->persist($commit);
		$this->em->flush();
	}

}
