<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportDatabaseSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import database schema from SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will drop all existing tables and import a new schema. Are you sure you want to continue?', true)) {
            $this->info('Operation cancelled.');
            return;
        }

        // تحديد مسار ملف SQL
        $sqlFile = base_path('../-- إنشاء قاعدة البيانات.sql');

        if (!File::exists($sqlFile)) {
            $this->error('SQL file not found at: ' . $sqlFile);
            return;
        }

        $this->info('Reading SQL file...');
        $sql = File::get($sqlFile);

        // تقسيم الملف إلى أوامر SQL منفصلة
        $statements = $this->splitSqlFile($sql);
        
        $this->info('Found ' . count($statements) . ' SQL statements.');
        
        $this->info('Dropping all existing tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // الحصول على جميع الجداول
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::connection()->getDatabaseName();
        
        foreach ($tables as $table) {
            $tableName = "Tables_in_$dbName";
            DB::statement("DROP TABLE IF EXISTS `{$table->$tableName}`");
            $this->info("Dropped table: {$table->$tableName}");
        }
        
        $this->info('All tables dropped successfully.');
        
        $this->info('Importing schema...');
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            try {
                DB::statement($statement);
                $successCount++;
                
                // عرض النسبة المئوية للتقدم
                $percentage = round(($successCount / count($statements)) * 100);
                $this->output->write("\rProgress: $percentage% ($successCount/" . count($statements) . ")");
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("\nError executing statement: " . $e->getMessage());
                $this->line("Statement: " . substr($statement, 0, 100) . '...');
            }
        }
        
        $this->info("\nImport completed: $successCount statements executed successfully, $errorCount errors.");
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // إضافة البيانات الأساسية
        $this->info('Adding basic data...');
        
        // إضافة دولة السودان
        try {
            DB::table('countries')->insert([
                'name' => 'Sudan',
                'code' => 'SD',
                'dial_code' => '+249',
                'currency' => 'SDG',
                'currency_symbol' => 'ج.س',
                'active' => 1,
                'allowed_for_registration' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info('Added Sudan to countries table.');
        } catch (\Exception $e) {
            $this->error('Error adding Sudan to countries table: ' . $e->getMessage());
        }
        
        // إضافة دولة السعودية
        try {
            DB::table('countries')->insert([
                'name' => 'Saudi Arabia',
                'code' => 'SA',
                'dial_code' => '+966',
                'currency' => 'SAR',
                'currency_symbol' => 'ر.س',
                'active' => 1,
                'allowed_for_registration' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info('Added Saudi Arabia to countries table.');
        } catch (\Exception $e) {
            $this->error('Error adding Saudi Arabia to countries table: ' . $e->getMessage());
        }
        
        // إضافة مستخدم مسؤول
        try {
            $adminId = DB::table('users')->insertGetId([
                'firstname' => 'Admin',
                'lastname' => 'User',
                'username' => 'admin',
                'email' => 'admin@aura.com',
                'password' => bcrypt('admin123'),
                'user_type' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // إضافة ملف تعريف المسؤول
            DB::table('admin_profiles')->insert([
                'user_id' => $adminId,
                'department' => 'IT',
                'position' => 'System Administrator',
                'access_level' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info('Added admin user (admin@aura.com / admin123)');
        } catch (\Exception $e) {
            $this->error('Error adding admin user: ' . $e->getMessage());
        }
        
        $this->info('Database setup completed.');
    }
    
    /**
     * تقسيم محتوى ملف SQL إلى جمل منفصلة
     * 
     * @param string $sql محتوى ملف SQL
     * @return array قائمة بجميع جمل SQL
     */
    private function splitSqlFile($sql)
    {
        $statements = [];
        $currentStatement = '';
        $delimiter = ';';
        
        // تقسيم الملف إلى سطور
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // تجاهل التعليقات والأسطر الفارغة
            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '#') === 0) {
                continue;
            }
            
            // إذا كان هناك تغيير للمحدد
            if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
                $delimiter = $matches[1];
                continue;
            }
            
            // إضافة السطر إلى الجملة الحالية
            $currentStatement .= ' ' . $line;
            
            // التحقق مما إذا كانت الجملة قد انتهت
            if (substr($line, -strlen($delimiter)) === $delimiter) {
                // إزالة المحدد من نهاية الجملة
                $currentStatement = substr($currentStatement, 0, strlen($currentStatement) - strlen($delimiter));
                $currentStatement = trim($currentStatement);
                
                // إضافة الجملة إلى القائمة إذا لم تكن فارغة
                if (!empty($currentStatement)) {
                    $statements[] = $currentStatement;
                }
                
                // إعادة تعيين الجملة الحالية
                $currentStatement = '';
            }
        }
        
        // التحقق من وجود جملة أخيرة
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }
        
        return $statements;
    }
}
