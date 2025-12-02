<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Psr\Log\LoggerInterface;

/**
 * Before Plugin: Sanitize banner title before save
 * 
 * Demonstrates:
 * - Before plugin with argument modification
 * - Returning array to change method parameters
 * - Data validation and sanitization
 * - Automatic data cleaning
 * 
 * This plugin executes BEFORE BannerRepository::save()
 * to clean and sanitize the banner title.
 * 
 * Sanitization Rules:
 * 1. Trim whitespace from start/end
 * 2. Remove extra spaces (multiple spaces → single space)
 * 3. Capitalize first letter of each word (Title Case)
 * 4. Remove special characters (optional)
 * 
 * Plugin Configuration:
 * - Configured in etc/di.xml
 * - sortOrder: 5 (executes BEFORE SaveLogger with sortOrder 10)
 * - Type: before
 * 
 * Use Cases:
 * - Automatic data cleaning
 * - Enforcing data quality standards
 * - Preventing garbage data entry
 * - Consistent formatting across system
 */
class BannerRepositoryTitleSanitizer
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Before plugin for BannerRepository::save()
     * 
     * Sanitizes the banner title before save operation.
     * MODIFIES the banner title by returning array.
     * 
     * Method Signature Pattern (with modification):
     * - Must be public
     * - Method name: before + OriginalMethodName
     * - First parameter: $subject (intercepted object)
     * - Remaining parameters: same as original method
     * - Return type: array (to modify parameters) OR void (no modification)
     * 
     * Return Array Structure:
     * - Array keys = parameter positions (0-indexed)
     * - Array values = modified parameter values
     * - Example: return [$modifiedBanner]; // Replaces first parameter
     * 
     * Plugin Execution Order:
     * 1. TitleSanitizer::beforeSave() (sortOrder 5) ← THIS PLUGIN - modifies title
     * 2. SaveLogger::beforeSave() (sortOrder 10) - logs sanitized title
     * 3. Original method receives sanitized banner
     * 
     * @param BannerRepositoryInterface $subject The repository being intercepted
     * @param BannerInterface $banner The banner being saved
     * @return array Modified banner as array [0 => $modifiedBanner]
     */
    public function beforeSave(
        BannerRepositoryInterface $subject,
        BannerInterface $banner
    ): array {
        // Get current title
        $originalTitle = $banner->getTitle();
        
        // Apply sanitization
        $sanitizedTitle = $this->sanitizeTitle($originalTitle);
        
        // Log if title was modified
        if ($originalTitle !== $sanitizedTitle) {
            $this->logger->info(
                'Banner title sanitized',
                [
                    'banner_id' => $banner->getBannerId() ?: 'NEW',
                    'original_title' => $originalTitle,
                    'sanitized_title' => $sanitizedTitle
                ]
            );
        }
        
        // Modify the banner title
        $banner->setTitle($sanitizedTitle);
        
        // CRITICAL: Return array to modify parameters
        // The array index corresponds to parameter position
        // [0] = first parameter (after $subject) = $banner
        // This modified banner will be passed to next plugin and original method
        return [$banner];
    }

    /**
     * Sanitize title string
     * 
     * Sanitization steps:
     * 1. Trim whitespace
     * 2. Remove extra spaces
     * 3. Convert to Title Case
     * 
     * @param string|null $title
     * @return string
     */
    private function sanitizeTitle(?string $title): string
    {
        if (!$title) {
            return '';
        }
        
        // Step 1: Trim leading/trailing whitespace
        $title = trim($title);

        $title = $this->removeSpecialCharacters($title);
        
        // Step 2: Replace multiple spaces with single space
        // Uses regex: \s+ matches one or more whitespace characters
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Step 3: Convert to Title Case (first letter of each word capitalized)
        // Note: ucwords() capitalizes first letter of each word
        // mb_convert_case() handles UTF-8 better for international characters
        $title = mb_convert_case($title, MB_CASE_TITLE, 'UTF-8');
        
        // Alternative: Simple ucfirst for first letter only
        // $title = ucfirst(strtolower($title));
        
        return $title;
    }

    /**
     * Additional sanitization: Remove special characters (optional)
     * 
     * Uncomment to enable strict sanitization
     * 
     * @param string $title
     * @return string
     */
    private function removeSpecialCharacters(string $title): string
    {
        // Allow: letters, numbers, spaces, hyphens, underscores
        // Remove: everything else
        return preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $title);
    }

    /**
     * Validate title length
     * 
     * @param string $title
     * @return bool
     */
    private function isValidTitleLength(string $title): bool
    {
        $length = mb_strlen($title, 'UTF-8');
        return $length > 0 && $length <= 255;
    }
}
