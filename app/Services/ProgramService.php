<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Support\Collection;

class ProgramService
{
    /**
     * جلب جميع البرامج النشطة مع cache لمدة ساعة.
     */
    public function getActivePrograms(): Collection
    {
        return cache()->remember('programs.active', 3600, function () {
            return Program::active()->orderBy('id')->get();
        });
    }

    /**
     * جلب برنامج بالـ code مع cache.
     */
    public function findByCode(string $code): ?Program
    {
        return cache()->remember("program.code.{$code}", 3600, function () use ($code) {
            return Program::where('code', $code)->first();
        });
    }

    /**
     * التحقق من ميزة بدون تحميل model كامل — مناسب للأماكن التي لا يكون فيها الطالب محملاً.
     */
    public function programAllows(int $programId, string $feature): bool
    {
        $program = cache()->remember("program.id.{$programId}", 3600, function () use ($programId) {
            return Program::find($programId);
        });

        return $program?->allows($feature) ?? false;
    }

    /**
     * مسح cache البرامج عند أي تعديل.
     */
    public function clearCache(?int $programId = null): void
    {
        cache()->forget('programs.active');

        if ($programId) {
            cache()->forget("program.id.{$programId}");
        }
    }
}
