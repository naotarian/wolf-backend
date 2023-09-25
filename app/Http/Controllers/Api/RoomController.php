<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Events\RoomEvent;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $master_user_id = $request['user_id'];
        $room = Room::create([
            'master_user_id' => $master_user_id
        ]);
        // $room->users()->sync([$master_user_id]);
        return response()->json(['roomId' => $room['id']]);
    }
    public function participation(Request $request)
    {
        $req_room_id = $request['roomId'];
        $room = Room::find($req_room_id);
        if (!$room) return response()->json('ルームがありません', 404);
        $user_ids = [];
        foreach ($room->users as $user) {
            array_push($user_ids, $user->id);
        }
        array_push($user_ids, $request['user']);
        $room->users()->sync($user_ids);
        $users = [];
        foreach ($room->fresh()->users as $user) {
            array_push($users, ['name' => $user->name, 'id' => $user->id]);
        }
        \Log::info($room->id);
        \Log::info($users);
        event(new RoomEvent($room->id, $users));
        return response()->noContent();
    }

    public function list()
    {
        \Log::info('test');
        $rooms = Room::all();
        return response()->json($rooms);
    }
}
