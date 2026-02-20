<?php

/**
 * Script to identify and remove duplicate Desa entries
 * Run with: php artisan check:duplicate-desa
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Desa;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class CheckDuplicateDesa extends Command
{
    protected $signature = 'check:duplicate-desa {--delete : Actually delete duplicates}';
    protected $description = 'Check for duplicate Desa entries and optionally remove them';

    public function handle()
    {
        $this->info('Checking for duplicate Desa entries...');
        $this->newLine();

        // Get all desa grouped by name
        $duplicates = DB::table('desa')
            ->select('nama_desa', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id ORDER BY id) as ids'))
            ->groupBy('nama_desa')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate desa entries found!');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} duplicate desa names:");
        $this->newLine();

        $table = [];
        $idsToDelete = [];

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $keepId = $ids[0]; // Keep the first one (oldest)
            $deleteIds = array_slice($ids, 1); // Delete the rest

            // Check if any users or submissions are linked to the duplicates
            $linkedUsers = User::whereIn('desa_id', $deleteIds)->count();
            $linkedSubmissions = Submission::whereIn('desa_id', $deleteIds)->count();

            $table[] = [
                'Nama Desa' => $duplicate->nama_desa,
                'Jumlah' => $duplicate->count,
                'IDs' => $duplicate->ids,
                'Keep ID' => $keepId,
                'Delete IDs' => implode(', ', $deleteIds),
                'Users Linked' => $linkedUsers,
                'Submissions Linked' => $linkedSubmissions,
            ];

            if ($this->option('delete')) {
                $idsToDelete = array_merge($idsToDelete, $deleteIds);
            }
        }

        $this->table([
            'Nama Desa',
            'Jumlah',
            'IDs',
            'Keep ID',
            'Delete IDs',
            'Users Linked',
            'Submissions Linked',
        ], $table);

        if (!$this->option('delete')) {
            $this->newLine();
            $this->warn('⚠️  This is a DRY RUN. No data will be deleted.');
            $this->info('To actually delete duplicates, run: php artisan check:duplicate-desa --delete');
            $this->newLine();
            $this->warn('⚠️  WARNING: Before deleting, make sure to:');
            $this->warn('1. Check if any Users or Submissions are linked to the duplicate IDs');
            $this->warn('2. If there are links, migrate them to the "Keep ID" first');
            return 0;
        }

        // Confirm deletion
        if (!$this->confirm('Are you sure you want to delete ' . count($idsToDelete) . ' duplicate desa entries?')) {
            $this->info('Deletion cancelled.');
            return 0;
        }

        // Check for linked data
        $hasLinkedData = false;
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $deleteIds = array_slice($ids, 1);

            $linkedUsers = User::whereIn('desa_id', $deleteIds)->count();
            $linkedSubmissions = Submission::whereIn('desa_id', $deleteIds)->count();

            if ($linkedUsers > 0 || $linkedSubmissions > 0) {
                $hasLinkedData = true;
                $this->error("Cannot delete {$duplicate->nama_desa}: Has {$linkedUsers} users and {$linkedSubmissions} submissions linked!");
            }
        }

        if ($hasLinkedData) {
            $this->error('⛔ Cannot proceed with deletion. Please migrate linked data first.');
            return 1;
        }

        // Safe to delete
        DB::beginTransaction();
        try {
            $deleted = Desa::whereIn('id', $idsToDelete)->delete();
            DB::commit();

            $this->info("✅ Successfully deleted {$deleted} duplicate desa entries!");
            $this->newLine();
            $this->info('Remaining desa entries:');

            $remaining = Desa::orderBy('nama_desa')->get(['id', 'nama_desa']);
            $this->table(['ID', 'Nama Desa'], $remaining->map(fn($d) => [$d->id, $d->nama_desa])->toArray());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Failed to delete duplicates: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
