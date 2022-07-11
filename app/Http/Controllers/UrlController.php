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

        Log::channel('single')->info('Showing the urls');

        $page = $request->query('page') ?? 1;
        $perPage = 10;
        $offset = $perPage * ($page - 1);

        // $urls = DB::table('urls')->select()->orderBy('id')->offset($offset)->limit($perPage)->get();

        $count = DB::table('urls')->count();
        $pageCount = (int) ceil($count / $perPage);

        if ($pageCount == 0) {
            $pageCount = 1;
        }
        if ($page > $pageCount  || $page < 1) {
                return abort(404);
        }

        $urls = DB::table('urls')
            ->leftJoin('url_checks', 'urls.id', '=', 'url_checks.url_id')
                ->selectRaw('urls.*, MAX(url_checks.created_at) as updated_at')
                    ->groupBy('urls.id')
                        ->orderBy('urls.id')
                            ->offset($offset)
                                ->limit($perPage)
                                    ->get();


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
        $checks = DB::table('url_checks')->select()->where('url_id', $id)->orderBy('id', 'desc')->limit(50)->get();

        return view('current', [ 'host' => $host,'checks' => $checks]);
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
            $checks = DB::table('url_checks')
                ->select()
                    ->where('url_id', $urlItem->id)
                        ->orderBy('id', 'desc')
                            ->limit(50)
                                ->get();

            return redirect()
                ->route('urls.id', ['id' => $urlItem->id])
                    ->with(['host' => $urlItem, 'checks' => $checks]);
        }

        $date = Carbon::now();
        DB::table('urls')->insert([
            'name' => $parsed,
            'created_at' => $date,
        ]);

        $added = DB::table('urls')->where('name', $parsed)->first();
        flash('Страница успешно добавлена!')->success();
        $checks = DB::table('url_checks')
            ->select()
                ->where('url_id', $added->id)
                    ->orderBy('id', 'desc')
                        ->limit(50)
                            ->get();

        return redirect()
            ->route('urls.id', ['id' => $added->id])
                ->with(['host' => $added, 'checks' => $checks]);
    }

    public function check(Request $request)
    {
        $id = request()->id;
        DB::table('url_checks')->insert([
            'url_id' => $id,
            'created_at' => Carbon::now(),
        ]);

        $host = DB::table('url_checks')->find($id);
        $checks = DB::table('url_checks')->select()->orderBy('id', 'desc')->limit(50)->get();
        return redirect()->route('urls.id', [ 'id' => $host->id ])->with([
            'host' => $host,
            'checks' => $checks,
        ]);
    }
}
