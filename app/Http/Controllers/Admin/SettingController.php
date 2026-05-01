<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')
            ->get()
            ->groupBy('group')
            ->toArray();

        return view('pages.admin.settings.settings_list', [
            'settings' => $settings,
        ]);
    }

    public function edit(Request $request)
    {
        $setting = Setting::find($request->id);

        return view('pages.admin.settings.setting_edit', [
            'setting' => $setting,
        ]);
    }

    public function save(Request $request)
    {
        $setting = Setting::find($request->id);

        $validation = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $setting->value = $request->value;
        $setting->save();

        return redirect()->route('admin.setting.edit', $setting->id)->with('success', 'Setting saved successfully');
    }
}
