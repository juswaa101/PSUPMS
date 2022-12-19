<!DOCTYPE html>
@if (isset(Auth::user()->email))
    <script>
        window.location = "/admin/dashboard";
    </script>
@endif
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <style>
        :root {
            --rm-primary: #970000;
            --rm-dark: #121314;
            --rm-light: #FFF;
            --rm-muted: #7D7D7D;
        }

        b {
            font-family: 'Poppins';
            font-size: 22px;
        }

        form label{
            font-family: 'Poppins';
        }

        footer {
            text-align: center;
            padding-top: 30px;
            padding-bottom: 20px;
            display: flex;
            justify-content: center;
            background-color: #fff;
            color: black;
            font-family: 'Poppins';
        }

        .img-fluid{
            padding-left: 90px;
            padding-top: 10px;
            width: 410px;
            height: 340px;
        }


        .responsive {
            width: 100%;
            height: 80px;
        }

        .card{
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        }
    </style>
</head>

<body>

    <!-- nav -->
    <nav class="navbar" style="background-color: #00305F">
        <div class="container-fluid py-3 ps-4">
            <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/login/PSULABEL.png') }}" class="responsive">
            </a>
        </div>
    </nav>

    <!-- Section: Design Block -->
    <section>
    <!-- Jumbotron -->
        <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: hsl(0, 0%, 93%)">
            <div class="container">
                <div class="row gx-lg-5 align-items-center">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <img src="{{ asset('assets/login/teamwork.png') }}" class="img-fluid">
                </div>

                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <form method = "post" action = "{{ url('/login/checkAuth') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body py-5 px-md-5">

                                    <div class="form-outline mb-4">
                                        @if (Session::has('error'))
                                        <div class="alert alert-danger">
                                            <li>{{ Session::get('error') }}</li>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Email input -->
                                    <div class="form-outline mb-4">
                                    <label class="form-label" for="form3Example3">Email</label>
                                    <input type = "email" autocomplete="off" class="form-control" name = "email" placeholder="Email" value="{{ old('email') }}" required>

                                    @error('email')
                                        <p class="text text-danger mt-2">{{ $message }}</p>
                                    @enderror
                                    </div>


                                    <!-- Password input -->
                                    <div class="form-outline mb-4">
                                    <label class="form-label" for="form3Example4">Password</label>
                                    <input type="password" name = "password" id="form3Example4" class="form-control" placeholder="Password" required>

                                    @error('password')
                                        <p class="text text-danger mt-2">{{ $message }}</p>
                                    @enderror
                                    </div>

                                    <!-- Submit button -->
                                    <button class="btn btn-primary btn-block mb-4">
                                    Login
                                    </button>
                            </div>
                        </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->


    <!-- fotter -->
    <footer class="text-white" style="background-color: #00305F; padding: 77px">
    <p class="m-0">© 2022 Project Management System</p>
    </footer>
</body>
</html>
