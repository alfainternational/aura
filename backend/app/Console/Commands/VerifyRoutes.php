<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VerifyRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all routes used in views and controllers are properly defined';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Verifying routes used in views and controllers...');
        
        // الحصول على جميع المسارات المعرفة في النظام
        $definedRoutes = collect(Route::getRoutes())->map(function ($route) {
            return $route->getName();
        })->filter()->toArray();
        
        $this->info('Found ' . count($definedRoutes) . ' defined routes in the application.');
        
        // البحث عن المسارات المستخدمة في ملفات القوالب
        $viewsPath = resource_path('views');
        $usedRoutesInViews = $this->findRoutesInFiles($viewsPath, '*.blade.php');
        
        // البحث عن المسارات المستخدمة في ملفات الكونترولر
        $controllersPath = app_path('Http/Controllers');
        $usedRoutesInControllers = $this->findRoutesInFiles($controllersPath, '*.php');
        
        // دمج المسارات المستخدمة
        $usedRoutes = array_unique(array_merge($usedRoutesInViews, $usedRoutesInControllers));
        
        $this->info('Found ' . count($usedRoutes) . ' routes used in views and controllers.');
        
        // التحقق من المسارات المفقودة
        $missingRoutes = array_diff($usedRoutes, $definedRoutes);
        
        if (count($missingRoutes) > 0) {
            $this->error('Found ' . count($missingRoutes) . ' undefined routes:');
            foreach ($missingRoutes as $route) {
                $this->warn(' - ' . $route);
            }
        } else {
            $this->info('All routes used in views and controllers are properly defined!');
        }
        
        // التحقق من المسارات غير المستخدمة
        $unusedRoutes = array_diff($definedRoutes, $usedRoutes);
        
        if (count($unusedRoutes) > 0) {
            $this->line('');
            $this->line('Found ' . count($unusedRoutes) . ' defined routes that are not used in views or controllers:');
            foreach ($unusedRoutes as $route) {
                $this->line(' - ' . $route);
            }
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * البحث عن المسارات المستخدمة في الملفات
     *
     * @param string $path
     * @param string $pattern
     * @return array
     */
    protected function findRoutesInFiles($path, $pattern)
    {
        $files = File::glob($path . '/**/' . $pattern);
        $routes = [];
        
        foreach ($files as $file) {
            $content = File::get($file);
            
            // البحث عن استخدامات route()
            preg_match_all('/route\s*\(\s*[\'"]([^\'"]+)[\'"]\s*/', $content, $matches);
            if (!empty($matches[1])) {
                $routes = array_merge($routes, $matches[1]);
            }
            
            // البحث عن استخدامات route->name()
            preg_match_all('/->name\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches);
            if (!empty($matches[1])) {
                $routes = array_merge($routes, $matches[1]);
            }
            
            // البحث عن استخدامات redirect()->route()
            preg_match_all('/redirect\s*\(\s*\)\s*->\s*route\s*\(\s*[\'"]([^\'"]+)[\'"]\s*/', $content, $matches);
            if (!empty($matches[1])) {
                $routes = array_merge($routes, $matches[1]);
            }
        }
        
        return array_unique($routes);
    }
}
