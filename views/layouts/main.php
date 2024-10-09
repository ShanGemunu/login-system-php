<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="http://localhost/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://localhost/assets/css/common.css" rel="stylesheet">
    <link href="http://localhost/assets/css/main.css" rel="stylesheet">
    <script src="http://localhost/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="http://localhost/jquery/jquery.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid ml-125p mr-125p ps-0 pe-0">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100p">
                    <div class="d-flex w-100p flex-row justify-content-between">
                        <div class="d-flex flex-row">
                            <li class="nav-item">
                                <a class="nav-link ps-0 mr-70" aria-current="page" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link ps-0 mr-70" href="#">Products</a>
                            </li>
                        </div>

                        <div class="d-flex flex-row">
                            <li class="nav-item">
                                <button type="button" class="btn btn-outline-primary btn-sm me-3 btn-w">
                                    <a class="nav-link" href="#">Login</a>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-w">
                                    <a class="nav-link" href="#">Register</a>
                                </button>
                            </li>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    {{content}}
</body>

</html>