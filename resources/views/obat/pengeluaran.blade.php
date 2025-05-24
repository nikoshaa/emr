@extends('layout.apps')
@section('content')
{{-- BREADCRUMBS --}}
<div class="form-head page-titles d-flex align-items-center mb-sm-4 mb-3">
    <div class="mr-auto">
        <h2 class="text-black font-w600">Pasien Detail</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Patient</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">RM#{{$pasien->no_rm}}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{$rekam->no_rekam}}</a></li>

        </ol>
    </div>
    <div class="d-flex">
        @if ($rekam)
            {!! $rekam->status_display() !!}
        @endif  
    </div>
</div>


<!-- Pencarian Obat -->
<div class="modal fade" id="modalObat">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Obat</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive card-table"> 
                    <table class="display dataTablesCard white-border table-responsive-sm"
                            style="width: 100%"
                        id="obat-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Obat</th>
                                <th>Nama Obat</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Add the password modal HTML --}}
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Enter Password to View Medical Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="decryptForm">
                    {{-- This hidden input will store the rekam_id --}}
                    <input type="hidden" id="encrypted_record_id" name="record_id" value="{{ $rekam ? $rekam->id : '' }}">
                    <div class="form-group">
                        <label for="decrypt_password">Password</label>
                        <input type="password" class="form-control" id="decrypt_password" name="password" required>
                    </div>
                    <div class="alert alert-danger mt-2" id="decrypt_error" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="decryptBtn">Decrypt</button>
            </div>
        </div>
    </div>
</div>


