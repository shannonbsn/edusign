<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;

class PresenceController extends Controller
{
    public function userPresences($user_id)
    {
        return Presence::with('cours')
            ->where('user_id', $user_id)
            ->get();
    }

    public function coursPresences($cours_id)
    {
        return Presence::with('user')
            ->where('cours_id', $cours_id)
            ->get();
    }
}
