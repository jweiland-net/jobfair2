<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\ApiModel;

use JWeiland\Jobboard\ApiModel\LocationModel;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class LocationModelTest extends UnitTestCase
{
    #[Test]
    public function isPrimaryWithPrimaryAttributeTrueReturnsTrue(): void
    {
        $subject = new LocationModel(new \SimpleXMLElement('<location primary="true"></location>'));

        self::assertTrue($subject->isPrimary());
    }

    #[Test]
    public function isPrimaryWithPrimaryAttributeFalseReturnsFalse(): void
    {
        $subject = new LocationModel(new \SimpleXMLElement('<location primary="false"></location>'));

        self::assertFalse($subject->isPrimary());
    }

    #[Test]
    public function isPrimaryWithoutPrimaryAttributeReturnsFalse(): void
    {
        $subject = new LocationModel(new \SimpleXMLElement('<location></location>'));

        self::assertFalse($subject->isPrimary());
    }
}
