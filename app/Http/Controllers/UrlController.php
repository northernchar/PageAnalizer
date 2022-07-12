<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Support\Str;

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
                ->selectRaw('
                urls.*, MAX(url_checks.created_at) as updated_at, MAX(url_checks.status_code) as status_code
                ')
                    ->groupBy('urls.id')
                        ->orderBy('urls.id')
                            ->offset($offset)
                                ->limit($perPage)
                                    ->get();


        $latestDates = DB::table('url_checks')
        ->select('url_id', DB::raw('MAX(created_at) as updated_at'))
            ->groupBy('url_id');

        $rawCodes = DB::table('url_checks')
        ->joinSub($latestDates, 'last_statuses', function ($join) {
            $join->on('url_checks.url_id', '=', 'last_statuses.url_id');
            $join->on('url_checks.created_at', '=', 'last_statuses.updated_at');
        })->get();

        $status_codes = $rawCodes->pluck('status_code', 'url_id')->all();

        return view('urls', [
            'urls' => $urls,
            'pageCount' => $pageCount,
            'page' => $page,
            'perPage' => $perPage,
            'count' => $count,
            'status_codes' => $status_codes
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
        $host = DB::table('urls')->find($id);
        $checks = DB::table('url_checks')->select()->orderBy('id', 'desc')->limit(50)->get();

        try {
            $status_code = Http::withOptions([
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 10,        // allow at most 10 redirects.
                ],
            ])->get($host->name)->status();
        } catch (\Exception $e) {
            flash('Страница не отвечает')->error();
            return redirect()
                ->route('urls.id', [ 'id' => $host->id ])
                    ->with([
                        'host' => $host,
                        'checks' => $checks,
                    ]);
        }

        $title = '';
        $h1 = '';
        $description = '';

        if ($status_code == 200) {
            $document = new Document($host->name, true);
            if ($document->has('title')) {
                $title = $document->first('title')->firstChild()->text();
            }
            if ($document->has('h1')) {
                $h1 = $document->first('h1')->firstChild()->text();
            }
            if ($document->has('meta[name=description]')) {
                $description = $document->first('meta[name=description]')->getAttribute('content');
            }
        }

        DB::table('url_checks')->insert([
            'url_id' => $id,
            'created_at' => Carbon::now(),
            'status_code' => $status_code,
            'h1' => Str::limit($h1, 10),
            'title' => Str::limit($title, 30),
            'description' => Str::limit($description, 30)
        ]);

        flash('Страница успешно проверена')->success();
        return redirect()->route('urls.id', [ 'id' => $host->id ])->with([
            'host' => $host,
            'checks' => $checks,
        ]);
    }
}
