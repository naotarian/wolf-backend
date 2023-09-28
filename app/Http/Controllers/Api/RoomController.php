<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Models
use App\Models\Room;
use App\Models\RoomInformation;
use App\Models\Cast;
use App\Models\RoomUser;
//Events
use App\Events\RoomEvent;
use App\Events\RoomVoiceUserEvent;
use App\Events\RoomReadyEvent;
use App\Events\RoomConfirmEvent;
use Carbon\Carbon;
//Facades
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
        event(new RoomReadyEvent($room->id, 0));
        event(new RoomConfirmEvent($room->id, false));
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
        event(new RoomReadyEvent($room->id, $room->phase));
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

    //開始ボタンを押したとき(役職選択画面に行くとき)
    public function pre_start(Request $request)
    {
        $room_id = $request['roomId'];
        $room = Room::find($room_id);
        $room->phase = 1;
        $room->save();
        event(new RoomReadyEvent($room_id, 1));
        return response()->noContent();
    }

    //役職が買われたとき
    public function select_position(Request $request)
    {
        $room_id = $request['roomId'];
        $user_id = $request['userId'];
        $position_id = $request['positionId'];
        $room_user = RoomUser::where('room_id', $room_id)->where('user_id', $user_id)->first('id');
        $room_user_id = $room_user['id'];
        $cast = $this->userCasting($room_id, $user_id);
        //すでに役が決まっている場合は何もしないでreturn
        if ($cast) return response()->json(['couldBuy' => false]);
        //まだ役が決まっていない場合は役がまだ空いていれば取得する
        $room_users = RoomUser::where('room_id', $room_id)->get()->toArray();
        $room_users_id = array_column($room_users, 'id');

        switch ($position_id) {
            case 1:
                //村人が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 1)->get();
                //すでに3人村人が決定していたら何もしないでreturn
                if (count($room_user_casts) === 3) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 1,
                ]);
                break;
            case 2:
                //人狼が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 2)->get();
                //すでに2人人狼が決定していたら何もしないでreturn
                if (count($room_user_casts) === 2) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 2,
                ]);
                break;
            case 3:
                //占い師が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 3)->get();
                //すでに1人占い師が決定していたら何もしないでreturn
                if (count($room_user_casts) === 1) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 3,
                ]);
                break;
            case 4:
                //霊能者が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 4)->get();
                //すでに1人霊能者が決定していたら何もしないでreturn
                if (count($room_user_casts) === 1) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 4,
                ]);
                break;
            case 5:
                //狩人が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 5)->get();
                //すでに1人狩人が決定していたら何もしないでreturn
                if (count($room_user_casts) === 1) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 5,
                ]);
                break;
            case 6:
                //狂人が買われた場合
                $room_user_casts = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 6)->get();
                //すでに1人狂人が決定していたら何もしないでreturn
                if (count($room_user_casts) === 1) return response()->json(['couldBuy' => false]);
                Cast::create([
                    'room_user_id' => $room_user_id,
                    'position_id' => 6,
                ]);
                break;
            default:
                //どのケースにも当てはまらない場合はないもしないでreturn
                return response()->json(['couldBuy' => false]);
        }
        return response()->json(['couldBuy' => true]);
    }

    //役職を買わなかった場合の余っている役職からランダム振り分け処理
    public function ramdom_position(Request $request)
    {
        $room_id = $request['roomId'];
        $user_id = $request['userId'];
        $room_user = RoomUser::where('room_id', $room_id)->where('user_id', $user_id)->first('id');
        $room_user_id = $room_user['id'];
        $cast = $this->userCasting($room_id, $user_id);
        //すでに役が決まっている場合は何もしないでreturn
        if ($cast) return response()->json($cast);
        $room_users = RoomUser::where('room_id', $room_id)->get()->toArray();
        $room_users_id = array_column($room_users, 'id');
        //現段階で決まっている村人の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 1)->count();
        if ($room_user_cast_1_count < 3) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 1,
            ]);
            return response()->json($cast);
        }
        //現段階で決まっている人狼の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 2)->count();
        if ($room_user_cast_1_count < 2) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 2,
            ]);
            return response()->json($cast);
        }
        //現段階で決まっている占い師の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 3)->count();
        if ($room_user_cast_1_count < 1) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 3,
            ]);
            return response()->json($cast);
        }
        //現段階で決まっている霊能者の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 4)->count();
        if ($room_user_cast_1_count < 1) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 4,
            ]);
            return response()->json($cast);
        }
        //現段階で決まっている狩人の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 5)->count();
        if ($room_user_cast_1_count < 1) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 5,
            ]);
            return response()->json($cast);
        }
        //現段階で決まっている狂人の人数
        $room_user_cast_1_count = Cast::whereIn('room_user_id', $room_users_id)->where('position_id', 6)->count();
        if ($room_user_cast_1_count < 1) {
            $cast = Cast::create([
                'room_user_id' => $room_user_id,
                'position_id' => 6,
            ]);
            return response()->json($cast);
        }
    }

    //ゲーム開始前の確認
    public function confirmed(Request $request)
    {
        $room_id = $request['roomId'];
        $cast_id = $request['castId'];
        $cast = Cast::find($cast_id);
        $cast->confirmed = 1;
        $cast->save();
        $room_users = RoomUser::where('room_id', $room_id)->get()->toArray();
        $room_users_id = array_column($room_users, 'id');
        $confirmed_casts_count = Cast::whereIn('room_user_id', $room_users_id)->where('confirmed', 1)->count();
        if (count($room_users_id) === $confirmed_casts_count) {
            //全員の準備が整った時
            event(new RoomConfirmEvent($room_id, true));
            $room = Room::find($room_id);
            $room->phase = 2;
            $room->game_start_time = Carbon::now();
            $room->save();
        }
        return response()->noContent();
    }

    public static function userCasting($room_id, $user_id)
    {
        $room_user = RoomUser::where('room_id', $room_id)->where('user_id', $user_id)->first('id');
        $room_user_id = $room_user['id'];
        $cast = Cast::where('room_user_id', $room_user_id)->first(['id', 'position_id']);
        if (!$cast) return null;
        return $cast;
    }
}
