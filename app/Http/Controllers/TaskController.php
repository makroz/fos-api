<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function beginTask(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $task = Task::where('id', $id)->where('status', 'O')->where('member_id', $request->user()->id)->update(['status' => 'S', 'start_date' => now()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($task, 'Task iniciada');
    }

    public function endTask(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $task = Task::where('id', $id)->where('status', 'C')->where('member_id', $request->user()->id)->update(['status' => 'E', 'end_date' => now()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($task, 'Task finalizada');
    }
}
