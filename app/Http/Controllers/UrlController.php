<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

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
        $request->validate([
            'url.name' => 'string|min:3|max:255|required|url',
        ]);

        // "|lte:255|unique:urls.name"
        $name = parse_url($url['name'], PHP_URL_HOST);
        $schema = parse_url($url['name'], PHP_URL_SCHEME);

        $parsed = $schema . "://" . $name;
        $date = Carbon::now();
        DB::table('urls')->insert([
            'name' => $parsed,
            'created_at' => $date,
        ]);

        return redirect()->route('urls');
    }
}