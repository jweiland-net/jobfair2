<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Functional\Domain\Repository;

use JWeiland\Jobboard\Domain\Model\JobType;
use JWeiland\Jobboard\Domain\Repository\JobTypeRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
final class JobTypeRepositoryTest extends FunctionalTestCase
{
    private const STORAGE_PAGE = 2;

    protected array $testExtensionsToLoad = [
        'friendsoftypo3/tt-address',
        'jweiland/maps2',
        'jweiland/jobboard',
    ];

    private JobTypeRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/JobTypeRepository.csv');

        $querySettings = $this->get(QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([self::STORAGE_PAGE]);

        $this->subject = $this->get(JobTypeRepository::class);
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
    public function findAllResolvesImportedJobTypesFromStoragePage(): void
    {
        $titles = array_map(
            static fn(JobType $jobType): string => $jobType->getTitle(),
            iterator_to_array($this->subject->findAll()),
        );

        self::assertEqualsCanonicalizing(
            ['Vollzeit', 'Teilzeit'],
            $titles,
        );
    }
}
