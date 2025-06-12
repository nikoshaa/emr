<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Detail Pengeluaran Obat - {{ $rekam->no_rekam }}</title>
    <style>
        body {
            font-family: 'sans-serif'; /* Use a generic font family available in mPDF */
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        .container {
            width: 100%;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
         .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .details-section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .details-section h3 {
            margin-top: 0;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .details-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .details-table td:first-child {
            width: 120px; /* Adjust as needed */
            font-weight: bold;
        }
        .obat-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .obat-table th, .obat-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .obat-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 30%; /* Adjust as needed */
            float: right; /* Or left */
            text-align: center;
        }
        .signature-box .signature-line {
            border-bottom: 1px solid #333;
            height: 60px; /* Space for signature */
            margin-bottom: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Optional: Add Clinic Logo/Name --}}
            {{-- <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;"> --}}
            <h1>KLINIK XYZ</h1> {{-- Replace with actual clinic name --}}
            <p>Jl. Alamat Klinik No. 123, Kota, Provinsi</p> {{-- Replace with actual address --}}
            <hr style="border-top: 1px solid #333;">
            <h2>DETAIL PENGELUARAN OBAT</h2>
        </div>

        <div class="details-section">
            <h3>Informasi Rekam Medis & Pasien</h3>
            <table class="details-table">
                <tr>
                    <td>No. Rekam Medis</td>
                    <td>: {{ $rekam->no_rekam }}</td>
                    <td>Tanggal Kunjungan</td>
                    <td>: {{ $rekam->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Nama Pasien</td>
                    <td>: {{ $pasien->nama }} ({{ $pasien->no_rm }})</td>
                    <td>Cara Bayar</td>
                    <td>: {{ $rekam->cara_bayar }}</td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>: {{ $pasien->tgl_lahir }}</td>
                     <td>Poli Tujuan</td>
                    <td>: {{ $rekam->poli }}</td>
                </tr>
                 <tr>
                    <td>Alamat</td>
                    <td colspan="3">: {{ $pasien->alamat_lengkap }}</td>
                </tr>
                <tr>
                    <td>Dokter</td>
                    <td colspan="3">: {{ $rekam->dokter ? $rekam->dokter->nama : 'N/A' }}</td> {{-- Assuming relation exists --}}
                </tr>
                 <tr>
                    <td>Diagnosa</td>
                    <td colspan="3">:
                        {{-- @if ($rekam->poli == "Poli Gigi")
                            @foreach ($rekam->gigi() as $item) {{ $item->diagnosa }}, @endforeach
                        @else
                        @endif --}}
                        {{ $rekam->diagnosa }}
                    </td>
                </tr>
                 <tr>
                    <td>Resep/Tindakan</td>
                    <td colspan="3">: {!! preg_replace('/^<p>(.*?)<\/p>$/is', '$1', $rekam->tindakan) !!}
                    </td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <h3>Rincian Obat Diberikan</h3>
            @if($pengeluaran->count() > 0)
                <table class="obat-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Obat</th>
                            <th>Nama Obat</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($pengeluaran as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->obat->kd_obat ?? 'N/A' }}</td>
                                <td>{{ $item->obat->nama ?? 'N/A' }}</td>
                                <td class="text-right">{{ $item->jumlah }}</td>
                                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td>{{ $item->keterangan }}</td>
                            </tr>
                            @php $total += $item->subtotal; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td colspan="5" class="text-right"><strong>Total Keseluruhan</strong></td>
                            <td class="text-right"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>Tidak ada obat yang dikeluarkan untuk rekam medis ini.</p>
            @endif
        </div>

        <div class="signature-section clearfix">
             <div class="signature-box" style="float: left; margin-left: 5%;">
                <p>Penerima,</p>
                <div class="signature-line"></div>
                <p>( {{ $pasien->nama }} )</p>
            </div>
            <div class="signature-box" style="float: right; margin-right: 5%;">
                <p>Petugas Apoteker,</p>
                <div class="signature-line"></div>
                <p>( {{ $apoteker ? $apoteker->name : 'N/A' }} )</p> {{-- Or fetch specific pharmacist name if needed --}}
            </div>
        </div>

        {{-- Optional Footer --}}
        {{-- <div class="footer">
            <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
        </div> --}}
    </div>
</body>
</html>