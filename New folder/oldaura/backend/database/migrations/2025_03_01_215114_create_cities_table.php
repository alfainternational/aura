<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع عن المدن حسب الدولة
            $table->index('country_id');
        });
        
        // إضافة بعض المدن الأساسية في السودان
        $sudanId = DB::table('countries')->where('code', 'SD')->value('id');
        
        if ($sudanId) {
            $cities = [
                ['name' => 'الخرطوم', 'latitude' => 15.5007, 'longitude' => 32.5599],
                ['name' => 'أم درمان', 'latitude' => 15.6445, 'longitude' => 32.4777],
                ['name' => 'بحري', 'latitude' => 15.6480, 'longitude' => 32.5363],
                ['name' => 'بورتسودان', 'latitude' => 19.6160, 'longitude' => 37.2161],
                ['name' => 'كسلا', 'latitude' => 15.4517, 'longitude' => 36.4043],
                ['name' => 'الأبيض', 'latitude' => 13.1629, 'longitude' => 30.2168],
                ['name' => 'نيالا', 'latitude' => 12.0490, 'longitude' => 24.8800],
                ['name' => 'الفاشر', 'latitude' => 13.6303, 'longitude' => 25.3565],
                ['name' => 'عطبرة', 'latitude' => 17.7022, 'longitude' => 33.9864],
                ['name' => 'ود مدني', 'latitude' => 14.4000, 'longitude' => 33.5167],
            ];
            
            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'name' => $city['name'],
                    'country_id' => $sudanId,
                    'is_active' => true,
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
