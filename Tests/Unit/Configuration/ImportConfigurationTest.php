<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\Configuration;

use JWeiland\Jobboard\Configuration\ImportConfiguration;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ImportConfigurationTest extends UnitTestCase
{
    #[Test]
    public function getStorageReturnsConstructorValue(): void
    {
        $subject = new ImportConfiguration(42);

        self::assertSame(42, $subject->getStorage());
    }
}
