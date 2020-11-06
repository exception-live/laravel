<?php

namespace ExceptionLive\ExceptionLiveLaravel\Commands;

use Exception;
use ExceptionLive\ExceptionLive;
use Illuminate\Console\Command;

class ExceptionLiveCheckinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exceptionlive:checkin {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send check-in to ExceptionLive';

    /**
     * Execute the console command.
     *
     * @param ExceptionLive $exceptionLive
     * @return mixed
     */
    public function handle(ExceptionLive $exceptionLive)
    {
        try {
            $exceptionLive->checkin($this->apiKey());
            $this->info(sprintf('Checkin %s was sent to ExceptionLive', $this->argument('id')));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the API key from input.
     *
     * @return string
     */
    private function apiKey(): string
    {
        return is_array($this->argument('id'))
            ? $this->argument('id')[0]
            : $this->argument('id');
    }
}
