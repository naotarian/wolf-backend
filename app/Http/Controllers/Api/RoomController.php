<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use \Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $master_user_id = $request['user_id'];
        $room = Room::create([
            'master_user_id' => $master_user_id
        ]);
        $room->users()->sync([$master_user_id]);
    }
    public function participation(Request $request)
    {
        \Log::info($request);
        return response()->json('ルームがありません', 404);
    }
}
