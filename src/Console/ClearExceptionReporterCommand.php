<?php

namespace Weiaibaicai\DcatExceptionReporter\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Weiaibaicai\DcatExceptionReporter\Models\ExceptionReporter;

class ClearExceptionReporterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exception-reporter:clear {--date= : 日期,格式为 Y-m-d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除异常表的异常记录。例子：删除一周前的异常执行“php artisan exception-reporter:clear”命令；删除指定日期前的异常“php artisan exception-reporter:clear 2022-01-14”命令';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date');

        if (!empty($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = Carbon::today()->subWeek();
        }

        if (is_null($date)) {
            $date = Carbon::now();
        }

        ExceptionReporter::query()->where('created_at', '<=', $date->toDateTimeString())->delete();

        $this->info('清除完成 ^_^^_^');
    }
}
