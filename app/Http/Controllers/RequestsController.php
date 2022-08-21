<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\UserJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestsController extends BaseController
{

    public function index(Request $request)
    {
        $userJobs = UserJobs::query()->with(['url' => function($q){
            $q->select('id','url');
        }])->where('user_id', Auth::id())->orderBy('id','desc')->paginate(10);

        if ($userJobs->isEmpty())
            return $this->error("you have no requests.", 404, null);
        return $this->success($userJobs, "user short url requests", 200);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        // todo
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Request $request,$id)
    {
        $userJob = UserJobs::query()->where([['id', '=', $id], ['user_id', '=', Auth::id()]]);
        $result = $userJob->delete();

        if ($result)
            return $this->success($result, 'request deleted');
        return $this->error('delete failed', 400, $result);

    }
    public function destroyAll(Request $request){
        $userJobs = UserJobs::query()->where('user_id',Auth::id());
        $result = $userJobs->delete();

        if ($result)
            return $this->success($result, 'requests deleted');
        return $this->error('delete failed', 400, $result);
    }
}
