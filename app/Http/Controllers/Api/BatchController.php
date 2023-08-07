<?php

namespace App\Http\Controllers\Api;

use App\Repositories\BatchRepository;
use Illuminate\Support\Facades\Auth;

class BatchController extends BaseController
{
    protected BatchRepository $batchRepository;
    public function __construct($batchRepository)
    {
        $this->batchRepository = $batchRepository;
    }

    public function all(int $id)
    {
        $user = Auth::user();

        
    }

    public function show(int $id)
    {

    }

    public function delete(int $id)
    {

    }
}
