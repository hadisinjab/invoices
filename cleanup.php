<?php
/**
 * سكريبت تنظيف شامل للمشروع
 * يقوم بحذف الملفات غير المهمة وتحسين الأداء
 */

echo "🧹 بدء عملية تنظيف المشروع...\n\n";

// 1. حذف ملفات cache
echo "1️⃣ تنظيف ملفات cache...\n";

// حذف ملفات views cache
$viewsCachePath = 'storage/framework/views';
if (is_dir($viewsCachePath)) {
    $files = glob($viewsCachePath . '/*.php');
    foreach ($files as $file) {
        if (basename($file) !== '.gitignore') {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        }
    }
}

// حذف ملفات cache data
$cacheDataPath = 'storage/framework/cache/data';
if (is_dir($cacheDataPath)) {
    $files = glob($cacheDataPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        }
    }
}

// 2. حذف ملفات sessions
echo "\n2️⃣ تنظيف ملفات sessions...\n";
$sessionsPath = 'storage/framework/sessions';
if (is_dir($sessionsPath)) {
    $files = glob($sessionsPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        }
    }
}

// 3. حذف ملفات testing
echo "\n3️⃣ تنظيف ملفات testing...\n";
$testingPath = 'storage/framework/testing';
if (is_dir($testingPath)) {
    $files = glob($testingPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        }
    }
}

// 4. تنظيف ملفات logs (احتفظ بآخر 1000 سطر)
echo "\n4️⃣ تنظيف ملفات logs...\n";
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    if (count($lines) > 1000) {
        $lines = array_slice($lines, -1000);
        file_put_contents($logFile, implode('', $lines));
        echo "   ✅ تم تقليل حجم ملف السجل إلى آخر 1000 سطر\n";
    } else {
        echo "   ℹ️ حجم ملف السجل مقبول\n";
    }
}

// 5. حذف ملفات .vite cache
echo "\n5️⃣ تنظيف ملفات Vite cache...\n";
$vitePath = '.vite';
if (is_dir($vitePath)) {
    $files = glob($vitePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        } elseif (is_dir($file)) {
            $subFiles = glob($file . '/*');
            foreach ($subFiles as $subFile) {
                if (is_file($subFile)) {
                    unlink($subFile);
                }
            }
            rmdir($file);
            echo "   ✅ حذف مجلد: " . basename($file) . "\n";
        }
    }
}

// 6. تنظيف node_modules (إذا كان كبير جداً)
echo "\n6️⃣ فحص حجم node_modules...\n";
$nodeModulesPath = 'node_modules';
if (is_dir($nodeModulesPath)) {
    $size = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($nodeModulesPath));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    $sizeMB = round($size / 1024 / 1024, 2);
    echo "   📊 حجم node_modules: {$sizeMB} MB\n";

    if ($sizeMB > 500) {
        echo "   ⚠️ حجم node_modules كبير جداً\n";
        echo "   💡 يمكنك حذفه وإعادة تثبيت التبعيات:\n";
        echo "      rm -rf node_modules && npm install\n";
    }
}

// 7. تنظيف ملفات مؤقتة أخرى
echo "\n7️⃣ تنظيف ملفات مؤقتة أخرى...\n";

// حذف ملفات .DS_Store (macOS)
$dsStoreFiles = glob('**/.DS_Store', GLOB_BRACE);
foreach ($dsStoreFiles as $file) {
    unlink($file);
    echo "   ✅ حذف: " . $file . "\n";
}

// حذف ملفات Thumbs.db (Windows)
$thumbsFiles = glob('**/Thumbs.db', GLOB_BRACE);
foreach ($thumbsFiles as $file) {
    unlink($file);
    echo "   ✅ حذف: " . $file . "\n";
}

// 8. تنظيف ملفات composer
echo "\n8️⃣ تنظيف ملفات composer...\n";
$composerCachePath = 'vendor/composer';
if (is_dir($composerCachePath)) {
    $cacheFiles = glob($composerCachePath . '/cache-*');
    foreach ($cacheFiles as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        }
    }
}

// 9. تنظيف ملفات npm
echo "\n9️⃣ تنظيف ملفات npm...\n";
$npmCachePath = 'node_modules/.cache';
if (is_dir($npmCachePath)) {
    $cacheFiles = glob($npmCachePath . '/*');
    foreach ($cacheFiles as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "   ✅ حذف: " . basename($file) . "\n";
        } elseif (is_dir($file)) {
            $subFiles = glob($file . '/*');
            foreach ($subFiles as $subFile) {
                if (is_file($subFile)) {
                    unlink($subFile);
                }
            }
            rmdir($file);
            echo "   ✅ حذف مجلد: " . basename($file) . "\n";
        }
    }
}

// 10. حساب المساحة المحررة
echo "\n🔍 حساب المساحة المحررة...\n";
$totalSize = 0;

// حساب حجم الملفات المحذوفة
$deletedFiles = [
    'storage/framework/views/*.php',
    'storage/framework/cache/data/*',
    'storage/framework/sessions/*',
    'storage/framework/testing/*',
    '.vite/*',
    '**/.DS_Store',
    '**/Thumbs.db'
];

foreach ($deletedFiles as $pattern) {
    $files = glob($pattern, GLOB_BRACE);
    foreach ($files as $file) {
        if (is_file($file)) {
            $totalSize += filesize($file);
        }
    }
}

$totalSizeMB = round($totalSize / 1024 / 1024, 2);
echo "   📊 إجمالي المساحة المحررة: {$totalSizeMB} MB\n";

// 11. نصائح إضافية
echo "\n💡 نصائح إضافية للتنظيف:\n";
echo "   • شغل: php artisan config:clear\n";
echo "   • شغل: php artisan cache:clear\n";
echo "   • شغل: php artisan view:clear\n";
echo "   • شغل: php artisan route:clear\n";
echo "   • شغل: composer dump-autoload\n";
echo "   • شغل: npm run build (لإعادة بناء الأصول)\n";

echo "\n🎉 تم الانتهاء من عملية التنظيف!\n";
echo "📊 تم تحرير {$totalSizeMB} MB من المساحة\n";
echo "✨ المشروع أصبح أكثر تنظيماً وأداءً أفضل\n";
?>
