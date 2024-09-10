<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Include Bootstrap CSS -->
    <link href="http://localhost/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="http://localhost/jquery/dist/jquery.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Products</a>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <a class="nav-link" href="#">Login</a>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <a class="nav-link" href="#">Register</a>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{content}}
    <!-- Include Bootstrap JS -->
    <script src="http://localhost/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>