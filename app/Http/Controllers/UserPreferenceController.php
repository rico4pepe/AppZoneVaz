<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
//use Auth;

class UserPreferenceController extends Controller
{
    //
      // Save User Preferences
      public function savePreferences(Request $request)
      {
          $request->validate([
              'leagues' => 'required|array',
              'teams' => 'required|array',
          ]);

          $user = Auth::user();

          UserPreference::updateOrCreate(
              ['user_id' => $user->id],
              ['leagues' => json_encode($request->leagues), 'teams' => json_encode($request->teams)]
          );

          return response()->json(['message' => 'Preferences saved successfully']);
      }

      // Fetch User Preferences
    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        return response()->json($preferences);
    }
}
