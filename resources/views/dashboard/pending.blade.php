@extends('layout.apps') {{-- Assuming 'layout.apps' is your main layout --}}

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;"> {{-- Center content vertically --}}
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-warning font-w600 mb-3">Menunggu Persetujuan Admin</h2>
                    <p class="lead">Akun Anda sedang ditinjau oleh Administrator.</p>
                    <p>Anda akan dapat mengakses fitur lengkap setelah akun Anda disetujui.</p>
                    <hr>
                    <p>Jika Anda merasa ini adalah kesalahan atau menunggu terlalu lama, silakan hubungi Administrator.</p>
                    {{-- Optional: Add a logout link --}}
                    <a href="{{ route('logout') }}" class="btn btn-secondary mt-3"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                        {{-- No CSRF needed for GET logout, but good practice if changing to POST --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection