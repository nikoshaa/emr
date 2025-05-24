<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>NeoSIMRS - Registrasi Akun</title> {{-- Changed Title --}}
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/logo.png')}}">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/toastr/css/toastr.min.css')}}">

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    {{-- Styles copied from login.blade.php --}}
    <style>
        .authincation {
            background-image: url('../images/intro.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* {{-- Add these properties to fix the background --}} */
            background-attachment: fixed; /* Keeps the background fixed during scroll */
            min-height: 100vh;
            
        }
        .authincation-content { background: #ffffff; }
        .form-control::placeholder { color: #acacac !important; }
        .auth-form label.text-white { color: #0066B3 !important; }
        .form-control { border-color: #0066B3; }
        .form-control:focus { border-color: #0066B3; box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25); }
        .btn-primary { background-color: #0066B3; border-color: #0066B3; color: #0066B3 !important; }
        .btn-primary:hover { background-color: #0066B3; border-color: #0066B3; }
        .invalid-feedback { color: #dc3545; display: block; } /* Style for validation errors */
    </style>
</head>

<body class="h-100">
    <div class="authincation">
        <div class="container h-100 ">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6 m-5">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
                                        <img class="logo-abbr" src="{{asset('images/login.png')}}" alt="" style="width: 400px; height: auto;">
                                    </div>
                                    <h4 class="text-center mb-4" style="color: #0066B3;">Registrasi Akun Baru</h4> {{-- Changed Heading --}}
                                    <br>
                                    {{-- Changed form action to register route --}}
                                    <form action="{{ route('register.submit') }}" method="POST">
                                        {{ csrf_field() }}

                                        {{-- Added Name Field --}}
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Nama Lengkap</strong></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required name="name">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        {{-- Add Email Field --}}
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Email</strong></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Alamat Email" value="{{ old('email') }}" required name="email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>No.Telp / HP</strong></label>
                                            <input type="number" class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor Telepon" value="{{ old('phone') }}" required name="phone">
                                             @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required name="password">
                                             @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        {{-- Added Password Confirmation Field --}}
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Konfirmasi Password</strong></label>
                                            <input type="password" class="form-control" placeholder="Konfirmasi Password" required name="password_confirmation">
                                        </div>

                                        <div class="text-center">
                                            {{-- Changed Button Text --}}
                                            <button type="submit" class="btn btn-rounded bg-white text-primary btn-primary form-control">Daftar</button>
                                        </div>
                                    </form>
                                    {{-- Optional: Add link back to login page --}}
                                    <div class="new-account mt-3 text-center">
                                        <p style="color: #0066B3;">Sudah punya akun? <a class="text-primary" href="{{ route('login') }}">Masuk</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts copied from login.blade.php --}}
    <script src="{{asset('vendor/global/global.min.js')}}"></script>
	<script src="{{asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/deznav-init.js')}}"></script>
    <script src="{{asset('vendor/toastr/js/toastr.min.js')}}"></script>

    <script>
        // Keep toastr notifications if needed
        @if(Session::has('sukses'))
            toastr.success("{{Session::get('sukses')}}", "Sukses",{timeOut: 5000})
        @endif
        @if(Session::has('gagal'))
            toastr.error("{{Session::get('gagal')}}", "Gagal",{timeOut: 5000})
        @endif
        // Display validation errors using toastr if preferred, or rely on inline messages
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}", "Error",{timeOut: 5000});
            @endforeach
        @endif
    </script>

</body>
</html>