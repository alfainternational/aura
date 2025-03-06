<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateCountriesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-countries-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create countries table if it does not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking if countries table exists...');
        
        if (!Schema::hasTable('countries')) {
            $this->info('Creating countries table...');
            
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 2)->unique();
                $table->string('flag')->nullable();
                $table->string('phone_code')->nullable();
                $table->string('currency')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('allow_registration')->default(false);
                $table->text('registration_message')->nullable();
                $table->timestamps();
            });
            
            $this->info('Countries table created successfully.');
            
            // Add Sudan as default country
            DB::table('countries')->insert([
                'name' => 'Sudan',
                'code' => 'SD',
                'phone_code' => '+249',
                'currency' => 'SDG',
                'is_active' => true,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info('Added Sudan as default country.');
        } else {
            $this->info('Countries table already exists.');
        }
    }
}
