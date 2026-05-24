<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CreateUserBackupJob;
use App\Models\UserBackup;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backupService)
    {
    }

    public function index(): JsonResponse
    {
        $backups = UserBackup::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->get();

        return response()->json([
            'backups' => $backups,
        ]);
    }

    public function store(): JsonResponse
    {
        $backup = $this->backupService->createPendingFullBackup(auth()->user());
        CreateUserBackupJob::dispatch($backup->id);

        return response()->json([
            'message' => 'Backup job queued successfully.',
            'backup' => $backup,
        ], 202);
    }

    public function restore(UserBackup $backup): JsonResponse
    {
        if ($backup->user_id !== auth()->id()) {
            abort(403);
        }

        if ($backup->status !== 'completed') {
            return response()->json([
                'message' => 'Only completed backups can be restored.',
            ], 422);
        }

        $restoredCounts = $this->backupService->restore(auth()->user(), $backup);

        return response()->json([
            'message' => 'Backup restored successfully.',
            'restored_counts' => $restoredCounts,
        ]);
    }
}
