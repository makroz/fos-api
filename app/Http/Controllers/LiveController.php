<?php

namespace App\Http\Controllers;

use App\Models\Live;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    public function tasksToday(Request $request)
    {
        $task = Task::select(['to_date', 'challenge_id', 'status', 'live_id', DB::raw("COUNT('id') as cant")])
            ->where('type', 'L')
            ->whereDate('to_date', date('Y-m-d'))
            ->where(function ($query) use ($request) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $request->user()->id);
            })
            ->groupBy('to_date', 'challenge_id', 'status', 'live_id')
            ->get();
        return $this->sendResponse($task, 'Task today');
    }

    public function meetTask(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $date = $request->input('to_date');
            $cant = $request->input('cant');
            $meet_link = $request->input('meet_link');
            $data = Live::create([
                'open_date' => date('Y-m-d H:i:s'),
                'meet_link' => $meet_link,
                'cant' => $cant,
                'user_id' => $request->user()->id,
                'challenge_id' => $id,
            ]);
            //ultimo id de la tabla lives
            $task = Task::where('to_date', $date)->where('challenge_id', $id)->update(['live_id' => $data->id, 'status' => 'O']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($data, 'Meet Creado');
    }
}
