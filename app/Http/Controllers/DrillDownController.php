<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DrillDownController extends Controller
{
    //

          public function loadDrilldown($type)

           {

             switch ($type) {
                            case 'user-registrations':
                            $users = User::where('created_at', '>=', now()->subDays(30))->latest()->get()->take(10);
                            return view('admin.partials.users', compact('users'));
                              // Add more types later
                            default:
                        return response('<p>No data available</p>', 404);
                 }
                                           
        }
                                       
}
