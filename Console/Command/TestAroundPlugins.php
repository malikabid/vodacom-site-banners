<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Class TestAroundPlugins
 * 
 * Console command to test V6.0.3 around plugins:
 * - CacheLayer: Cache-aside pattern
 * - PerformanceMonitor: Execution time tracking
 * - CircuitBreaker: Protected banner deletion prevention
 * 
 * Usage:
 * bin/magento vodacom:test-around-plugins
 * bin/magento vodacom:test-around-plugins --test=cache
 * bin/magento vodacom:test-around-plugins --test=performance
 * bin/magento vodacom:test-around-plugins --test=circuit-breaker
 */
class TestAroundPlugins extends Command
{
    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @var BannerInterfaceFactory
     */
    private BannerInterfaceFactory $bannerFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        BannerInterfaceFactory $bannerFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
        $this->bannerRepository = $bannerRepository;
        $this->bannerFactory = $bannerFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this->setName('vodacom:test-around-plugins')
            ->setDescription('Test V6.0.3 Around Plugins (Cache, Performance, Circuit Breaker)')
            ->addOption(
                'test',
                't',
                InputOption::VALUE_OPTIONAL,
                'Specific test to run: cache, performance, circuit-breaker, or all (default: all)',
                'all'
            );
    }

    /**
     * Execute command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $testType = $input->getOption('test');

        $output->writeln('<info>=================================================</info>');
        $output->writeln('<info>   V6.0.3 Around Plugins Testing Suite</info>');
        $output->writeln('<info>=================================================</info>');
        $output->writeln('');

        try {
            switch ($testType) {
                case 'cache':
                    $this->testCacheLayer($output);
                    break;
                case 'performance':
                    $this->testPerformanceMonitor($output);
                    break;
                case 'circuit-breaker':
                    $this->testCircuitBreaker($output);
                    break;
                case 'all':
                default:
                    $this->testCacheLayer($output);
                    $output->writeln('');
                    $this->testPerformanceMonitor($output);
                    $output->writeln('');
                    $this->testCircuitBreaker($output);
                    break;
            }

            $output->writeln('');
            $output->writeln('<info>=================================================</info>');
            $output->writeln('<info>Check var/log/system.log for detailed plugin logs:</info>');
            $output->writeln('<comment>tail -f var/log/system.log | grep -E "(CACHE|PERFORMANCE|CIRCUIT BREAKER)"</comment>');
            $output->writeln('<info>=================================================</info>');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    /**
     * Test 1: CacheLayer Around Plugin
     */
    private function testCacheLayer(OutputInterface $output): void
    {
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('<fg=cyan>TEST 1: CacheLayer Around Plugin</fg=cyan>');
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('');

        // Get first banner ID from database
        $searchCriteria = $this->searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();
        $results = $this->bannerRepository->getList($searchCriteria);
        
        $items = $results->getItems();
        if (empty($items)) {
            $output->writeln('<error>No banners found. Please create sample banners first.</error>');
            return;
        }

        $bannerId = reset($items)->getId();

        $output->writeln('<comment>Testing cache-aside pattern with banner ID: ' . $bannerId . '</comment>');
        $output->writeln('');

        // First call - CACHE MISS (will load from database)
        $output->writeln('<info>1. First getById() call - Expected: CACHE MISS</info>');
        $banner1 = $this->bannerRepository->getById((int)$bannerId);
        $output->writeln('   ✓ Banner loaded: ' . $banner1->getTitle());
        $output->writeln('   → Check logs for: [CACHE MISS] loading from database');
        sleep(1);

        // Second call - CACHE HIT (will return from cache, skipping database)
        $output->writeln('');
        $output->writeln('<info>2. Second getById() call - Expected: CACHE HIT</info>');
        $banner2 = $this->bannerRepository->getById((int)$bannerId);
        $output->writeln('   ✓ Banner loaded: ' . $banner2->getTitle());
        $output->writeln('   → Check logs for: [CACHE HIT] loaded from cache (skipped database query)');
        sleep(1);

        // Save banner - CACHE INVALIDATION
        $output->writeln('');
        $output->writeln('<info>3. Save banner - Expected: CACHE INVALIDATION</info>');
        $banner2->setTitle($banner2->getTitle() . ' [Modified]');
        $this->bannerRepository->save($banner2);
        $output->writeln('   ✓ Banner saved with modified title');
        $output->writeln('   → Check logs for: [CACHE] Invalidating cache before save');
        sleep(1);

        // Third call after save - CACHE MISS again (cache was invalidated)
        $output->writeln('');
        $output->writeln('<info>4. Third getById() call after save - Expected: CACHE MISS</info>');
        $banner3 = $this->bannerRepository->getById((int)$bannerId);
        $output->writeln('   ✓ Banner loaded: ' . $banner3->getTitle());
        $output->writeln('   → Check logs for: [CACHE MISS] loading from database');

        // Restore original title
        $banner3->setTitle(str_replace(' [Modified]', '', $banner3->getTitle()));
        $this->bannerRepository->save($banner3);
        $output->writeln('');
        $output->writeln('   <comment>✓ Title restored to original</comment>');
    }

    /**
     * Test 2: PerformanceMonitor Around Plugin
     */
    private function testPerformanceMonitor(OutputInterface $output): void
    {
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('<fg=cyan>TEST 2: PerformanceMonitor Around Plugin</fg=cyan>');
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('');

        $output->writeln('<comment>Testing execution time measurement for getList()</comment>');
        $output->writeln('');

        // Test 1: Small query (should be fast)
        $output->writeln('<info>1. Fast query - Get first 5 banners</info>');
        $searchCriteria1 = $this->searchCriteriaBuilder
            ->setPageSize(5)
            ->setCurrentPage(1)
            ->create();
        $results1 = $this->bannerRepository->getList($searchCriteria1);
        $output->writeln('   ✓ Retrieved ' . $results1->getTotalCount() . ' banners');
        $output->writeln('   → Check logs for: [PERFORMANCE] Banner search completed in X.XX ms');
        sleep(1);

        // Test 2: Larger query with filter
        $output->writeln('');
        $output->writeln('<info>2. Filtered query - Get active banners only</info>');
        $searchCriteria2 = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->setPageSize(20)
            ->create();
        $results2 = $this->bannerRepository->getList($searchCriteria2);
        $output->writeln('   ✓ Retrieved ' . count($results2->getItems()) . ' active banners');
        $output->writeln('   → Check logs for execution time and filter count');
        sleep(1);

        // Test 3: All banners (potentially slower)
        $output->writeln('');
        $output->writeln('<info>3. Large query - Get all banners (no pagination)</info>');
        $searchCriteria3 = $this->searchCriteriaBuilder->create();
        $results3 = $this->bannerRepository->getList($searchCriteria3);
        $output->writeln('   ✓ Retrieved ' . $results3->getTotalCount() . ' total banners');
        $output->writeln('   → If > 100ms, should see: [SLOW QUERY DETECTED]');
    }

    /**
     * Test 3: CircuitBreaker Around Plugin
     */
    private function testCircuitBreaker(OutputInterface $output): void
    {
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('<fg=cyan>TEST 3: CircuitBreaker Around Plugin</fg=cyan>');
        $output->writeln('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $output->writeln('');

        $output->writeln('<comment>Testing protected banner deletion prevention (sort_order < 10)</comment>');
        $output->writeln('');

        // Find a protected banner (sort_order < 10)
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $allBanners = $this->bannerRepository->getList($searchCriteria);
        
        $protectedBanner = null;
        $unprotectedBanner = null;

        foreach ($allBanners->getItems() as $banner) {
            if ($banner->getSortOrder() < 10 && !$protectedBanner) {
                $protectedBanner = $banner;
            }
            if ($banner->getSortOrder() >= 10 && !$unprotectedBanner) {
                $unprotectedBanner = $banner;
            }
        }

        // Test 1: Try to delete PROTECTED banner (should FAIL)
        if ($protectedBanner) {
            $output->writeln('<info>1. Attempt to delete PROTECTED banner</info>');
            $output->writeln('   Banner: "' . $protectedBanner->getTitle() . '"');
            $output->writeln('   Sort Order: ' . $protectedBanner->getSortOrder() . ' (< 10 = PROTECTED)');
            $output->writeln('');

            try {
                $this->bannerRepository->delete($protectedBanner);
                $output->writeln('   <error>✗ UNEXPECTED: Deletion succeeded (should have been blocked!)</error>');
            } catch (CouldNotDeleteException $e) {
                $output->writeln('   <fg=green>✓ EXPECTED: Deletion blocked by circuit breaker</>');
                $output->writeln('   Error: ' . $e->getMessage());
                $output->writeln('   → Check logs for: [CIRCUIT BREAKER] BLOCKED deletion');
            }
        } else {
            $output->writeln('<comment>No protected banners found (sort_order < 10)</comment>');
        }

        // Test 2: Try to delete UNPROTECTED banner (should SUCCEED)
        $output->writeln('');
        if ($unprotectedBanner) {
            $output->writeln('<info>2. Attempt to delete UNPROTECTED banner</info>');
            $output->writeln('   Banner: "' . $unprotectedBanner->getTitle() . '"');
            $output->writeln('   Sort Order: ' . $unprotectedBanner->getSortOrder() . ' (>= 10 = ALLOWED)');
            $output->writeln('');

            // Create a test banner to delete safely
            $testBanner = $this->bannerFactory->create();
            $testBanner->setTitle('Test Banner for Circuit Breaker (Safe to Delete)');
            $testBanner->setContent('This banner is created for testing and will be deleted.');
            $testBanner->setIsActive(false);
            $testBanner->setSortOrder(100); // Definitely unprotected
            $savedBanner = $this->bannerRepository->save($testBanner);

            try {
                $this->bannerRepository->delete($savedBanner);
                $output->writeln('   <fg=green>✓ EXPECTED: Deletion approved and completed</>');
                $output->writeln('   → Check logs for: [CIRCUIT BREAKER] APPROVED deletion');
            } catch (\Exception $e) {
                $output->writeln('   <error>✗ UNEXPECTED: Deletion failed</error>');
                $output->writeln('   Error: ' . $e->getMessage());
            }
        } else {
            $output->writeln('<comment>No unprotected banners found for testing</comment>');
        }
    }
}
