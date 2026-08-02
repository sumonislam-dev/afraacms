<?php

namespace App\Console\Commands;

use App\CMS\Cache\CmsCacheManager;
use Illuminate\Console\Command;

class ClearCmsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear every registered CMS cache layer (settings, and future modules as they register)';

    /**
     * Execute the console command.
     */
    public function handle(CmsCacheManager $cache): int
    {
        $cleared = $cache->clear();

        if (empty($cleared)) {
            $this->warn('No CMS cache layers are registered.');

            return self::SUCCESS;
        }

        $this->info('Cleared CMS cache: '.implode(', ', $cleared));

        return self::SUCCESS;
    }
}
