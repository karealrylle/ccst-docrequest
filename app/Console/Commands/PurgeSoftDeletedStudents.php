<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PurgeSoftDeletedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-soft-deleted-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted students after 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting purge of soft-deleted students...');

        $count = User::onlyTrashed()
            ->where('role', 'student')
            ->where('deleted_at', '<=', now()->subDays(30))
            ->forceDelete();

        $this->info("Successfully purged {$count} student(s) who were deleted more than 30 days ago.");
    }
}
