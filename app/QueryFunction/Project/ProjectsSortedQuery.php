<?php

declare(strict_types = 1);

namespace App\QueryFunction\Project;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;


final class ProjectsSortedQuery
{

	/** @var EntityManagerInterface */
	private $em;


	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}


	/** @return Project[] */
	public function get(): array
	{
		return $this->em->createQueryBuilder()
			->select('p')
			->from(Project::class, 'p')
			->addOrderBy('p.sort', 'ASC')
			->getQuery()
			->useResultCache(true, 300)
			->getResult();
	}

}
