<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use DiDom\Element;
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

        $offset = $perPage * ((int) $page - 1);

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
    public function indexWithId(int $id)
    {
        $host = DB::table('urls')->find($id);
        $checks = DB::table('url_checks')->select()->where('url_id', $id)->orderBy('id', 'desc')->limit(50)->get();
        $checks = $checks->map(function ($item) {
            $item->h1 = Str::limit($item->h1, 10);
            $item->title = Str::limit($item->title, 30);
            $item->description = Str::limit($item->description, 30);
            return $item;
        });

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

        $added = DB::table('urls')->where('name', $parsed)->first() ?? collect(['id' => 1]);
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

        try {
            $resourse = Http::withOptions([
                'http_errors' => false,
                'allow_redirects' => false,
            ])->get($host->name);
            $status_code = $resourse->status();
            $content = $resourse->body();
        } catch (\Exception $e) {
            flash('Страница не отвечает')->error();
            return redirect()
                ->route('urls.id', [ 'id' => $host->id ]);
        }

        $title = '';
        $h1 = '';
        $description = '';

        $document = new Document($content);
        if ($document->has('title')) {
            // $title = $document->first('title')->firstChild()->text();
            $tdoc = $document->first('title');
            $tchild = $tdoc ? $tdoc->firstChild() : new Element('div');
            $title = $tchild->text();
        }
        if ($document->has('h1')) {
            // $h1children = $document->first('h1')->children();
            $h1doc = $document->first('h1');
            $h1children = $h1doc ? $h1doc->children() : [];
            $h1text = array_map(fn($attr) => $attr->text(), $h1children);
            $h1 = implode('', $h1text);
        }
        if ($document->has('meta[name=description]')) {
            $ddoc = $document->first('meta[name=description]');
            $description = $ddoc ? $ddoc->getAttribute('content') : '';
        }

        DB::table('url_checks')->insert([
            'url_id' => $id,
            'created_at' => Carbon::now(),
            'status_code' => $status_code,
            'h1' => Str::limit($h1, 255, ''),
            'title' => Str::limit($title, 255, ''),
            'description' => Str::limit($description, 255, ''),
        ]);

        flash('Страница успешно проверена')->success();
        return redirect()->route('urls.id', [ 'id' => $host->id ]);
    }
}
