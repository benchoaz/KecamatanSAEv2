<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ModuleSetting;

class GetSettingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setting:get {module} {key} {--default=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a setting value from ModuleSetting (useful for bash scripts)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $key = $this->argument('key');
        $default = $this->option('default');

        $value = ModuleSetting::getValue($module, $key, $default);

        // Hanya output nilainya secara murni agar mudah ditangkap oleh Bash script
        echo $value;

        return Command::SUCCESS;
    }
}
