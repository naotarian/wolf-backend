<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomInformation;
use App\Events\RoomEvent;
use App\Events\RoomVoiceUserEvent;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $master_user_id = $request['userId'];
        $text = $request['text'];
        $room = DB::transaction(function () use ($master_user_id, $text) {
            $room = Room::create([
                'master_user_id' => $master_user_id
            ]);
            RoomInformation::create([
                'room_id' => $room->id,
                'rule' => $text
            ]);
            return $room;
        }, 3);
        if (!$room) return response()->noContent();
        session(['voiceUserId' => ['test']]);
        event(new RoomVoiceUserEvent($room->id, ['test']));
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
            array_push($users, ['name' => $user->name, 'id' => $user->id, 'character_id' => $user->character_id]);
        }
        event(new RoomEvent($room->id, $users));
        return response()->json($room);
    }

    public function list()
    {
        $rooms = Room::with(['room_master' => function ($query) {
            $query->select(['id', 'name', 'character_id']);
        }])->select(['id', 'master_user_id'])->get();
        return response()->json($rooms);
    }

    public function leaving(Request $request)
    {
        $req_room_id = $request['roomId'];
        $room = Room::find($req_room_id);
        $user_ids = [];
        foreach ($room->users as $user) {
            if ($request['userId'] !== $user->id) array_push($user_ids, $user->id);
        }
        $room->users()->sync($user_ids);
        $users = [];
        foreach ($room->fresh()->users as $user) {
            array_push($users, ['name' => $user->name, 'id' => $user->id, 'character_id' => $user->character_id]);
        }
        event(new RoomEvent($room->id, $users));
        return response()->noContent();
    }

    public function dissolution(Request $request)
    {
        $req_room_id = $request['roomId'];
        $room = Room::find($req_room_id);
        if ($room->master_user_id !== $request['userId']) return response()->noContent();
        $room->users()->sync([]);
        event(new RoomEvent($room->id, []));
        $room->delete();
        return response()->noContent();
    }

    public function voiceUserAdd(Request $request)
    {
        $voice_users = $request['voiceOnUser'];
        array_push($voice_users, $request['userId']);
        event(new RoomVoiceUserEvent($request->roomId, $voice_users));
        return response()->noContent();
    }
    public function voiceUserRemove(Request $request)
    {
        $voice_users = $request['voiceOnUser'];
        $users = array_diff($voice_users, [$request['userId']]);
        $users = array_values($users);
        event(new RoomVoiceUserEvent($request->roomId, $users));
        return response()->noContent();
    }
}
