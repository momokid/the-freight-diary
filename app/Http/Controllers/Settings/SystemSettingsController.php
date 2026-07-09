<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('system_settings')
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');

        return view('settings.system-settings', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key'   => ['required', 'string', 'max:100', 'unique:system_settings,key', 'regex:/^[a-z0-9_]+$/'],
            'label' => ['required', 'string', 'max:150'],
            'group' => ['required', 'string', 'max:50'],
            'value' => ['nullable', 'string'],
        ]);

        DB::table('system_settings')->insert([
            'key'        => $request->key,
            'value'      => $request->value,
            'label'      => $request->label,
            'group'      => $request->group,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Setting added.']);
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'value' => ['nullable', 'string'],
        ]);

        $updated = DB::table('system_settings')
            ->where('key', $key)
            ->update([
                'value'      => $request->value,
                'updated_at' => now(),
            ]);

        if (! $updated) {
            return response()->json(['success' => false, 'message' => 'Setting not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Saved.']);
    }
}
