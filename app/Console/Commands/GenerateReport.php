<?php

namespace App\Console\Commands;

use App\Clockify;
use Illuminate\Console\Command;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Clockify Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Clockify $clockify)
    {
        $this->line($clockify->generateReport());

        return Command::SUCCESS;
    }
}
