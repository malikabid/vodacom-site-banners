<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Vodacom\SiteBanners\Service\BannerService;

/**
 * Class DemonstratePatterns
 * Console command to demonstrate Factory and Proxy patterns
 * 
 * USAGE:
 * bin/magento vodacom:banner:demo-patterns
 * bin/magento vodacom:banner:demo-patterns --skip-bulk  (skip bulk create)
 * 
 * WHAT THIS DEMONSTRATES:
 * 1. Factory Pattern - Creating multiple banner instances dynamically
 * 2. CollectionFactory - Independent collections for different queries
 * 3. Service Layer - Business logic separated from presentation
 * 
 * @package Vodacom\SiteBanners\Console\Command
 */
class DemonstratePatterns extends Command
{
    /**
     * @var BannerService
     */
    private BannerService $bannerService;

    /**
     * DemonstratePatterns constructor.
     * 
     * @param BannerService $bannerService
     * @param string|null $name
     */
    public function __construct(
        BannerService $bannerService,
        string $name = null
    ) {
        parent::__construct($name);
        $this->bannerService = $bannerService;
    }

    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this->setName('vodacom:banner:demo-patterns');
        $this->setDescription('Demonstrate Factory and Proxy patterns');
        $this->addOption(
            'skip-bulk',
            null,
            InputOption::VALUE_NONE,
            'Skip bulk banner creation'
        );
        parent::configure();
    }

    /**
     * Execute command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('<info>========================================</info>');
        $output->writeln('<info>  V5.0.2 Factory Pattern Demonstration</info>');
        $output->writeln('<info>========================================</info>');
        $output->writeln('');

        // Test 1: Create banner using Factory
        $output->writeln('<comment>Test 1: Creating banner using BannerInterfaceFactory</comment>');
        $output->writeln('-----------------------------------------------');
        try {
            $banner = $this->bannerService->createBanner([
                'title' => 'Demo Banner from Console Command',
                'content' => 'This banner was created using Factory pattern via BannerService',
                'is_active' => 1,
                'sort_order' => 100
            ]);
            $output->writeln("✅ <info>Created banner ID: {$banner->getBannerId()}</info>");
            $output->writeln("   Title: {$banner->getTitle()}");
        } catch (\Exception $e) {
            $output->writeln("<error>❌ Failed to create banner: {$e->getMessage()}</error>");
        }
        $output->writeln('');

        // Test 2: Get statistics using multiple CollectionFactory instances
        $output->writeln('<comment>Test 2: Using CollectionFactory for independent queries</comment>');
        $output->writeln('-----------------------------------------------');
        try {
            $stats = $this->bannerService->getBannerStatistics();
            $output->writeln("✅ <info>Statistics retrieved successfully:</info>");
            $output->writeln("   Active Banners: <info>{$stats['active']}</info>");
            $output->writeln("   Inactive Banners: <info>{$stats['inactive']}</info>");
            $output->writeln("   Total Banners: <info>{$stats['total']}</info>");
            $output->writeln("   Active Percentage: <info>{$stats['active_percentage']}%</info>");
            $output->writeln('');
            $output->writeln('   <comment>Each stat uses FRESH collection from CollectionFactory</comment>');
        } catch (\Exception $e) {
            $output->writeln("<error>❌ Failed to get statistics: {$e->getMessage()}</error>");
        }
        $output->writeln('');

        // Test 3: Clone banner (if any exist)
        $output->writeln('<comment>Test 3: Cloning banner (Factory creates NEW instance)</comment>');
        $output->writeln('-----------------------------------------------');
        try {
            $count = $this->bannerService->getActiveBannersCount();
            if ($count > 0) {
                // Clone the first banner (assuming banner_id = 1 exists)
                $cloned = $this->bannerService->cloneBanner(1);
                $output->writeln("✅ <info>Cloned banner successfully!</info>");
                $output->writeln("   Original ID: <info>1</info>");
                $output->writeln("   Cloned ID: <info>{$cloned->getBannerId()}</info>");
                $output->writeln("   Cloned Title: {$cloned->getTitle()}");
            } else {
                $output->writeln("<comment>⚠ No banners available to clone</comment>");
            }
        } catch (\Exception $e) {
            $output->writeln("<error>❌ Failed to clone banner: {$e->getMessage()}</error>");
        }
        $output->writeln('');

        // Test 4: Bulk create (Factory in loop)
        if (!$input->getOption('skip-bulk')) {
            $output->writeln('<comment>Test 4: Bulk creation (Factory in loop)</comment>');
            $output->writeln('-----------------------------------------------');
            $bulkData = [
                ['title' => 'Bulk Banner 1', 'content' => 'First bulk banner content', 'is_active' => 1, 'sort_order' => 200],
                ['title' => 'Bulk Banner 2', 'content' => 'Second bulk banner content', 'is_active' => 1, 'sort_order' => 210],
                ['title' => 'Bulk Banner 3', 'content' => 'Third bulk banner content', 'is_active' => 0, 'sort_order' => 220]
            ];
            
            try {
                $results = $this->bannerService->bulkCreateBanners($bulkData);
                $output->writeln("✅ <info>Bulk creation complete:</info>");
                $output->writeln("   Successfully created: <info>" . count($results['success']) . " banners</info>");
                $output->writeln("   Failed: <info>" . count($results['failed']) . "</info>");
                
                if (!empty($results['success'])) {
                    $output->writeln("   Created IDs: " . implode(', ', $results['success']));
                }
                
                if (!empty($results['failed'])) {
                    $output->writeln("   <error>Failed banners:</error>");
                    foreach ($results['failed'] as $failed) {
                        $output->writeln("     - {$failed['data']['title']}: {$failed['error']}");
                    }
                }
            } catch (\Exception $e) {
                $output->writeln("<error>❌ Bulk creation failed: {$e->getMessage()}</error>");
            }
            $output->writeln('');
        }

        // Summary
        $output->writeln('<info>========================================</info>');
        $output->writeln('<info>KEY CONCEPTS DEMONSTRATED:</info>');
        $output->writeln('<info>========================================</info>');
        $output->writeln('');
        $output->writeln('<comment>1. BannerInterfaceFactory</comment>');
        $output->writeln('   - Creates NEW Banner instances dynamically');
        $output->writeln('   - Auto-generated by Magento (not written manually)');
        $output->writeln('   - Used for: loops, cloning, dynamic creation');
        $output->writeln('');
        $output->writeln('<comment>2. CollectionFactory</comment>');
        $output->writeln('   - Creates FRESH Collection instances');
        $output->writeln('   - Each create() returns clean slate (no filter pollution)');
        $output->writeln('   - Used for: independent queries, statistics');
        $output->writeln('');
        $output->writeln('<comment>3. Service Layer Pattern</comment>');
        $output->writeln('   - Business logic in Service classes');
        $output->writeln('   - Reusable across controllers, commands, APIs');
        $output->writeln('   - Testable and maintainable');
        $output->writeln('');
        $output->writeln('<info>Pattern demonstration complete!</info>');
        $output->writeln('');

        return Command::SUCCESS;
    }
}
