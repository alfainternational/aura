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
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
        
        // إضافة مدن السودان
        $sudanId = DB::table('countries')->where('code', 'SD')->value('id');
        
        if ($sudanId) {
            $cities = [
                ['name' => 'Khartoum', 'name_ar' => 'الخرطوم', 'is_default' => true, 'latitude' => 15.5007, 'longitude' => 32.5599],
                ['name' => 'Omdurman', 'name_ar' => 'أم درمان', 'latitude' => 15.6445, 'longitude' => 32.4777],
                ['name' => 'Bahri', 'name_ar' => 'بحري', 'latitude' => 15.6326, 'longitude' => 32.5265],
                ['name' => 'Port Sudan', 'name_ar' => 'بورتسودان', 'latitude' => 19.6158, 'longitude' => 37.2164],
                ['name' => 'Kassala', 'name_ar' => 'كسلا', 'latitude' => 15.4517, 'longitude' => 36.4042],
                ['name' => 'Al-Ubayyid', 'name_ar' => 'الأبيض', 'latitude' => 13.1867, 'longitude' => 30.2167],
                ['name' => 'Nyala', 'name_ar' => 'نيالا', 'latitude' => 12.0489, 'longitude' => 24.8769],
                ['name' => 'Wad Madani', 'name_ar' => 'ود مدني', 'latitude' => 14.4012, 'longitude' => 33.5199],
            ];
            
            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'country_id' => $sudanId,
                    'name' => $city['name'],
                    'name_ar' => $city['name_ar'],
                    'is_active' => true,
                    'is_default' => $city['is_default'] ?? false,
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // إضافة مدن السعودية
        $saudiId = DB::table('countries')->where('code', 'SA')->value('id');
        
        if ($saudiId) {
            $cities = [
                ['name' => 'Riyadh', 'name_ar' => 'الرياض', 'is_default' => true, 'latitude' => 24.7136, 'longitude' => 46.6753],
                ['name' => 'Jeddah', 'name_ar' => 'جدة', 'latitude' => 21.4858, 'longitude' => 39.1925],
                ['name' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'latitude' => 21.3891, 'longitude' => 39.8579],
                ['name' => 'Medina', 'name_ar' => 'المدينة المنورة', 'latitude' => 24.5247, 'longitude' => 39.5692],
                ['name' => 'Dammam', 'name_ar' => 'الدمام', 'latitude' => 26.4207, 'longitude' => 50.0888],
            ];
            
            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'country_id' => $saudiId,
                    'name' => $city['name'],
                    'name_ar' => $city['name_ar'],
                    'is_active' => true,
                    'is_default' => $city['is_default'] ?? false,
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
