<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncProductionDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync-production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync production PostgreSQL database to local SQLite database via SSH tunnel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database synchronization from production PostgreSQL to local SQLite...');

        // Test the production connection
        try {
            DB::connection('production_pgsql')->getPdo();
            $this->info('✓ Successfully connected to remote PostgreSQL via tunnel!');
        } catch (\Exception $e) {
            $this->error('Failed to connect to production PostgreSQL. Make sure the SSH tunnel is active on port 5433.');
            $this->error('Error details: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Disable foreign keys for SQLite
        $this->info('Disabling foreign key constraints in SQLite...');
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Tables to clear and sync
        $tables = [
            'categories',
            'users',
            'items',
            'item_photos',
            'claims',
            'personal_access_tokens',
        ];

        foreach ($tables as $table) {
            $this->info("Processing table: {$table}...");

            // 1. Truncate local table
            DB::table($table)->truncate();
            $this->info("  ✓ Local table '{$table}' truncated.");

            // 2. Fetch from production PGSQL
            $rows = DB::connection('production_pgsql')->table($table)->get();
            $count = $rows->count();
            $this->info("  ✓ Fetched {$count} records from production.");

            if ($count === 0) {
                continue;
            }

            // Convert collection of stdClass objects to arrays
            $insertData = [];
            foreach ($rows as $row) {
                $arrayRow = (array) $row;

                // Specific adjustment for users table (handling role field)
                if ($table === 'users') {
                    // Check if 'role' field is missing from remote row and default it
                    if (!isset($arrayRow['role'])) {
                        // Check if this is the seeded admin email or contains certain strings
                        if ($arrayRow['email'] === 'muhammad.nuha23@student.uisi.ac.id' || str_contains($arrayRow['email'], 'admin')) {
                            $arrayRow['role'] = 'admin';
                        } else {
                            $arrayRow['role'] = 'user';
                        }
                    }
                }

                // SQLite does not support native boolean types directly in PGSQL format if driver behaves differently.
                // But Laravel PDO pgsql returns numeric/string/boolean. Let's make sure boolean values are cast to 1 or 0 for SQLite.
                foreach ($arrayRow as $key => $value) {
                    if (is_bool($value)) {
                        $arrayRow[$key] = $value ? 1 : 0;
                    }
                }

                $insertData[] = $arrayRow;
            }

            // 3. Bulk insert to local SQLite
            // SQLite has a maximum variable parameter limit (typically 999 or 32766 depending on compilation).
            // To prevent "Too many SQL variables" error in SQLite, we insert in chunks of 50.
            $chunks = array_chunk($insertData, 50);
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
            }
            $this->info("  ✓ Successfully inserted {$count} records into local SQLite.");
        }

        // Re-enable foreign keys
        $this->info('Re-enabling foreign key constraints in SQLite...');
        DB::statement('PRAGMA foreign_keys = ON;');

        // Verify some data counts
        $userCount = DB::table('users')->count();
        $itemCount = DB::table('items')->count();
        $claimCount = DB::table('claims')->count();

        $this->newLine();
        $this->info('==================================================');
        $this->info('🎉 Sync Complete!');
        $this->info("Total Users in SQLite: {$userCount}");
        $this->info("Total Items in SQLite: {$itemCount}");
        $this->info("Total Claims in SQLite: {$claimCount}");
        $this->info('==================================================');

        return Command::SUCCESS;
    }
}
