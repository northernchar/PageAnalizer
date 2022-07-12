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
        <!-- jquary.js -->
        <script src="//code.jquery.com/jquery.js"></script>
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
            @include('flash::message')
            <script>
                $('#flash-overlay-modal').modal();
            </script>
        <div class="container-lg">
            <h1 class="mt-5 mb-3">Сайт: {{$host->name}}</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <td>
                            ID
                        </td>
                        <td>
                            {{$host->id}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Имя
                        </td>
                        <td>
                            {{$host->name}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Дата создания
                        </td>
                        <td>
                            {{$host->created_at}}
                        </td>
                    </tr>
                </table>
            <h2 class="mt-5 mb-3">Проверки</h2>
            <form action={{ "/urls/" . $host->id . "/checks" }} method="post">
                {{ csrf_field() }}
                <input type="submit" class="btn btn-primary" value="Запустить проверку">
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <td>
                            ID
                        </td>
                        <td>
                            Дата создания
                        </td>
                        <td>
                            Код ответа
                        </td>
                    </tr>
                    @foreach ($checks as $check)
                        <tr>
                            <td>
                                {{$check->id}}
                            </td>
                            <td>
                                {{$check->created_at}}
                            </td>
                            <td>
                                {{$check->status_code}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <!-- <script>
            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        </script> -->
    </body>
</html>
