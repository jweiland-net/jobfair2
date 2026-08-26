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
use JWeiland\Jobboard\ApiModel\ApiModelInterface;
use JWeiland\Jobboard\ApiModel\ApiModelTrait;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ApiModelTraitTest extends UnitTestCase
{
    private function buildSubject(): ApiModelInterface
    {
        return new class () implements ApiModelInterface {
            use ApiModelTrait;

            private const NAME = 'acme';

            private const API_ENDPOINT = 'https://example.org/api/jobs.xml';

            private const MAPPING = [
                'title' => [
                    'apiPath' => 'title/de',
                    'isDate' => false,
                    'default' => '',
                ],
                'vacancy_id' => [
                    'apiPath' => 'vacancy_id',
                    'isDate' => false,
                    'default' => 0,
                    'prefix' => self::NAME,
                ],
            ];

            private int $storagePid = 0;
        };
    }

    #[Test]
    public function getNameReturnsNameConstant(): void
    {
        self::assertSame('acme', $this->buildSubject()->getName());
    }

    #[Test]
    public function getApiEndpointReturnsApiEndpointConstant(): void
    {
        self::assertSame('https://example.org/api/jobs.xml', $this->buildSubject()->getApiEndpoint());
    }

    #[Test]
    public function getMappingReturnsApiMappingObjectsBuiltFromMappingConstant(): void
    {
        $mapping = $this->buildSubject()->getMapping();

        self::assertEquals(
            new ApiMapping('title/de', false, ''),
            $mapping['title'],
        );
        self::assertEquals(
            new ApiMapping('vacancy_id', false, 0, 'acme'),
            $mapping['vacancy_id'],
        );
    }

    #[Test]
    public function getStoragePidInitiallyReturnsZero(): void
    {
        self::assertSame(0, $this->buildSubject()->getStoragePid());
    }

    #[Test]
    public function withStoragePidReturnsNewInstanceWithChangedStoragePidAndLeavesOriginalUntouched(): void
    {
        $subject = $this->buildSubject();

        $clonedSubject = $subject->withStoragePid(5);

        self::assertNotSame($subject, $clonedSubject);
        self::assertSame(0, $subject->getStoragePid());
        self::assertSame(5, $clonedSubject->getStoragePid());
    }
}
