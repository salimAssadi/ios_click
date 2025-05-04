<?php

namespace Modules\Reminder\Console\Commands;

use Illuminate\Console\Command;
use Modules\Document\Entities\DocumentVersion;
use Modules\Reminder\Services\ReminderService;
use Illuminate\Support\Facades\Log;

class MigrateDocumentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:migrate-document-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing document reminders to the new reminder system';

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
        $this->info('Starting migration of document reminders to the new system...');
        
        $documentVersions = DocumentVersion::whereNotNull('expiry_date')
            ->whereNotNull('reminder_days')
            ->where('is_active', true)
            ->get();
            
        $this->info("Found {$documentVersions->count()} document versions with reminders to migrate.");
        
        $migrated = 0;
        $errors = 0;
        
        foreach ($documentVersions as $version) {
            try {
                $this->info("Processing document version ID {$version->id}...");
                
                if ($version->migrateToNewReminderSystem()) {
                    $migrated++;
                    $this->info("Successfully migrated reminder for document version ID {$version->id}");
                }
            } catch (\Exception $e) {
                $errors++;
                $errorMsg = "Error migrating reminder for document version ID {$version->id}: " . $e->getMessage();
                $this->error($errorMsg);
                Log::error($errorMsg);
            }
        }
        
        $this->info("Migration completed. Successfully migrated reminders: {$migrated}. Errors: {$errors}.");
        
        return 0;
    }
}
