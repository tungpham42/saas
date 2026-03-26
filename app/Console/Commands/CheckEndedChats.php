<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckEndedChats extends Command
{
    protected $signature = 'saas:check-ended-chats';
    protected $description = 'Check for ended chat sessions and send email notifications';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Checking for ended chat sessions...');

        $this->notificationService->processEndedChats();

        $this->info('Notification check completed.');

        return Command::SUCCESS;
    }
}
