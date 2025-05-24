<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>NeoSIMRS - Sistem Informasi Rumah Sakit</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/logo.png')}}">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/toastr/css/toastr.min.css')}}">

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        .authincation {
            background-image: url('../images/intro.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
    <style>
        .authincation-content {
            background: #ffffff;  /* Membuat kotak login berwarna biru */
        }

        .form-control::placeholder {
            color: #acacac !important;  /* Warna placeholder biru */
        }

        /* Label tetap putih karena background biru */
        .auth-form label.text-white {
            color: #0066B3 !important;
        }

        /* Style untuk input */
        .form-control {
            border-color: #0066B3;
        }

        /* Hover state untuk input */
        .form-control:focus {
            border-color: #0066B3;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        /* Styling tombol */
        .btn-primary {
            background-color: #0066B3;
            border-color: #0066B3;
            color: #0066B3 !important;
        }

        .btn-primary:hover {
            background-color: #0066B3;
            border-color: #0066B3;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
                                        <img class="logo-abbr" src="{{asset('images/login.png')}}" alt="" style="width: 400px; height: auto;">
                                        {{-- <img class="logo-compact" src="{{asset('images/logo-text.png')}}" alt="" style="width: 150px; height: auto;"> --}}
                                    </div>
                                    {{-- <h4 class="text-center mb-4 text-white">Sign in your account</h4> --}}
                                    <br><br>
                                    <form action="{{Route('login.auth')}}" method="POST">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>No.Telp / HP</strong></label>
                                            <input type="number" class="form-control" placeholder="phone number" value="" required name="phone">
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input type="password" class="form-control"  placeholder="password" value="" required name="password">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-rounded bg-white text-primary btn-primary form-control">Masuk</button>
                                        </div>
                                    </form>
                                    {{-- Tambahkan tautan ke halaman registrasi --}}
                                    <div class="new-account mt-3 text-center">
                                        <p style="color: #0066B3;">Belum punya akun? <a class="text-primary" href="{{ route('register.page') }}">Daftar</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{asset('vendor/global/global.min.js')}}"></script>
	<script src="{{asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/deznav-init.js')}}"></script>
    <script src="{{asset('vendor/toastr/js/toastr.min.js')}}"></script>

    <script>
        @if(Session::has('sukses'))
            toastr.success("{{Session::get('sukses')}}", "Sukses",{timeOut: 5000})
        @endif
        @if(Session::has('gagal'))
            toastr.error("{{Session::get('gagal')}}", "Gagal",{timeOut: 5000})
        @endif
    </script>

</body>

</html>
