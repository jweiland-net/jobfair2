<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * A customer can select a value from auto-complete. In that case the search value
 * has the following structure "zip - city". Without using auto-complete the
 * value is either a zip or a city
 */
final readonly class ZipCity
{
    private string $zip;
    private string $city;

    public function __construct(string $address)
    {
        $sanitizedAddress = trim($address);

        if (str_contains($sanitizedAddress, '-')) {
            [$zip, $city] = GeneralUtility::trimExplode('-', $sanitizedAddress);
        } elseif (MathUtility::canBeInterpretedAsInteger($sanitizedAddress)) {
            // Sure, zip is not an integer because of leading zero, but it
            // still can be interpreted as an integer 03524 -> 3524
            $zip = $sanitizedAddress;
            $city = '';
        } else {
            $zip = '';
            $city = $sanitizedAddress;
        }

        $this->zip = $zip;
        $this->city = $city;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
