<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use PDO;

class UrlController extends Controller
{
    /**
     * Show all urls
     *
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $perPage = 10;
        $offset = $perPage * ($page - 1);

        $urls = DB::table('urls')->select()->orderBy('id')->offset($offset)->limit($perPage)->get();

        $count = DB::table('urls')->count();
        $pageCount = (int) ceil($count / $perPage);

        if ($pageCount == 0) {
            $pageCount = 1;
        }
        
        if ($page > $pageCount  || $page < 1) {
                return abort(404);
        }

        return view('urls', [
            'urls' => $urls,
            'pageCount' => $pageCount,
            'page' => $page,
            'perPage' => $perPage,
            'count' => $count,
        ]);
    }

    /**
     * Show specific url
     *
     *
     * @return \Illuminate\View\View
     */
    public function indexWithId($id)
    {
        $host = DB::table('urls')->find($id);

        return view('current', [ 'host' => $host ]);
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
        $parsed = parse_url($url['name'], PHP_URL_SCHEME)
                                                    . "://"
                                                        . parse_url($url['name'], PHP_URL_HOST);

        $urlItem = DB::table('urls')->where('name', $parsed)->first();

        if ($urlItem) {
            flash('Страница уже существует');

            return redirect()->route('urls.id', ['id' => $urlItem->id])->with(['host' => $urlItem]);
        }

        $date = Carbon::now();
        DB::table('urls')->insert([
            'name' => $parsed,
            'created_at' => $date,
        ]);

        $added = DB::table('urls')->where('name', $parsed)->first();
        flash('Страница успешно добавлена!')->success();

        return redirect()->route('urls.id', ['id' => $added->id])->with(['host' => $added]);
    }
}