<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Functional\Domain\Repository;

use JWeiland\Jobboard\Domain\Model\Job;
use JWeiland\Jobboard\Domain\Model\Search;
use JWeiland\Jobboard\Domain\Repository\JobAreaRepository;
use JWeiland\Jobboard\Domain\Repository\JobRepository;
use JWeiland\Jobboard\Domain\Repository\JobTypeRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 *
 * Covers JobRepository::findBySearch(): the "title is not empty" and "not yet
 * ended" base filters, the job area/job type equality filters, and the zip
 * (exact) / city (LIKE) address filter.
 */
class JobRepositorySearchTest extends FunctionalTestCase
{
    private const STORAGE_PAGE = 2;

    protected JobRepository $subject;

    protected array $testExtensionsToLoad = [
        'friendsoftypo3/tt-address',
        'jweiland/maps2',
        'jweiland/jobboard',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/JobSearchResults.csv');

        $querySettings = $this->get(QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([self::STORAGE_PAGE]);

        $this->subject = $this->get(JobRepository::class);
        $this->subject->setDefaultQuerySettings($querySettings);
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    /**
     * @return string[]
     */
    private function findJobTitles(Search $search): array
    {
        return array_map(
            static fn(Job $job): string => $job->getTitle(),
            iterator_to_array($this->subject->findBySearch($search)),
        );
    }

    #[Test]
    public function findBySearchWithoutAnyCriteriaReturnsAllActiveJobsWithATitle(): void
    {
        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with same city but different zip',
                'Job in a different city',
                'Job in city name containing search term',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job area',
                'Job in Pforzheim but other job type',
                'Job with matching zip in another city',
            ],
            $this->findJobTitles(new Search(null, null, '')),
        );
    }

    #[Test]
    public function findBySearchByExactZipOnlyMatchesAddressesWithThatExactZip(): void
    {
        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job area',
                'Job in Pforzheim but other job type',
            ],
            $this->findJobTitles(new Search(null, null, '76133')),
        );
    }

    #[Test]
    public function findBySearchByCityUsesLikeMatchAndMatchesPartialCityNames(): void
    {
        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with same city but different zip',
                'Job in city name containing search term',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job area',
                'Job in Pforzheim but other job type',
            ],
            $this->findJobTitles(new Search(null, null, 'Pforzheim')),
        );
    }

    #[Test]
    public function findBySearchByAutoCompleteZipAndCityFormatCombinesBothWithLogicalOr(): void
    {
        // "76199 - Pforzheim" is the exact format the frontend autocomplete sends.
        // Zip 76199 belongs to Illingen, so this proves zip and city are
        // OR-combined, not AND-combined - both the exact zip match in Illingen
        // and every partial city match in Pforzheim are returned.
        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with same city but different zip',
                'Job in city name containing search term',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job area',
                'Job in Pforzheim but other job type',
                'Job with matching zip in another city',
            ],
            $this->findJobTitles(new Search(null, null, '76199 - Pforzheim')),
        );
    }

    #[Test]
    public function findBySearchByJobAreaOnlyReturnsJobsOfThatJobArea(): void
    {
        $jobArea = $this->get(JobAreaRepository::class)->findByUid(1);

        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with same city but different zip',
                'Job in city name containing search term',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job type',
                'Job with matching zip in another city',
            ],
            $this->findJobTitles(new Search($jobArea, null, '')),
        );
    }

    #[Test]
    public function findBySearchByJobTypeOnlyReturnsJobsOfThatJobType(): void
    {
        $jobType = $this->get(JobTypeRepository::class)->findByUid(1);

        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job in a different city',
                'Job in city name containing search term',
                'Job with far future ending date in Pforzheim',
                'Job in Pforzheim but other job area',
                'Job with matching zip in another city',
            ],
            $this->findJobTitles(new Search(null, $jobType, '')),
        );
    }

    #[Test]
    public function findBySearchCombinesJobAreaJobTypeAndAddressWithLogicalAnd(): void
    {
        $jobArea = $this->get(JobAreaRepository::class)->findByUid(1);
        $jobType = $this->get(JobTypeRepository::class)->findByUid(1);

        self::assertEqualsCanonicalizing(
            [
                'Job with exact zip match',
                'Job with far future ending date in Pforzheim',
            ],
            $this->findJobTitles(new Search($jobArea, $jobType, '76133')),
        );
    }
}
