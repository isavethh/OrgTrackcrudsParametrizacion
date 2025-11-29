<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupDatabase extends Command
{
    protected $signature = 'db:setup-schema';
    protected $description = 'Setup database schema from SQL file';

    public function handle()
    {
        $sqlFile = database_path('schema.sql');
        
        if (!file_exists($sqlFile)) {
            $this->error('SQL file not found!');
            return 1;
        }

        $sql = file_get_contents($sqlFile);
        
        try {
            DB::unprepared($sql);
            $this->info('✅ Database schema created successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error executing SQL: ' . $e->getMessage());
            return 1;
        }
    }
}
