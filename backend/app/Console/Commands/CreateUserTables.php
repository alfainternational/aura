<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-user-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user profile tables if they do not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating user profile tables if they do not exist...');
        
        // Create admin_profiles table
        if (!Schema::hasTable('admin_profiles')) {
            $this->info('Creating admin_profiles table...');
            
            Schema::create('admin_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('position')->nullable();
                $table->string('department')->nullable();
                $table->text('bio')->nullable();
                $table->timestamps();
            });
            
            $this->info('admin_profiles table created successfully.');
        } else {
            $this->info('admin_profiles table already exists.');
        }
        
        // Create customer_profiles table
        if (!Schema::hasTable('customer_profiles')) {
            $this->info('Creating customer_profiles table...');
            
            Schema::create('customer_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->timestamps();
            });
            
            $this->info('customer_profiles table created successfully.');
        } else {
            $this->info('customer_profiles table already exists.');
        }
        
        // Create merchant_profiles table
        if (!Schema::hasTable('merchant_profiles')) {
            $this->info('Creating merchant_profiles table...');
            
            Schema::create('merchant_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('business_name')->nullable();
                $table->string('business_type')->nullable();
                $table->string('business_address')->nullable();
                $table->string('business_city')->nullable();
                $table->string('tax_number')->nullable();
                $table->timestamps();
            });
            
            $this->info('merchant_profiles table created successfully.');
        } else {
            $this->info('merchant_profiles table already exists.');
        }
        
        // Create agent_profiles table
        if (!Schema::hasTable('agent_profiles')) {
            $this->info('Creating agent_profiles table...');
            
            Schema::create('agent_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('agency_name')->nullable();
                $table->string('agency_address')->nullable();
                $table->string('agency_city')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(0);
                $table->timestamps();
            });
            
            $this->info('agent_profiles table created successfully.');
        } else {
            $this->info('agent_profiles table already exists.');
        }
        
        // Create messenger_profiles table
        if (!Schema::hasTable('messenger_profiles')) {
            $this->info('Creating messenger_profiles table...');
            
            Schema::create('messenger_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('vehicle_type')->nullable();
                $table->string('vehicle_plate')->nullable();
                $table->foreignId('zone_id')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
            
            $this->info('messenger_profiles table created successfully.');
        } else {
            $this->info('messenger_profiles table already exists.');
        }
        
        $this->info('All user profile tables have been created successfully!');
    }
}
