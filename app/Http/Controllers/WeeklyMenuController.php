<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMenu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyMenuController extends Controller
{
    public function index()
    {
        $menus = WeeklyMenu::with('items')->latest()->paginate(10);
        return view('meals.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'week_start_date' => 'required|date',
            'items' => 'required|array', // Logic for 7 days x 3 meals
        ]);

        DB::transaction(function () use ($validated) {
            $menu = WeeklyMenu::create([
                'center_id' => auth()->user()->center_id,
                'week_start_date' => $validated['week_start_date'],
                'created_by' => auth()->id(),
                'is_published' => true,
                'published_at' => now(),
            ]);

            foreach ($validated['items'] as $day => $meals) {
                foreach ($meals as $type => $content) {
                    MenuItem::create([
                        'weekly_menu_id' => $menu->id,
                        'day_of_week' => $day,
                        'meal_type' => $type,
                        'items' => is_array($content) ? json_encode($content) : $content,
                    ]);
                }
            }
        });

        return redirect()->route('menus.index')->with('success', 'تم حفظ الجدول الأسبوعي بنجاح.');
    }
}
