<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Functional\Domain\Repository;

use JWeiland\Jobboard\Domain\Model\JobArea;
use JWeiland\Jobboard\Domain\Repository\JobAreaRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
final class JobAreaRepositoryTest extends FunctionalTestCase
{
    private const STORAGE_PAGE = 2;

    protected array $testExtensionsToLoad = [
        'friendsoftypo3/tt-address',
        'jweiland/maps2',
        'jweiland/jobboard',
    ];

    private JobAreaRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/JobAreaRepository.csv');

        $querySettings = $this->get(QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([self::STORAGE_PAGE]);

        $this->subject = $this->get(JobAreaRepository::class);
        $this->subject->setDefaultQuerySettings($querySettings);
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function findByUidsReturnsOnlyRequestedJobAreas(): void
    {
        $titles = array_map(
            static fn(JobArea $jobArea): string => $jobArea->getTitle(),
            iterator_to_array($this->subject->findByUids([1, 3])),
        );

        self::assertEqualsCanonicalizing(
            ['IT', 'Pflege'],
            $titles,
        );
    }

    #[Test]
    public function findByUidsWithEmptyArrayReturnsNoJobAreas(): void
    {
        self::assertCount(
            0,
            $this->subject->findByUids([]),
        );
    }
}
