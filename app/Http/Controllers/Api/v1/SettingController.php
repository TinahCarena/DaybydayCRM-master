<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function getRemise()
    {
        $remise = Setting::first()->remise; 
        return response()->json(['remise' => $remise]);
    }

    public function updateRemise(Request $request)
    {
        $request->validate([
            'remise' => 'required|numeric|min:0',
        ]);

        $setting = Setting::first();
        $setting->remise = $request->remise;
        $setting->save();

        return response()->json(['message' => 'Remise mise à jour avec succès']);
    }
}
