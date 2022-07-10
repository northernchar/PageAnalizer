<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CurrentUrlController extends Controller
{
    /**
     * Show all urls
     *
     * 
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $host = DB::table('urls')->find($id);

        return view('current', [ 'host' => $host ]);
    }
}