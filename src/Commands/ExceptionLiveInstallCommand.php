<?php

namespace ExceptionLive\ExceptionLiveLaravel\Commands;

use ExceptionLive\ExceptionLiveLaravel\CommandTasks;
use ExceptionLive\ExceptionLiveLaravel\Concerns\RequiredInput;
use ExceptionLive\ExceptionLiveLaravel\Contracts\Installer;
use ExceptionLive\ExceptionLiveLaravel\Exceptions\TaskFailed;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use ExceptionLive\Exceptions\Exception as ServiceException;

class ExceptionLiveInstallCommand extends Command
{
    use RequiredInput;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exceptionlive:install {apiKey?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure ExceptionLive';

    /**
     * Configuration from gathered input.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var Installer;
     */
    protected $installer;

    /**
     * @var CommandTasks
     */
    protected $tasks;

    /**
     * Execute the console command.
     *
     * @param Installer $installer
     * @param CommandTasks $commandTasks
     * @return mixed
     * @throws \ExceptionLive\ExceptionLiveLaravel\Exceptions\TaskFailed
     */
    public function handle(Installer $installer, CommandTasks $commandTasks)
    {
        $this->installer = $installer;
        $this->tasks = $commandTasks;
        $this->tasks->setOutput($this->output);

        $this->config = $this->gatherConfig();

        $this->writeEnv();

        if ($this->installer->shouldPublishConfig()) {
            $this->tasks->addTask(
                'Publish the config file',
                function () {
                    return $this->publishConfig();
                }
            );
        }

        $results = $this->sendTest();

        try {
            $this->tasks->runTasks();
            $this->outputSuccessMessage(Arr::get($results ?? [], 'id', ''));
        } catch (TaskFailed $e) {
            $this->line('');
            $this->error($e->getMessage());
        }
    }

    /**
     * Prompt for input and gather responses.
     *
     * @return array
     */
    private function gatherConfig(): array
    {
        return [
            'api_key' => $this->argument('apiKey') ?? $this->promptForApiKey(),
        ];
    }

    /**
     * Prompt for the API key.
     *
     * @return string
     */
    private function promptForApiKey(): string
    {
        return $this->requiredSecret('Your API key', 'The API key is required');
    }

    /**
     * Send test exception to ExceptionLive.
     *
     * @return array
     */
    private function sendTest(): array
    {
        Config::set('exceptionlive.api_key', $this->config['api_key']);

        try {
            $result = $this->installer->sendTestException();
        } catch (ServiceException $e) {
            $result = [];
        }

        $this->tasks->addTask(
            'Send test exception to ExceptionLive',
            function () use ($result) {
                return ! empty($result);
            }
        );

        return $result;
    }

    /**
     * Write configuration values to the env files.
     *
     * @return void
     */
    private function writeEnv(): void
    {
        $this->tasks->addTask(
            'Write EXCEPTION_LIVE_API_KEY to .env',
            function () {
                return $this->installer->writeConfig(
                    ['EXCEPTION_LIVE_API_KEY' => $this->config['api_key']],
                    base_path('.env')
                );
            }
        );

        $this->tasks->addTask(
            'Write EXCEPTION_LIVE_API_KEY placeholder to .env.example',
            function () {
                return $this->installer->writeConfig(
                    ['EXCEPTION_LIVE_API_KEY' => ''],
                    base_path('.env.example')
                );
            }
        );
    }

    /**
     * Publish the config file for Lumen or Laravel.
     *
     * @return bool
     */
    public function publishConfig(): bool
    {
        if (app('exceptionlive.isLumen')) {
            return $this->installer->publishLumenConfig();
        }

        return $this->installer->publishLaravelConfig();
    }

    /**
     * Output the success message.
     *
     * @param  string  $noticeId
     * @return void
     */
    private function outputSuccessMessage(string $noticeId): void
    {
        $this->line(SuccessMessage::make($noticeId));
    }
}
