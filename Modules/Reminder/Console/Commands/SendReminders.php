<?php

namespace Modules\Reminder\Console\Commands;

use Illuminate\Console\Command;
use Modules\Reminder\Services\ReminderService;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all due reminders';

    /**
     * The reminder service.
     *
     * @var \Modules\Reminder\Services\ReminderService
     */
    protected $reminderService;

    /**
     * Create a new command instance.
     *
     * @param \Modules\Reminder\Services\ReminderService $reminderService
     * @return void
     */
    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting to process due reminders...');
        
        try {
            $stats = $this->reminderService->processDueReminders();
            
            $this->info("Reminders processed: {$stats['processed']}");
            $this->info("Reminders sent: {$stats['sent']}");
            $this->info("Reminders rescheduled: {$stats['rescheduled']}");
            
            if ($stats['errors'] > 0) {
                $this->warn("Errors encountered: {$stats['errors']}");
            }
            
            Log::info("Reminder processing completed: processed={$stats['processed']}, sent={$stats['sent']}, rescheduled={$stats['rescheduled']}, errors={$stats['errors']}");
        } catch (\Exception $e) {
            $this->error("Error processing reminders: " . $e->getMessage());
            Log::error("Error processing reminders: " . $e->getMessage());
            return 1;
        }
        
        $this->info('Reminder processing completed.');
        return 0;
    }
}
