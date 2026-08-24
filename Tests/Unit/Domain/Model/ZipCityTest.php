<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\Domain\Model;

use JWeiland\Jobboard\Domain\Model\ZipCity;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ZipCityTest extends UnitTestCase
{
    #[Test]
    public function constructWithZipAndCityCombinedByDashSplitsIntoZipAndCity(): void
    {
        $subject = new ZipCity('76133-Pforzheim');

        self::assertSame('76133', $subject->getZip());
        self::assertSame('Pforzheim', $subject->getCity());
    }

    #[Test]
    public function constructWithAutoCompleteFormatSplitsIntoZipAndCity(): void
    {
        // This is the exact format the frontend autocomplete sends: "<zip> - <city>".
        $subject = new ZipCity('76133 - Pforzheim');

        self::assertSame('76133', $subject->getZip());
        self::assertSame('Pforzheim', $subject->getCity());
    }

    #[Test]
    public function constructTrimsWhitespaceAroundDashSeparatedParts(): void
    {
        $subject = new ZipCity('  76133 - Pforzheim  ');

        self::assertSame('76133', $subject->getZip());
        self::assertSame('Pforzheim', $subject->getCity());
    }

    #[Test]
    public function constructWithOnlyNumericAddressSetsZipAndEmptyCity(): void
    {
        $subject = new ZipCity('76133');

        self::assertSame('76133', $subject->getZip());
        self::assertSame('', $subject->getCity());
    }

    #[Test]
    public function constructWithOnlyTextAddressSetsCityAndEmptyZip(): void
    {
        $subject = new ZipCity('Pforzheim');

        self::assertSame('', $subject->getZip());
        self::assertSame('Pforzheim', $subject->getCity());
    }

    #[Test]
    public function constructWithLeadingZeroZipIsTreatedAsCity(): void
    {
        // MathUtility::canBeInterpretedAsInteger() casts to int and back to
        // string for comparison, so a leading zero breaks the round-trip and
        // the value falls through to the city branch instead of zip.
        $subject = new ZipCity('03524');

        self::assertSame('', $subject->getZip());
        self::assertSame('03524', $subject->getCity());
    }

    #[Test]
    public function constructWithEmptyAddressReturnsEmptyZipAndCity(): void
    {
        $subject = new ZipCity('');

        self::assertSame('', $subject->getZip());
        self::assertSame('', $subject->getCity());
    }

    #[Test]
    public function constructWithDashOnlyAddressReturnsEmptyZipAndCity(): void
    {
        $subject = new ZipCity('-');

        self::assertSame('', $subject->getZip());
        self::assertSame('', $subject->getCity());
    }

    #[Test]
    public function constructWithMoreThanOneDashUsesOnlyFirstTwoSegments(): void
    {
        $subject = new ZipCity('76133-Pforzheim-Mitte');

        self::assertSame('76133', $subject->getZip());
        self::assertSame('Pforzheim', $subject->getCity());
    }
}
