<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
    <div class="logo-container" style='text-align: center; display: flex; justify-content: center;'>

        {{-- <img class="logo-abbr" src="{{ asset('images/login.png') }}" alt=""
            style="width: 400px; height: auto;"> --}}
        <img class="logo-abbr" src="https://imghost.net/ib/ttRCCc6qqMOHkyo_1749402813.png" alt=""
            style="height: 120px;">
    </div>
    <h2 style='color: #0066B3;'>Kode OTP Login NeoSIMRS</h2>
    <p>Gunakan kode berikut untuk menyelesaikan login Anda:</p>
    <div style='background: #f5f5f5; padding: 20px; text-align: center; margin: 20px 0; border-radius: 10px;'>
        <h1 style='color: #0066B3; letter-spacing: 5px; margin: 0; font-size: 32px;'>{{ $otp }}</h1>
    </div>
    <p style='color: #666;'>Kode ini akan kedaluwarsa dalam 5 menit.</p>
    <p style='color: #666;'>Jika Anda tidak meminta kode ini, abaikan email ini.</p>
    <hr style='margin: 20px 0;'>
    <p style='color: #999; font-size: 12px;'>NeoSIMRS - Sistem Informasi Rumah Sakit</p>
</div>
