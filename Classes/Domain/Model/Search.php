<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Domain\Model;

final readonly class Search
{
    private ZipCity $zipCity;

    public function __construct(
        private ?JobArea $jobArea,
        private ?JobRole $jobRole,
        private ?JobType $jobType,
        private string $address,
        private string $searchWord,
    ) {
        $this->zipCity = new ZipCity($address);
    }

    public function getJobArea(): ?JobArea
    {
        return $this->jobArea;
    }

    public function getJobRole(): ?JobRole
    {
        return $this->jobRole;
    }

    public function getJobType(): ?JobType
    {
        return $this->jobType;
    }

    public function getZipCity(): ZipCity
    {
        return $this->zipCity;
    }

    public function getSearchWord(): string
    {
        return $this->searchWord;
    }

    /**
     * Can be used to assign to Template via assignMultiple()
     */
    public function getSelectedValues(): array
    {
        $selectedValues = [
            'selected_address' => $this->address,
            'selected_search_word' => $this->searchWord,
        ];

        if ($this->jobArea instanceof JobArea) {
            $selectedValues['selected_job_area'] = $this->jobArea->getUid();
        }

        if ($this->jobRole instanceof JobRole) {
            $selectedValues['selected_job_role'] = $this->jobRole->getUid();
        }

        if ($this->jobType instanceof JobType) {
            $selectedValues['selected_job_type'] = $this->jobType->getUid();
        }

        return $selectedValues;
    }
}
