<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Анализатор страниц</title>
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    </head>
    <body class="min-vh-100 d-flex flex-column">
        <header class="flex-shrink-0">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark px-3">
                <a class="navbar-brand" href="/">Анализатор страниц</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link " href="/">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/urls">Сайты</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="container-lg">
            <h1 class="mt-5 mb-3">Сайты</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Последняя проверка</th>
                    @foreach ($urls as $url)
                        <tr>
                            <td>{{ $url->id }}</td>
                            <td><a class="link-info" href={{"/urls/" . $url->id}}>{{ $url->name }}</a></td>
                            <td>{{ $url->updated_at }}</td>
                        </tr>
                    @endforeach
                </table>
                <nav class="d-flex justify-items-center justify-content-between">
                    <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
                        <div>
                            <p class="small text-muted">
                                Showing
                                <span class="font-medium">
                                    @if ($page == 1)
                                        @if ($count >= 1)
                                            1
                                        @else
                                            0
                                        @endif
                                    @else
                                        {{$perPage * ($page - 1) + 1}}
                                    @endif
                                </span>
                                to
                                <span class="font-medium">
                                    @if ($page == $pageCount)
                                        {{$count}}
                                    @else
                                        @if ($count === 0)
                                            0
                                        @else
                                            {{$page * $perPage}}
                                        @endif
                                    @endif
                                </span>
                                of
                                <span class="font-medium">
                                    {{$count}}
                                </span>
                                results
                            </p>
                        </div>
                    </div>
                    <div>
                        <ul class="pagination">
                            @if ($page == 1)
                                    <li class="page-item disabled" aria-disabled="true" aria-label="pagination.previous">
                                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                    </li>
                                @else
                                    <a class="page-link" href={{ "/urls?page=" . $page - 1 }} rel="prev" aria-label="pagination.previous">&lsaquo;</a>
                            @endif

                            @for ($i = 1; $i <=$pageCount; $i++)
                                @if ($i == $page)
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{$i}}</span></li>
                                    @continue
                                @endif
                                
                            <li class="page-item"><a class="page-link" href= {{ "/urls?page=" . $i }} >{{$i}}</a></li>
                            @endfor

                            @if ($page != $pageCount)
                                <li class="page-item">
                                    <a class="page-link" href={{ "/urls?page=" . $page + 1 }} rel="next" aria-label="pagination.next">&rsaquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true" aria-label="pagination.next">
                                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </body>
</html>

