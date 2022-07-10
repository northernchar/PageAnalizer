<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script src="{{ asset('js/app.js') }}"></script> -->
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <!-- Fonts -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <title>Анализатор страниц</title>
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
                            <a class="nav-link active" href="/">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="/urls">Сайты</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <main class="flex-grow-1">
            <div class="container-lg mt-3">
                <div class="row">
                    <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                        <h1 class="display-3">Анализатор страниц</h1>
                        <p class="lead">Бесплатно проверяйте сайты на SEO пригодность</p>
                        <form action="/urls" method="post" class="d-flex justify-content-center">
                            {{ csrf_field() }}
                            <input type="text" name="url[name]" value="" class="form-control form-control-lg" placeholder="https://www.example.com">
                            <input type="submit" class="btn btn-primary btn-lg ms-3 px-5 text-uppercase mx-3" value="Проверить">
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-top py-3 mt-5 flex-shrink-0">
            <div class="container-lg">
                <div class="text-center">
                    <a href="https://github.com/northernchar" target="_blank">Мой Github</a>
                </div>
            </div>
        </footer>
    </body>
