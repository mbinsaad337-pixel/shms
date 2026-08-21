<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function createBackup()
    {
        $dbName = config('database.connections.' . config('database.default') . '.database');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$timestamp}.sql";

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $filePath = $backupDir . '/' . $filename;

        $fp = fopen($filePath, 'w');
        if (!$fp) {
            return response()->json(['error' => 'تعذّر إنشاء ملف النسخة الاحتياطية'], 500);
        }

        fwrite($fp, "-- ============================================\n");
        fwrite($fp, "-- نسخة احتياطية لقاعدة البيانات: {$dbName}\n");
        fwrite($fp, "-- تاريخ الإنشاء: " . now()->format('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- نظام إدارة مراكز الطلاب (SHMS)\n");
        fwrite($fp, "-- ============================================\n\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$dbName}";
        $tablesCount = 0;
        $totalRows = 0;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $tablesCount++;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            fwrite($fp, "-- -------------------------------------------\n");
            fwrite($fp, "-- هيكل الجدول: {$tableName}\n");
            fwrite($fp, "-- -------------------------------------------\n");
            fwrite($fp, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($fp, $createTable[0]->{'Create Table'} . ";\n\n");

            $hasData = false;

            DB::table($tableName)->orderBy('id')->chunk(500, function ($rows) use (
                $fp, $tableName, &$hasData, &$totalRows
            ) {
                if ($rows->isEmpty()) return;

                if (!$hasData) {
                    $columns = array_keys((array) $rows->first());
                    $escapedColumns = array_map(fn($c) => "`{$c}`", $columns);
                    fwrite($fp, "INSERT INTO `{$tableName}` (" . implode(', ', $escapedColumns) . ") VALUES\n");
                    $hasData = true;
                } else {
                    $columns = array_keys((array) $rows->first());
                }

                $valueRows = [];
                foreach ($rows as $row) {
                    $values = array_map(function ($col) use ($row) {
                        $val = $row->$col;
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        if (is_bool($val)) {
                            return $val ? '1' : '0';
                        }
                        return "'" . addslashes((string) $val) . "'";
                    }, $columns);
                    $valueRows[] = '  (' . implode(', ', $values) . ')';
                    $totalRows++;
                }
                fwrite($fp, implode(",\n", $valueRows) . ";\n\n");
            });
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($fp);

        $sizeInBytes = filesize($filePath);
        $sizeFormatted = $this->formatBytes($sizeInBytes);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء النسخة الاحتياطية بنجاح',
            'filename' => $filename,
            'size' => $sizeFormatted,
            'size_bytes' => $sizeInBytes,
            'tables_count' => $tablesCount,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function downloadBackup(Request $request)
    {
        $filename = $request->query('file');

        if (!$filename) {
            return response()->json(['error' => 'لم يتم تحديد ملف'], 400);
        }

        if (preg_match('/[^a-zA-Z0-9_\-.]/', $filename) || str_contains($filename, '..')) {
            return response()->json(['error' => 'اسم الملف غير صالح'], 400);
        }

        $filePath = storage_path('app/backups/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        $sizeInBytes = filesize($filePath);
        $sizeFormatted = $this->formatBytes($sizeInBytes);

        $headers = [
            'Content-Type' => 'application/sql; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length' => $sizeInBytes,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($filePath) {
            readfile($filePath);
        }, 200, $headers);
    }

    public function deleteBackup(Request $request)
    {
        $filename = $request->input('filename');

        if (!$filename) {
            return response()->json(['error' => 'لم يتم تحديد ملف'], 400);
        }

        if (preg_match('/[^a-zA-Z0-9_\-.]/', $filename) || str_contains($filename, '..')) {
            return response()->json(['error' => 'اسم الملف غير صالح'], 400);
        }

        $filePath = storage_path('app/backups/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        if (!@unlink($filePath)) {
            return response()->json(['error' => 'تعذّر حذف الملف'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف النسخة الاحتياطية بنجاح',
        ]);
    }

    public function listBackups()
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            return response()->json(['backups' => []]);
        }

        $files = glob($backupDir . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($file)),
                'size_bytes' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return response()->json(['backups' => $backups]);
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
