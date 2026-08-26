<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/jobboard.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Jobboard\Middleware;

use Doctrine\DBAL\Driver\Exception;
use JWeiland\Jobboard\Traits\ConnectionPoolTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AddressSearchMiddleware implements MiddlewareInterface
{
    use ConnectionPoolTrait;

    private const HEADER_NAME = 'jobboard-address-search';

    private const TABLE = 'tt_address';

    private const LIMIT = 5;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->hasHeader(self::HEADER_NAME)) {
            return new JsonResponse($this->getAvailableCities($request));
        }

        return $handler->handle($request);
    }

    public function getAvailableCities(ServerRequestInterface $request): array
    {
        $storagePids = $this->getStoragePids($request);
        if ($storagePids === []) {
            return [];
        }

        $zipCity = $this->getZipCityFromRequest($request);
        if ($zipCity === '') {
            return [];
        }

        $queryBuilder = $this->getQueryBuilderForTable(self::TABLE);
        $likeZipOrCity = $queryBuilder->createNamedParameter(
            '%' . $queryBuilder->escapeLikeWildcards($zipCity) . '%',
        );

        $queryResult = $queryBuilder
            ->select('zip', 'city')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('zip', $likeZipOrCity),
                        $queryBuilder->expr()->like('city', $likeZipOrCity),
                    ),
                    $queryBuilder->expr()->in(
                        'pid',
                        $queryBuilder->createNamedParameter($storagePids, Connection::PARAM_INT_ARRAY),
                    ),
                ),
            )
            ->groupBy('zip', 'city')
            ->orderBy('city', 'ASC')->setMaxResults(self::LIMIT)->executeQuery();

        $availableCities = [];
        try {
            while ($ttAddressRecord = $queryResult->fetchAssociative()) {
                $availableCities[] = $ttAddressRecord['zip'] . ' - ' . $ttAddressRecord['city'];
            }
        } catch (Exception) {
        }

        return $availableCities;
    }

    /**
     * "zipCity" comes straight from the request body of an anonymous frontend
     * request and must never be trusted to be a string.
     */
    private function getZipCityFromRequest(ServerRequestInterface $request): string
    {
        $parsedBody = $request->getParsedBody();
        $zipCity = is_array($parsedBody) ? ($parsedBody['zipCity'] ?? '') : '';

        return is_string($zipCity) ? $zipCity : '';
    }

    /**
     * Reads "jobboard.storagePid" from the Site Settings instead of accepting it
     * from the request, so the pages this search is allowed to touch can not be
     * widened by a manipulated request - see Documentation for details.
     *
     * @return int[]
     */
    private function getStoragePids(ServerRequestInterface $request): array
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return [];
        }

        return GeneralUtility::intExplode(
            ',',
            (string)$site->getSettings()->get('jobboard.storagePid', '0'),
            true,
        );
    }
}
