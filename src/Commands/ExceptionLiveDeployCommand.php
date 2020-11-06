<?php

namespace ExceptionLive\ExceptionLiveLaravel\Commands;

use Exception;
use ExceptionLive\ExceptionLive;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ExceptionLiveDeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exceptionlive:deploy {--branch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deployment to ExceptionLive';

    /**
     * Execute the console command.
     *
     * @param ExceptionLive $exceptionLive
     * @return mixed
     * @throws \ExceptionLive\Exceptions\Exception
     */
    public function handle(ExceptionLive $exceptionLive)
    {
        $result = $exceptionLive->deployNotification($this->option('branch') ?? $this->gitHash());

        if ($result['status'] !== 'success') {
            throw new \Exception(vsprintf('Sending the deployment to ExceptionLive failed. Response %s.', [
                (string) $result,
            ]));
        }

        $this->info('Deployment successfully sent');
    }

    /**
     * @return string
     */
    private function gitHash(): string
    {
        return trim(exec('git log --pretty="%h" -n1 HEAD'));
    }
}
