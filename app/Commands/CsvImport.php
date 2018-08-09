<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CsvImport extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'csv:import';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Read CSV file and prepare format';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->task("Transforming all CSVs", function () {
            collect(array_values(array_diff(scandir(base_path('files')), ['..', '.'])))->each(function ($file) {
                $this->task("Transforming " . $file, function () use ($file) {
                    $importFile = fopen(base_path('files/' . $file), 'r');
                    $newFile = fopen(base_path('exports/' . $file), 'w+');
                    while (($data = fgetcsv($importFile, 0, ";")) !== false) {
                        fputs($newFile, implode(";", array_map(function ($column) {
                            $column = str_replace('\\"', '"', $column);
                            $column = str_replace('"', '\"', $column);
                            $column = str_replace(',', '.', $column);
                            return '"'.$column.'"';
                        }, $data)) . "\r\n");
                    }
                    fclose($newFile);
                    fclose($importFile);
                });
                // unlink(base_path('files/' . $file));
            });
            return true;
        });
    }



    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
