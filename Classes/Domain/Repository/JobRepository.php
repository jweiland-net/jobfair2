<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Domain\Repository;

use JWeiland\Jobboard\Domain\Model\JobArea;
use JWeiland\Jobboard\Domain\Model\JobType;
use JWeiland\Jobboard\Domain\Model\Search;
use JWeiland\Jobboard\Domain\Model\ZipCity;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Allows to help search for jobs by various search criteria
 *
 * Deliberately does not filter out jobs without resolvable salary information -
 * that is a display rule, not a data access concern, and belongs in
 * JobboardController (see excludeJobsWithoutSalaryInformation()).
 */
class JobRepository extends Repository
{
    protected $defaultOrderings = [
        'endingDate' => QueryInterface::ORDER_ASCENDING,
    ];

    public function findBySettings(array $settings, int $limit = 0): QueryResultInterface
    {
        $query = $this->createQuery();

        $andConstraint = [
            $query->logicalNot($query->equals('title', '')),
        ];

        $andConstraint[] = $query->logicalOr(
            $query->equals('endingDate', 0),
            $query->greaterThanOrEqual('endingDate', new \DateTime()),
        );

        $jobAreas = $settings['jobAreas'] ?? [];
        if (is_array($jobAreas)) {
            $andConstraint[] = $query->in('jobArea', $jobAreas);
        }

        if ($limit) {
            $query->setLimit($limit);
        }

        return $query->matching($query->logicalAnd(...$andConstraint))->execute();
    }

    public function findBySearch(Search $search, int $limit = 0): QueryResultInterface
    {
        $query = $this->createQuery();

        $andConstraint = [
            $query->logicalNot($query->equals('title', '')),
        ];

        $andConstraint[] = $query->logicalOr(
            $query->equals('endingDate', 0),
            $query->greaterThanOrEqual('endingDate', new \DateTime()),
        );

        if ($search->getJobArea() instanceof JobArea) {
            $andConstraint[] = $query->equals('jobArea', $search->getJobArea());
        }

        if ($search->getJobType() instanceof JobType) {
            $andConstraint[] = $query->equals('jobType', $search->getJobType());
        }

        $orConstraint = $this->buildOrConstraintForAddress($search->getZipCity(), $query);
        if ($orConstraint !== []) {
            $andConstraint[] = $query->logicalOr(...$orConstraint);
        }

        if ($limit) {
            $query->setLimit($limit);
        }

        return $query->matching($query->logicalAnd(...$andConstraint))->execute();
    }

    private function buildOrConstraintForAddress(ZipCity $zipCity, QueryInterface $query): array
    {
        $orConstraint = [];

        // For zip, we do an exact search
        if ($zipCity->getZip()) {
            $orConstraint[] = $query->equals('address.zip', $zipCity->getZip());
        }

        // For city, we start a like search
        if ($zipCity->getCity()) {
            $orConstraint[] = $query->like(
                'address.city',
                '%' . addcslashes($zipCity->getCity(), '_%') . '%',
            );
        }

        return $orConstraint;
    }
}
