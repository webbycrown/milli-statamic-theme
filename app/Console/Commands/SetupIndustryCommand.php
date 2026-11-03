<?php

namespace App\Console\Commands;

use App\Support\MilliIndustry;
use Illuminate\Console\Command;

class SetupIndustryCommand extends Command
{
    protected $signature = 'milli:setup {industry : original|fashion|menswear|womenswear|kids|streetwear|jewellery|beauty|accessories|homeware} {--force : Apply without asking}';

    protected $description = 'Apply a Milli catalogue demo pack (brand colour, site name, home hero).';

    public function handle(): int
    {
        $industry = strtolower((string) $this->argument('industry'));
        if (! in_array($industry, MilliIndustry::SLUGS, true)) {
            $this->error('Catalogue must be one of: '.implode(', ', MilliIndustry::SLUGS));

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm(
            "Apply the {$industry} Milli catalogue pack (site name, brand colour, home hero)?",
            false
        )) {
            $this->warn('Cancelled. Settings were not changed.');

            return self::FAILURE;
        }

        if (! MilliIndustry::apply($industry)) {
            $this->error('Could not apply the '.$industry.' demo pack.');

            return self::FAILURE;
        }

        $this->info("Milli catalogue set to {$industry}. Brand colour and labels updated.");

        return self::SUCCESS;
    }
}
