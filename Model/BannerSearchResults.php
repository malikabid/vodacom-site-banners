<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Model;

use Magento\Framework\Api\SearchResults;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterface;

/**
 * Service Data Object with Banner search results.
 * 
 * Extends generic SearchResults to provide type-safe interface
 * for banner-specific search operations.
 * 
 * @since 4.0.1
 */
class BannerSearchResults extends SearchResults implements BannerSearchResultsInterface
{
    // This class intentionally left empty.
    // It extends SearchResults and implements BannerSearchResultsInterface
    // to provide type safety for banner search operations.
    // All functionality is inherited from the parent SearchResults class.
}