<!-- Pencarian Obat -->
<div class="modal fade" id="modalObat">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Obat</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive card-table"> 
                    <table class="display dataTablesCard white-border table-responsive-sm"
                            style="width: 100%"
                        id="obat-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Obat</th>
                                <th>Nama Obat</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- DATA --}}
    <div class="row">
       
        <div class="col-xxl-12">
            <div class="row">
                {{-- Define the decryption condition --}}
                @php
                    $needsDecryption = 
                                        ($rekam->status == 3) 
                                       && ($rekam->is_decrypted == 0);
                @endphp

                <div class="col-xl-12 col-xxl-5 col-lg-5">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="fs-20 text-black mb-0">Detail Pasien</h4>
                            <div class="dropdown">
                                <div class="btn-link" role="button" data-toggle="dropdown" aria-expanded="false">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">Accept Patient</a>
                                    <a class="dropdown-item" href="#">Reject Order</a>
                                    <a class="dropdown-item" href="#">View Details</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="media mb-4 align-items-center">
                                <div class="media-body">
                                    <input type="hidden" id="pasien_id" value="{{$pasien->id}}">
                                    <input type="hidden" id="rekam_id" value="{{$rekam ? $rekam->id : '' }}">

                                    {{-- Always show patient name --}}
                                    <h3 class="fs-18 font-w600 mb-1"><a href="javascript:void(0)"
                                         class="text-black">{{$pasien->nama}}</a></h3>

                                    {{-- Conditionally show other patient details or lock message --}}
                                    @if($needsDecryption)
                                        <div class="encrypted-content-row text-center mt-3">
                                            <i class="fa fa-lock text-warning fa-2x"></i> 
                                            <p class="mt-2">Patient details are encrypted.</p>
                                            {{-- The decrypt button will be in the Info Detail card below --}}
                                        </div>
                                    @else
                                        {{-- Show patient details if not encrypted or already decrypted --}}
                                        <h4 class="fs-14 font-w600 mb-1">{{$pasien->tmp_lahir.", ".$pasien->tgl_lahir}}</h4>
                                        <h4 class="fs-14 font-w600 mb-1">{{$pasien->agama}}</h4>
                                        <h4 class="fs-14 font-w600 mb-1">{{$pasien->jk.", ".$pasien->status_menikah}}</h4>
                                        <span class="fs-14">{{$pasien->alamat_lengkap}}</span>
                                        <span class="fs-14">{{$pasien->kelurahan.", ".$pasien->kecamatan.", ".$pasien->kabupaten.", ".$pasien->kewarganegaraan}}</span>
                                        <h4 class="fs-14 font-w600 mb-1">Alergi : {{$pasien->alergi}}</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-xxl-7 col-lg-7">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <div>
                                <h4 class="fs-20 text-black mb-1">Info Detail</h4>
                                <span class="fs-12">Rincian Data</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Conditionally show Info Detail content or lock message --}}
                            @if($needsDecryption)
                                <div class="encrypted-content-row text-center">
                                    <i class="fa fa-lock text-warning fa-2x"></i> 
                                    <p class="mt-2">Medical record details are encrypted.</p>
                                    {{-- Add the decrypt button here --}}
                                    <button type="button" class="btn btn-primary btn-sm decrypt-row mt-2" 
                                       data-id="{{ $rekam ? $rekam->id : '' }}">
                                        <i class="fa fa-key"></i> Decrypt File
                                    </button>
                                </div>
                            @else
                                {{-- Show Info Detail content if not encrypted or already decrypted --}}
                                <div class="row align-items-center">
                                    <div class="col-xl-12 col-xxl-6 col-sm-12">
                                        <div class="d-flex align-items-center">
                                            <span class="fs-12 col-6 p-0 text-black">
                                                Cara Bayar
                                            </span>
                                            <div class="col-8 p-0">
                                                <p>{{ $rekam->cara_bayar }}
                                                </p>
                                             </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-12 col-6 p-0 text-black">
                                                Keluhan
                                            </span>
                                            <div class="col-8 p-0">
                                                <p>{!! $rekam->keluhan !!} {{-- Accessor handles decryption --}}
                                                </p>
                                             </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-12 col-6 p-0 text-black">
                                                Diagnosa
                                            </span>
                                            <div class="col-8 p-0">
                                                {{$rekam->diagnosa}} {{-- Accessor handles decryption --}}
                                                {{-- @if ($rekam->poli == "Poli Gigi")
                                                        @foreach ($rekam->gigi() as $item)
                                                        {{$item->diagnosa.", "}} 
                                                        @endforeach
                                                @else
                                                    @foreach ($rekam->diagnosa() as $item)
                                                        {{$item->diagnosis->code}}<br/>{{$item->diagnosis->name_id}}
                                                        <br/><br/>
                                                    @endforeach
                                                @endif --}}
                                                
                                             </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-12 col-6 p-0 text-black">
                                                {{$rekam->poli=="Poli Gigi" ? 'Resep Obat' : 'Tindakan'}}
                                            </span>
                                            {{-- <div class="col-8 p-0"> --}}
                                            {{-- wrap it --}}
                                            <div class="col-8 p-0">
                                                {{-- @if ($rekam->poli == "Poli Gigi")
                                                    {!! $rekam->resep_obat !!} 
                                                @else
                                                @endif --}}
                                                <p>{!! $rekam->tindakan !!}</p> {{-- Accessor handles decryption --}}
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            @endif {{-- End of @if($needsDecryption) else --}}
                        </div>
                    </div>
                </div>
                
                {{-- The "Pemberian Obat" and "Riwayat Obat" sections --}}
                @if ($rekam->status==3)
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="fs-20 text-black mb-0">Pemberian Obat</h4>
                            {{-- Only show the "Selesaikan Proses Ini Tanpa Pemberian Obat" button if not encrypted or already decrypted --}}
                            @if (!$needsDecryption)
                                @if ($rekam)
                                   @if ($rekam->status==3)
                                       @if (auth()->user()->role_display()=="Admin")
                                            <a href="{{Route('rekam.status',[$rekam->id,5])}}" class="btn btn-success">
                                                Selesaikan Proses Ini Tanpa Pemberian Obat
                                                <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                            </a>
                                       @elseif (auth()->user()->role_display()=="Apotek")
                                            <a href="{{Route('rekam.status',[$rekam->id,5])}}" class="btn btn-success">
                                                Selesaikan Proses Ini Tanpa Pemberian Obat
                                                <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                            </a>
                                       @endif
                                    @endif
                                @endif
                            @endif {{-- End of if (!$needsDecryption) --}}
                        </div>
                        <div class="card-body pt-3">
                            {{-- Conditionally show Pemberian Obat form/table or lock message --}}
                            @if($needsDecryption)
                                <div class="encrypted-content-row text-center">
                                    <i class="fa fa-lock text-warning fa-2x"></i> 
                                    <p class="mt-2">Pemberian Obat section is locked until medical record is decrypted.</p>
                                    {{-- The decrypt button is already in the Info Detail card --}}
                                </div>
                            @else
                                {{-- Show Pemberian Obat form/table if not encrypted or already decrypted --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <form method="POST">
                                            {{-- <input type="hidden" class="form-control " id="obat_id"/> --}}
                                            <input type="hidden" class="form-control " id="stok"/>
                                            <input type="hidden" class="form-control " id="obat_code"/>

                                            <div class="form-group">
                                                <label class="text-black font-w500">Nama Obat*</label>
                                                <div class="input-group transparent-append">
                                                    <input type="text" id="obat_id" class="form-control"
                                                      data-toggle="modal" data-target="#modalObat"
                                                     name="obat_id" placeholder="Pilih Obat..">
                                                    <div class="input-group-append show-pass"  data-toggle="modal"
                                                     data-target="#modalObat">
                                                        <span class="input-group-text"> 
                                                            <a href="javascript:void(0)"  data-toggle="modal"
                                                             data-target="#modalObat"><i class="fa fa-search"></i></a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-black font-w500">Nama Obat </label>
                                                <input type="text" id="nama_obat" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-black font-w500">Jumlah* </label>
                                                <input type="number" name="jumlah" id="jumlah" required class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="text-black font-w500">Harga* </label>
                                                <input type="number" name="harga" id="harga" required class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="text-black font-w500">Keterangan </label>
                                                <input type="text" id="keterangan" class="form-control">
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="button" onclick="addObat()" class="btn btn-info">+ Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="table-responsive">
                                            <h5>Obat Yang Akan Dikeluarkan</h5>
                                           <form action="{{Route('obat.pengeluaran.store')}}" method="POST">
                                            {{ csrf_field() }}
                                                <input type="hidden" name="rekam_id" value="{{$rekam->id}}">
                                                <input type="hidden" name="pasien_id" value="{{$pasien->id}}">
                                                <table  id="table-obat"
                                                class="table table-responsive-md table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><strong>Kode</strong></th>
                                                        <th><strong>Nama</strong></th>
                                                        <th><strong>Jumlah</strong></th>
                                                        <th><strong>Harga</strong></th>
                                                        <th><strong>Total</strong></th>
                                                        <th><strong>Keterangan</strong></th>
                                                        <th><strong>#</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                
                                            </table>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">SIMPAN & SELESAIKAN PROSES</button>
                                            </div>
                                           </form>
                                        </div>
                                    </div>
                                </div>
                            @endif {{-- End of @if($needsDecryption) else for Pemberian Obat --}}
                        </div>
                    </div>
                </div>
                @elseif($rekam->status==4 || $rekam->status==5)
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center"> {{-- Added flex utilities --}}
                            <h4 class="fs-20 text-black mb-0">Riwayat Obat</h4>
                            {{-- Modified Export Button --}}
                            @if($pengeluaran->count() > 0) {{-- Only show if there are items --}}
                                {{-- Removed href, added ID and data attribute --}}
                                <button type="button" id="triggerPdfModalBtn" data-rekam-id="{{ $rekam->id }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-print mr-1"></i> Cetak PDF Terproteksi
                                </button>
                            @endif
                        </div>
                        <div class="card-body pt-3">
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <div class="table-responsive">                                      
                                            <table
                                            class="table table-responsive-md table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><strong>Kode</strong></th>
                                                    <th><strong>Nama</strong></th>
                                                    <th><strong>Jumlah</strong></th>
                                                    <th><strong>Harga</strong></th>
                                                    <th><strong>Total</strong></th>
                                                    <th><strong>Keterangan</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengeluaran as $item)
                                                    <tr>
                                                        <td>{{$item->obat->kd_obat}}</td>
                                                        <td>{{$item->obat->nama}}</td>
                                                        <td>{{$item->jumlah}}</td>
                                                        <td>{{number_format($item->harga)}}</td>
                                                        <td>{{number_format($item->subtotal)}}</td>
                                                        <td>{{$item->keterangan}}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                            
                                        </table>
                                       
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    {{-- The passwordPdfModal remains unchanged --}}
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
                <input type="hidden" id="pdfRekamId"> {{-- To store rekam_id --}}
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
    function addObat() {
       var obatNama = $("#nama_obat").val();
       var obatId = $("#obat_id").val();
       var obatCode = $("#obat_code").val();

       var harga = $("#harga").val();
       var stok = $("#stok").val();
       var jumlah = $("#jumlah").val();
       var keterangan = $("#keterangan").val();

       if(jumlah=="" || obatId=="" || harga==""){
            alert("Obat Wajib Dipilih")
       }else{
            if(parseInt(jumlah) > parseInt(stok)){
                alert("Jumlah tidak sesuai stok");
                return; // <-- Add this return statement to stop execution
            }
            var subtotal = parseInt(harga) * parseInt(jumlah);
            var markup = '<tr>'+
                    '<td>'+obatCode+
                        '<input type="hidden" name="obat_id[]" value="'+obatId+'"/>'+
                    '</td>'+
                    '<td>'+obatNama+
                    '</td>'+
                    '<td>'+jumlah+
                        '<input type="hidden" name="jumlah[]" value="'+jumlah+'"/>'+
                    '</td>'+
                    '<td>'+harga+
                        '<input type="hidden" name="harga[]" value="'+harga+'"/>'+
                    '</td>'+
                    '<td>'+subtotal+
                        '<input type="hidden" name="subtotal[]" value="'+subtotal+'"/>'+
                    '</td>'+
                    '<td>'+keterangan+
                        '<input type="hidden" name="keterangan[]" value="'+keterangan+'"/>'+
                    '</td>'+
                    '<td style="width: 80px">'+
                        // '<button type="button" class="btn btn-danger btnDelete">Hapus</button>'+
                        '<a href="#" class="btn btn-danger shadow btn-xs sharp btnDelete"><i class="fa fa-trash"></i></a>'+
                    '</td>'+
                    '</tr>';

             $("#table-obat tbody").append(markup);


       }

     }
    $(function () {
        var table = $('#obat-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging:true,
            select: false,
            pageLength: 5,
            lengthChange:false ,
            ajax: "{{ route('obat.data') }}",
            columns: [
                {data: 'action', name: 'action'},
                {data: 'kd_obat', name: 'kd_obat'},
                {data: 'nama', name: 'nama'},
                {data: 'stok', name: 'stok'},
                {data: 'satuan', name: 'satuan'},
                {data: 'harga', name: 'harga'}
            ]
        });

        // Modify the delete button click handler
        $("#table-obat").on('click','.btnDelete',function(e){ // Add the event parameter 'e'
             e.preventDefault(); // <-- Prevent the default anchor tag behavior (scrolling)
             $(this).closest('tr').remove();
        });

        $('#triggerPdfModalBtn').on('click', function() {
            console.log("Button clicked")
            var rekamId = $(this).data('rekam-id');
            $('#pdfRekamId').val(rekamId); // Store rekam_id in the hidden input
            $('#pdfUserPassword').val(''); // Clear password field
            $('#passwordError').hide(); // Hide any previous errors
            $('#passwordPdfModal').modal('show');
        });

        // 2. Handle password confirmation and AJAX request
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
                url: '{{ route("obat.pengeluaran.verify-password") }}', // Use the named route
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
                        console.log("response ",response)

                        var filename = "PengeluaranObat-" + rekamId + ".pdf"; // Fallback if header parsing fails
                        // --- End Modification ---

                        // Try to get filename from header first (this part remains the same)
                        var disposition = xhr.getResponseHeader('content-disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1 || disposition && disposition.indexOf('inline') !== -1) { // Also check for inline
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


    $(document).on('click', '.decrypt-row', function() {
            var recordId = $(this).data('id');
            
            // Set the rekam_id in the password modal's hidden input
            $('#encrypted_record_id').val(recordId);
            $('#decrypt_error').hide();
            $('#decrypt_password').val('');
            $('#passwordModal').modal('show'); // This line shows the modal
        });
    
        {{-- Add the decryptBtn click handler --}}
        $('#decryptBtn').on('click', function() {
            var recordId = $('#encrypted_record_id').val();
            var password = $('#decrypt_password').val();

            if (!password) {
                $('#decrypt_error').text('Password is required').show();
                return;
            }

            // Disable the button and show loading indicator if needed
            // $(this).prop('disabled', true).text('Decrypting...');

            $.ajax({
                url: "{{ route('rekam.decrypt.row') }}", // Use the new route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: recordId,
                    password: password
                },
                success: function(response) {
                    // Re-enable button and reset text
                    // $('#decryptBtn').prop('disabled', false).text('Decrypt');

                    if (response.success) {
                        $('#passwordModal').modal('hide');
                        // Reload the page to show the decrypted content
                        location.reload();
                    } else {
                        $('#decrypt_error').text(response.message).show();
                    }
                },
                error: function(xhr) {
                    // Re-enable button and reset text
                    // $('#decryptBtn').prop('disabled', false).text('Decrypt');
                    $('#decrypt_error').text('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred')).show();
                }
            });
        });

        // Allow Enter key to submit the decrypt form
        $('#decrypt_password').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#decryptBtn').click();
            }
        });

    $(document).on("click", ".pilihObat", function () {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var harga = $(this).data('harga');
        var stok = $(this).data('stok');
        var satuan = $(this).data('satuan');
        var code = $(this).data('code');
        $("#nama_obat").val(nama);
        $("#obat_id").val(id);
        $("#obat_code").val(code);

        $("#harga").val(harga);
        $("#stok").val(stok);

        // $("#cara_bayar").val(metode).change();

        $("#modalObat").modal('hide');

        // toastr.success("Obat "+nama+" telah dipilih", "Sukses",{timeOut: 3000})
    });


</script>
@endsection