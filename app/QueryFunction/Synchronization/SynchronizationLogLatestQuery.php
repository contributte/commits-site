<?php

declare(strict_types = 1);

namespace App\QueryFunction\Synchronization;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Synchronization\SynchronizationLog;


final class SynchronizationLogLatestQuery
{

	private EntityManagerInterface $em;


	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}


	public function get(): SynchronizationLog
	{
		$log = $this->em->createQueryBuilder()
			->select('sl')
			->from(SynchronizationLog::class, 'sl')
			->addOrderBy('sl.startedAt', 'DESC')
			->andWhere('sl.finishedAt IS NOT NULL')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		if ($log === null) {
			throw SynchronizationLogNotFoundException::latestFinished();
		}

		assert($log instanceof SynchronizationLog);

		return $log;
	}

}
