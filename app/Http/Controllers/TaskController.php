<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{

    public function tasksToday(Request $request)
    {
        $task = Task::select(['to_date', 'challenge_id', DB::raw("COUNT('id') as cant")])->where(DB::raw('DATE(to_date)'), date('Y-m-d'))->groupBy('to_date', 'challenge_id')
            ->get();
        return $this->sendResponse($task, 'Task today');
    }
}
