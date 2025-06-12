@extends('layout.apps')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Dashboard Pasien</h4>
                <span>Selamat datang di sistem informasi rumah sakit</span>
            </div>
        </div>
    </div>

    @if(!$pasien)
        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Data pasien Anda belum terhubung dengan akun ini. Silahkan hubungi admin.
        </div>
    @else
        {{-- Add Patient Details Card --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Pasien</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nomor RM:</strong> {{ $pasien->no_rm }}</p>
                                <p><strong>Nama:</strong> {{ $pasien->nama }}</p>
                                <p><strong>Tempat, Tanggal Lahir:</strong> {{ $pasien->tmp_lahir }}, {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d M Y') }}</p>
                                <p><strong>Jenis Kelamin:</strong> {{ $pasien->jk }}</p>
                                <p><strong>Status Menikah:</strong> {{ $pasien->status_menikah }}</p>
                                <p><strong>Agama:</strong> {{ $pasien->agama }}</p>
                                <p><strong>Pendidikan:</strong> {{ $pasien->pendidikan }}</p>
                                <p><strong>Pekerjaan:</strong> {{ $pasien->pekerjaan }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Alamat Lengkap:</strong> {{ $pasien->alamat_lengkap }}</p>
                                <p><strong>Kelurahan:</strong> {{ $pasien->kelurahan }}</p>
                                <p><strong>Kecamatan:</strong> {{ $pasien->kecamatan }}</p>
                                <p><strong>Kabupaten:</strong> {{ $pasien->kabupaten }}</p>
                                <p><strong>Kodepos:</strong> {{ $pasien->kodepos }}</p>
                                <p><strong>Kewarganegaraan:</strong> {{ $pasien->kewarganegaraan }}</p>
                                <p><strong>Nomor HP:</strong> {{ $pasien->no_hp }}</p>
                                <p><strong>Cara Bayar:</strong> {{ $pasien->cara_bayar }}</p>
                                <p><strong>Nomor BPJS:</strong> {{ $pasien->no_bpjs ?? '-' }}</p>
                                <p><strong>Alergi:</strong> {{ $pasien->alergi ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Patient Details Card --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Rekam Medis</h4>
                    </div>
                    <div class="card-body">
                        @if($rekamMedis->isEmpty())
                            <div class="alert alert-info">
                                Belum ada data rekam medis.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Dokter</th>
                                            <th>Diagnosa</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekamMedis as $rm)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($rm->created_at)->format('d M Y') }}</td>
                                                <td>{{ $rm->dokter->nama  }}</td>
                                                <td>{{ $rm->diagnosa ?? 'Tidak ada data' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary triggerPdfModalBtn" data-rekam-id="{{ $rm->id }}">
                                                        <i class="fa fa-download"></i> Download PDF
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Password Modal for PDF -->
<div class="modal fade" id="passwordPdfModal" tabindex="-1" role="dialog" aria-labelledby="passwordPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordPdfModalLabel">Konfirmasi Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Masukkan password Anda untuk mengunci file PDF:</p>
                <input type="hidden" id="pdfRekamId"> <!-- To store rekam_id -->
                <div class="form-group">
                    <label for="pdfUserPassword">Password</label>
                    <input type="password" class="form-control" id="pdfUserPassword" required>
                    <div class="invalid-feedback" id="passwordError" style="display: none;">
                        Password salah.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmPasswordPdfBtn">Konfirmasi & Unduh PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    // Handle PDF modal trigger button click
    $('.triggerPdfModalBtn').on('click', function() {
        var rekamId = $(this).data('rekam-id');
        $('#pdfRekamId').val(rekamId); // Store rekam_id in the hidden input
        $('#pdfUserPassword').val(''); // Clear password field
        $('#passwordError').hide(); // Hide any previous errors
        $('#passwordPdfModal').modal('show');
    });

    // Handle password confirmation and AJAX request
    $('#confirmPasswordPdfBtn').on('click', function() {
        var password = $('#pdfUserPassword').val();
        var rekamId = $('#pdfRekamId').val();
        var button = $(this); // Reference to the button for disabling

        if (!password) {
            $('#pdfUserPassword').addClass('is-invalid');
            $('#passwordError').text('Password tidak boleh kosong.').show();
            return;
        } else {
            $('#pdfUserPassword').removeClass('is-invalid');
            $('#passwordError').hide();
        }

        // Disable button to prevent multiple clicks
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');

        $.ajax({
            url: '{{ route("obat.pengeluaran.verify-password") }}', // You'll need to create this route
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Add CSRF token
                password: password,
                rekam_id: rekamId
            },
            xhrFields: {
                responseType: 'blob' // Important for handling PDF download
            },
            success: function(response, status, xhr) {
                // Check if response is PDF (might have content-disposition)
                var contentType = xhr.getResponseHeader('content-type');

                if (contentType && contentType.indexOf('application/pdf') !== -1) {
                    // Create a Blob from the PDF Stream
                    var blob = new Blob([response], { type: 'application/pdf' });

                    // Create a link element
                    var link = document.createElement('a');

                    var filename = "RekamMedis-" + rekamId + ".pdf"; // Fallback if header parsing fails

                    // Try to get filename from header first
                    var disposition = xhr.getResponseHeader('content-disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1 || disposition && disposition.indexOf('inline') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    link.download = filename;

                    // Create a URL for the blob and set it as the href
                    link.href = window.URL.createObjectURL(blob);

                    // Append the link to the body (required for Firefox)
                    document.body.appendChild(link);

                    // Programmatically click the link to trigger the download
                    link.click();

                    // Remove the link from the document
                    document.body.removeChild(link);

                    // Revoke the object URL to free up memory
                    window.URL.revokeObjectURL(link.href);

                    $('#passwordPdfModal').modal('hide'); // Hide modal on success
                } else {
                    // Handle cases where the response isn't a PDF (e.g., unexpected server error)
                    // Try to parse as JSON error if possible
                    try {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var jsonResponse = JSON.parse(reader.result);
                            $('#passwordError').text(jsonResponse.message || 'Terjadi kesalahan tak terduga.').show();
                        }
                        reader.readAsText(response);
                    } catch (e) {
                        $('#passwordError').text('Gagal memproses PDF. Respon tidak valid.').show();
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle specific errors (like 401 Unauthorized for wrong password)
                if (jqXHR.status === 401) {
                    try {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var jsonResponse = JSON.parse(reader.result);
                            $('#pdfUserPassword').addClass('is-invalid');
                            $('#passwordError').text(jsonResponse.message || 'Password salah.').show();
                        }
                        reader.readAsText(jqXHR.response); // Use response instead of responseText for blob
                    } catch(e) {
                        $('#pdfUserPassword').addClass('is-invalid');
                        $('#passwordError').text('Password salah.').show();
                    }
                } else {
                    // Generic error
                    $('#passwordError').text('Error: ' + (jqXHR.responseJSON ? jqXHR.responseJSON.message : errorThrown)).show();
                }
            },
            complete: function() {
                // Re-enable button
                button.prop('disabled', false).html('Konfirmasi & Unduh PDF');
            }
        });
    });
});
</script>
@endsection