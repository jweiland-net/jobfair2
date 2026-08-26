<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\Domain\Model;

use JWeiland\Jobboard\Domain\Model\Address;
use JWeiland\Maps2\Domain\Model\PoiCollection;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class AddressTest extends UnitTestCase
{
    protected Address $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Address();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getTxMaps2UidInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getTxMaps2Uid());
    }

    #[Test]
    public function setTxMaps2UidSetsTxMaps2Uid(): void
    {
        $instance = new PoiCollection();
        $this->subject->setTxMaps2Uid($instance);

        self::assertSame(
            $instance,
            $this->subject->getTxMaps2Uid(),
        );
    }
}
