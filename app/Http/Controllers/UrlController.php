<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UrlController extends Controller
{
    /**
     * Show all urls
     *
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $urls = DB::table('urls')->get();
        return view('urls', [ 'urls' => $urls ]);
    }

    /**
     * Store a new url.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = $request->input('url');
        // $request->validate([
        //     'url.name' => 'required',
        // ]);

        // "|lte:255|unique:urls.name"
        $name = parse_url($url['name']);
        $date = Carbon::now()->toDateString();

        DB::table('urls')->insert(['name' => $name, 'created_at' => $date]);

        return redirect()->route('urls');
    }
}