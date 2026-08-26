<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\ApiModel;

use JWeiland\Jobboard\ApiModel\ApiMapping;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ApiMappingTest extends UnitTestCase
{
    #[Test]
    public function constructWithAllValuesSetsRespectiveGetters(): void
    {
        $subject = new ApiMapping('locations/location/street', true, 'fallback', 'drs');

        self::assertSame('locations/location/street', $subject->getApiPath());
        self::assertTrue($subject->isDate());
        self::assertSame('fallback', $subject->getDefault());
        self::assertSame('drs', $subject->getPrefix());
    }

    #[Test]
    public function constructWithOnlyApiPathUsesDefaultValues(): void
    {
        $subject = new ApiMapping('title/de');

        self::assertSame('title/de', $subject->getApiPath());
        self::assertFalse($subject->isDate());
        self::assertSame('', $subject->getDefault());
        self::assertSame('', $subject->getPrefix());
    }
}
