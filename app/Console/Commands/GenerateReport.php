<?php

namespace App\Console\Commands;

use App\Clockify;
use Illuminate\Console\Command;


class GenerateReport extends Command
{
    protected $signature = 'report:generate';

    protected $description = 'Generates Clockify Report';

    public function handle(Clockify $clockify)
    {
        $this->info($clockify->generateReport());
    }
}
