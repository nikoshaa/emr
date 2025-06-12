<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekam Medis #{{ $rekamMedis->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        h1 {
            font-size: 18pt;
            margin: 5px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 150px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .content-table th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAM MEDIS PASIEN</h1>
        <p>NeoSIMRS - Sistem Informasi Rumah Sakit</p>
    </div>
    
    <table class="info-table">
        <tr>
            <td class="label">No. Rekam Medis:</td>
            <td>{{ $rekamMedis->pasien->no_rm ?? '-' }}</td>
            <td class="label">Tanggal:</td>
            <td>{{ \Carbon\Carbon::parse($rekamMedis->created_at)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pasien:</td>
            <td>{{ $rekamMedis->pasien->nama ?? '-' }}</td>
            <td class="label">Jenis Kelamin:</td>
            <td>{{ $rekamMedis->pasien->jk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lahir:</td>
            <td>{{ $rekamMedis->pasien->tgl_lahir ? \Carbon\Carbon::parse($rekamMedis->pasien->tgl_lahir)->format('d M Y') : '-' }}</td>
            <td class="label">Umur:</td>
            <td>{{ $rekamMedis->pasien->umur ?? '-' }} tahun</td>
        </tr>
        <tr>
            <td class="label">Dokter:</td>
            <td colspan="3">{{ $rekamMedis->dokter->nama ?? '-' }}</td>
        </tr>
    </table>
    
    <h3>Diagnosa</h3>
    <p>{{ $rekamMedis->diagnosa ?? 'Tidak ada data' }}</p>
    
    <h3>Tindakan</h3>
    <p>{{ $rekamMedis->tindakan->nama ?? 'Tidak ada data' }}</p>
    
    <h3>Resep Obat</h3>
    @if(isset($rekamMedis->resep) && count($rekamMedis->resep) > 0)
        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Dosis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekamMedis->resep as $resep)
                    <tr>
                        <td>{{ $resep->obat->nama ?? '-' }}</td>
                        <td>{{ $resep->dosis ?? '-' }}</td>
                        <td>{{ $resep->jumlah ?? '-' }}</td>
                        <td>{{ $resep->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada resep obat</p>
    @endif
    
    <h3>Catatan</h3>
    <p>{{ $rekamMedis->catatan ?? 'Tidak ada catatan' }}</p>
    
    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i:s') }}</p>
    </div>
</body>
</html>