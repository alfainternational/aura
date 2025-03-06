<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileHelper
{
    /**
     * حفظ ملف مرفوع
     *
     * @param UploadedFile $file الملف المرفوع
     * @param string $directory المجلد
     * @param string|null $filename اسم الملف (اختياري)
     * @param string $disk القرص (public, local, s3)
     * @return string مسار الملف
     */
    public static function saveFile(UploadedFile $file, string $directory, ?string $filename = null, string $disk = 'public')
    {
        $filename = $filename ?? Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }
    
    /**
     * حفظ صورة مع تغيير حجمها
     *
     * @param UploadedFile $image الصورة المرفوعة
     * @param string $directory المجلد
     * @param int $width العرض
     * @param int $height الارتفاع
     * @param string|null $filename اسم الملف (اختياري)
     * @param string $disk القرص (public, local, s3)
     * @return string مسار الصورة
     */
    public static function saveImage(UploadedFile $image, string $directory, int $width, int $height, ?string $filename = null, string $disk = 'public')
    {
        $filename = $filename ?? Str::uuid() . '.' . $image->getClientOriginalExtension();
        $fullPath = storage_path('app/public/' . $directory . '/' . $filename);
        
        // إنشاء المجلد إذا لم يكن موجودًا
        if (!file_exists(storage_path('app/public/' . $directory))) {
            mkdir(storage_path('app/public/' . $directory), 0755, true);
        }
        
        // تغيير حجم الصورة وحفظها
        Image::make($image->getRealPath())
            ->fit($width, $height)
            ->save($fullPath);
            
        return $directory . '/' . $filename;
    }
    
    /**
     * حذف ملف
     *
     * @param string|null $path مسار الملف
     * @param string $disk القرص (public, local, s3)
     * @return bool
     */
    public static function deleteFile(?string $path, string $disk = 'public')
    {
        if (!$path) {
            return false;
        }
        
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }
    
    /**
     * الحصول على URL للملف
     *
     * @param string|null $path مسار الملف
     * @param string $disk القرص (public, local, s3)
     * @return string|null
     */
    public static function getFileUrl(?string $path, string $disk = 'public')
    {
        if (!$path) {
            return null;
        }
        
        if ($disk === 'public') {
            return asset('storage/' . $path);
        }
        
        return Storage::disk($disk)->url($path);
    }
    
    /**
     * التحقق من نوع الملف
     *
     * @param UploadedFile $file الملف المرفوع
     * @param array $allowedTypes الأنواع المسموح بها
     * @return bool
     */
    public static function isValidFileType(UploadedFile $file, array $allowedTypes)
    {
        return in_array($file->getClientMimeType(), $allowedTypes);
    }
    
    /**
     * التحقق من حجم الملف
     *
     * @param UploadedFile $file الملف المرفوع
     * @param int $maxSize الحجم الأقصى بالكيلوبايت
     * @return bool
     */
    public static function isValidFileSize(UploadedFile $file, int $maxSize)
    {
        return $file->getSize() <= $maxSize * 1024;
    }
}
