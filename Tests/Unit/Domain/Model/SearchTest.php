<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\Domain\Model;

use JWeiland\Jobboard\Domain\Model\JobArea;
use JWeiland\Jobboard\Domain\Model\JobType;
use JWeiland\Jobboard\Domain\Model\Search;
use JWeiland\Jobboard\Domain\Model\ZipCity;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class SearchTest extends UnitTestCase
{
    #[Test]
    public function constructWithJobAreaAndJobTypeSetsRespectiveGetters(): void
    {
        $jobArea = new JobArea();
        $jobType = new JobType();

        $subject = new Search($jobArea, $jobType, 'Pforzheim', '');

        self::assertSame($jobArea, $subject->getJobArea());
        self::assertSame($jobType, $subject->getJobType());
    }

    #[Test]
    public function constructWithoutJobAreaAndJobTypeReturnsNull(): void
    {
        $subject = new Search(null, null, 'Pforzheim', '');

        self::assertNull($subject->getJobArea());
        self::assertNull($subject->getJobType());
    }

    #[Test]
    public function getZipCityReturnsZipCityBuiltFromAddress(): void
    {
        // "<zip> - <city>" is the exact format the frontend autocomplete sends.
        $subject = new Search(null, null, '76133 - Pforzheim', '');

        self::assertEquals(
            new ZipCity('76133 - Pforzheim'),
            $subject->getZipCity(),
        );
    }

    #[Test]
    public function getSearchWordReturnsConstructorValue(): void
    {
        $subject = new Search(null, null, '', 'Bahnhofstraße Pforzheim');

        self::assertSame('Bahnhofstraße Pforzheim', $subject->getSearchWord());
    }

    #[Test]
    public function getSelectedValuesWithoutJobAreaAndJobTypeReturnsOnlyAddressAndSearchWord(): void
    {
        $subject = new Search(null, null, 'Pforzheim', 'Bahnhofstraße');

        self::assertSame(
            [
                'selected_address' => 'Pforzheim',
                'selected_search_word' => 'Bahnhofstraße',
            ],
            $subject->getSelectedValues(),
        );
    }

    #[Test]
    public function getSelectedValuesWithJobAreaAlsoReturnsJobAreaUid(): void
    {
        $subject = new Search(new JobArea(), null, 'Pforzheim', '');

        self::assertSame(
            [
                'selected_address' => 'Pforzheim',
                'selected_search_word' => '',
                'selected_job_area' => null,
            ],
            $subject->getSelectedValues(),
        );
    }

    #[Test]
    public function getSelectedValuesWithJobTypeAlsoReturnsJobTypeUid(): void
    {
        $subject = new Search(null, new JobType(), 'Pforzheim', '');

        self::assertSame(
            [
                'selected_address' => 'Pforzheim',
                'selected_search_word' => '',
                'selected_job_type' => null,
            ],
            $subject->getSelectedValues(),
        );
    }

    #[Test]
    public function getSelectedValuesWithJobAreaAndJobTypeReturnsAllSelectedValues(): void
    {
        $subject = new Search(new JobArea(), new JobType(), 'Pforzheim', '');

        self::assertSame(
            [
                'selected_address' => 'Pforzheim',
                'selected_search_word' => '',
                'selected_job_area' => null,
                'selected_job_type' => null,
            ],
            $subject->getSelectedValues(),
        );
    }
}
