<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Tests\Unit\UserFunc;

use JWeiland\Jobboard\UserFunc\SalaryStepTitleFormatter;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class SalaryStepTitleFormatterTest extends UnitTestCase
{
    protected SalaryStepTitleFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['LANG'] = null;

        $this->subject = new SalaryStepTitleFormatter();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function formatTitleWithStepLabelPrependsFallbackPrefix(): void
    {
        $parameters = [
            'row' => [
                'step_label' => '3',
                'amount' => 3421.84,
            ],
        ];

        $this->subject->formatTitle($parameters);

        self::assertSame('Step 3 - 3,421.84', $parameters['title']);
    }

    #[Test]
    public function formatTitleWithoutStepLabelOnlyContainsAmount(): void
    {
        $parameters = [
            'row' => [
                'step_label' => '',
                'amount' => 100.0,
            ],
        ];

        $this->subject->formatTitle($parameters);

        self::assertSame('100.00', $parameters['title']);
    }

    #[Test]
    public function formatTitleWithZeroAmountFormatsZero(): void
    {
        $parameters = [
            'row' => [
                'step_label' => '',
                'amount' => 0.0,
            ],
        ];

        $this->subject->formatTitle($parameters);

        self::assertSame('0.00', $parameters['title']);
    }

    #[Test]
    public function formatTitleWithNegativeAmountFormatsNegative(): void
    {
        $parameters = [
            'row' => [
                'step_label' => '',
                'amount' => -50.5,
            ],
        ];

        $this->subject->formatTitle($parameters);

        self::assertSame('-50.50', $parameters['title']);
    }

    #[Test]
    public function formatTitleRoundsAmountToTwoFractionDigits(): void
    {
        $parameters = [
            'row' => [
                'step_label' => '',
                'amount' => 3421.849,
            ],
        ];

        $this->subject->formatTitle($parameters);

        self::assertSame('3,421.85', $parameters['title']);
    }
}
