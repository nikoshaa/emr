<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NeoSIMRS - Sistem Informasi Rumah Sakit</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <style>
        /* Your existing styles remain here... */
        .authincation {
            background-image: url('../images/intro.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .authincation-content {
            background: #ffffff;
        }

        .form-control::placeholder {
            color: #acacac !important;
        }

        .auth-form label.text-white {
            color: #0066B3 !important;
        }

        .form-control {
            border-color: #0066B3;
        }

        .form-control:focus {
            border-color: #0066B3;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        .btn-primary {
            background-color: #0066B3;
            border-color: #0066B3;
        }

        .btn-primary:hover {
            background-color: #00569a;
            border-color: #00569a;
        }

        .loading {
            display: none;
        }

        /* === NEW & IMPROVED OTP MODAL STYLES === */
        .otp-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .otp-modal.show {
            display: flex;
            /* Use flexbox for centering */
        }

        .otp-modal-content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: slide-up 0.3s ease-out;
        }

        @keyframes slide-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .otp-modal-content .logo {
            width: 150px;
            margin-bottom: 25px;
        }

        .otp-modal-content h4 {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .otp-modal-content p {
            color: #4a5568;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .otp-modal-content p strong {
            color: #2d3748;
            font-weight: 600;
        }

        #otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .otp-input {
            width: 50px;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            color: #2d3748;
            transition: all 0.2s ease-in-out;
        }

        .otp-input:focus {
            outline: none;
            border-color: #0066B3;
            box-shadow: 0 0 0 3px rgba(0, 102, 179, 0.2);
        }

        .otp-input.filled {
            border-color: #0066B3;
        }

        .otp-verify-btn {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            background-color: #0066B3;
            border: none;
            color: white;
            transition: background-color 0.2s;
        }

        .otp-verify-btn:hover {
            background-color: #00569a;
        }

        .otp-verify-btn:disabled {
            background-color: #a0aec0;
            cursor: not-allowed;
        }

        #resend-otp {
            color: #0066B3;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        #resend-otp:hover {
            color: #004d87;
        }

        #otp-error {
            min-height: 20px;
            /* Prevents layout shift */
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

                                        <img class="logo-abbr" src="{{ asset('images/login.png') }}" alt=""
                                            style="width: 400px; height: auto;">

                                    </div>

                                    <form id="login-form">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="mb-1"><strong>No.Telp / HP</strong></label>
                                            <input type="number" class="form-control"
                                                placeholder="Masukkan nomor telepon" required name="phone"
                                                id="phone">
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1"><strong>Password</strong></label>
                                            <input type="password" class="form-control" placeholder="Masukkan password"
                                                required name="password" id="password">
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-block" id="login-btn">
                                                Masuk
                                            </button>
                                            <div id="login-loading" class="loading mt-2">
                                                <span style="color: #0066B3;">Memverifikasi dan mengirim OTP...</span>
                                            </div>
                                        </div>
                                        <div id="login-error" class="text-danger mt-2 text-center"></div>
                                    </form>

                                    <div class="new-account mt-3 text-center">
                                        <p style="color: #0066B3;">Belum punya akun? <a class="text-primary"
                                                href="{{ route('register.page') }}">Daftar</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="otp-modal" class="otp-modal">
        <div class="otp-modal-content">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            <h4>Verifikasi Email Anda</h4>
            <p>Kode 6 digit telah dikirimkan ke email <br><strong id="user-email"></strong></p>

            <form id="otp-form" autocomplete="off">
                <div id="otp-inputs">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                    <input type="text" class="otp-input" inputmode="numeric" maxlength="1">
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn otp-verify-btn" id="verify-btn">Verifikasi</button>
                    <div id="otp-loading" class="loading mt-2">
                        <span style="color: #0066B3;">Memverifikasi...</span>
                    </div>
                </div>

                <div id="otp-error" class="text-danger mt-2 mb-3"></div>

                <div class="mt-3">
                    <p style="color: #666; font-size: 0.9rem;">Tidak menerima kode? <a href="#"
                            id="resend-otp">Kirim ulang</a></p>
                </div>
            </form>
        </div>
    </div>


    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const RESEND_API_KEY = '{{ env('RESEND_API_KEY') }}';
            let userEmail = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ===================================
            // LOGIN FORM LOGIC (Mostly Unchanged)
            // ===================================
            $('#login-form').on('submit', async function(e) {
                e.preventDefault();
                const phone = $('#phone').val();
                const password = $('#password').val();
                const btn = $('#login-btn');
                const loading = $('#login-loading');

                // Store credentials temporarily for resend functionality
                localStorage.setItem('last_phone', phone);
                localStorage.setItem('temp_password', password);

                btn.prop('disabled', true).hide();
                loading.show();
                $('#login-error').text('');

                try {
                    const response = await $.post('{{ route('login.auth') }}', {
                        phone,
                        password
                    });
                    if (response.success) {
                        userEmail = response.email;
                        $('#user-email').text(response.email);
                        $('#otp-modal').addClass('show');
                        $('.otp-input').first().focus();
                    } else {
                        $('#login-error').text(response.message);
                        btn.prop('disabled', false).show();
                        loading.hide();
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    const message = error.responseJSON?.message ||
                        'Terjadi kesalahan. Silakan coba lagi.';
                    $('#login-error').text(message);
                    btn.prop('disabled', false).show();
                    loading.hide();
                }
            });

            // ===================================
            //  NEW & IMPROVED OTP HANDLING
            // ===================================
            const otpInputs = $('.otp-input');

            otpInputs.on('input', function(e) {
                const currentInput = $(this);
                const nextInput = currentInput.next('.otp-input');

                if (currentInput.val()) {
                    currentInput.addClass('filled');
                    if (nextInput.length) {
                        nextInput.focus();
                    } else {
                        currentInput.blur(); // All fields filled
                        $('#otp-form').submit(); // Auto-submit when last field is filled
                    }
                }
            });

            otpInputs.on('keydown', function(e) {
                const currentInput = $(this);
                if (e.key === 'Backspace') {
                    if (!currentInput.val()) {
                        currentInput.removeClass('filled');
                        currentInput.prev('.otp-input').focus().val('').removeClass('filled');
                    } else {
                        currentInput.val('').removeClass('filled');
                    }
                }
            });

            otpInputs.on('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text')
                    .trim();
                if (pastedData.length === 6 && /^\d+$/.test(pastedData)) {
                    for (let i = 0; i < 6; i++) {
                        $(otpInputs[i]).val(pastedData[i]).addClass('filled');
                    }
                    $(otpInputs[5]).focus();
                    $('#otp-form').submit();
                }
            });

            // OTP Verification
            $('#otp-form').on('submit', function(e) {
                e.preventDefault();
                let otp = '';
                otpInputs.each(function() {
                    otp += $(this).val();
                });

                if (otp.length !== 6) {
                    $('#otp-error').text('Kode OTP harus 6 digit.');
                    return;
                }

                const btn = $('#verify-btn');
                const loading = $('#otp-loading');

                btn.prop('disabled', true).hide();
                loading.show();
                $('#otp-error').text('');

                $.post('{{ route('login.verify-otp') }}', {
                        otp: otp
                    })
                    .done(function(response) {
                        if (response.success) {
                            toastr.success(response.message, "Sukses");
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        } else {
                            // This case is for success:false from server, handled in .fail now
                            $('#otp-error').text(response.message || 'Kode OTP tidak valid.');
                            btn.prop('disabled', false).show();
                            loading.hide();
                        }
                    })
                    .fail(function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Gagal memverifikasi OTP. Coba lagi.';
                        $('#otp-error').text(message);
                        otpInputs.val('').removeClass('filled');
                        otpInputs.first().focus();
                        btn.prop('disabled', false).show();
                        loading.hide();
                    });
            });

            // Resend OTP (Now re-triggers the original login flow)
            $('#resend-otp').on('click', async function(e) {
                e.preventDefault();
                $('#otp-modal').removeClass('show');
                $('#login-btn').prop('disabled', false).show();
                $('#login-loading').hide();

                // To resend, we simply resubmit the login form
                // which the user has to do manually for security.
                // A small toastr notification can guide them.
                toastr.info("Silakan masukkan kembali kredensial Anda untuk mengirim ulang OTP.",
                    "Info");
                $('#phone').focus();
            });

            // Close modal when clicking on the background overlay
            $('#otp-modal').on('click', function(e) {
                if ($(e.target).is('#otp-modal')) {
                    $(this).removeClass('show');
                    otpInputs.val('').removeClass('filled');
                    // Reset login button state
                    $('#login-btn').prop('disabled', false).show();
                    $('#login-loading').hide();
                }
            });

            // Keep existing toastr notifications
            @if (Session::has('sukses'))
                toastr.success("{{ Session::get('sukses') }}", "Sukses", {
                    timeOut: 5000
                });
            @endif
            @if (Session::has('gagal'))
                toastr.error("{{ Session::get('gagal') }}", "Gagal", {
                    timeOut: 5000
                });
            @endif
        });
    </script>
</body>

</html>
