<?php

namespace App\Jobs;

use App\Models\UserBackup;
use App\Services\BackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateUserBackupJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $backupId)
    {
    }

    public function handle(BackupService $backupService): void
    {
        $backup = UserBackup::query()->find($this->backupId);

        if (! $backup) {
            return;
        }

        $backupService->processBackup($backup);
    }
}
