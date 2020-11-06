<?php

namespace ExceptionLive\ExceptionLiveLaravel\Commands;

use Exception;
use ExceptionLive\ExceptionLive;
use ExceptionLive\ExceptionLiveLaravel\Exceptions\TestException;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ExceptionLiveTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exceptionlive:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests notifications to ExceptionLive';

    /**
     * Execute the console command.
     *
     * @param ExceptionLive $exceptionLive
     * @return void
     */
    public function handle(ExceptionLive $exceptionLive)
    {
        try {
            $result = $exceptionLive->notify(new TestException);
            $this->info('A test exception was sent to ExceptionLive');

            if (is_null(Arr::get($result, 'id'))) {
                throw new Exception('There was an error sending the exception to ExceptionLive');
            }

            $this->line(sprintf('https://exception.live/report/', Arr::get($result, 'id')));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
