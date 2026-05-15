<?php
/**
 * Run once: copies files from storage/app/public/ into public/storage/
 * Visit https://yourdomain.com/fix-storage.php  — then DELETE this file.
 */

$src  = dirname(__DIR__) . '/storage/app/public';
$dest = __DIR__ . '/storage';

function copyDir(string $from, string $to): array {
    $moved = [];
    if (!is_dir($from)) return $moved;
    if (!is_dir($to)) mkdir($to, 0755, true);

    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    ) as $item) {
        $rel  = substr($item->getPathname(), strlen($from) + 1);
        $target = $to . '/' . $rel;
        if ($item->isDir()) {
            if (!is_dir($target)) mkdir($target, 0755, true);
        } else {
            if (!file_exists($target)) {
                copy($item->getPathname(), $target);
                $moved[] = $rel;
            }
        }
    }
    return $moved;
}

$files = copyDir($src, $dest);
echo '<pre>';
echo count($files) . " file(s) copied:\n\n";
foreach ($files as $f) echo "  $f\n";
echo "\nDone. DELETE this file (fix-storage.php) now.</pre>";
