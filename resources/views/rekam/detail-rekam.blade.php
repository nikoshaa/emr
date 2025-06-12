@extends('layout.apps')
@section('content')

@include('rekam.partial.modal-pemeriksaan')
{{-- MODAL TINAKAN --}}
@include('rekam.partial.modal-tindakan')
{{-- MODAL Diagnosa --}}
@include('rekam.partial.modal-diagnosa')
{{-- MODAL OBAT --}}
@include('rekam.partial.modal-resep-obat')

<!-- Password Modal for Encrypted Content -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Enter Password to Decrypt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="decryptForm">
                    <input type="hidden" id="encrypted_record_id" name="id"> {{-- Used for row decryption --}}
                    <input type="hidden" id="decrypt_target" name="decrypt_target"> {{-- New: 'pasien_info' or 'rekam_row' --}}
                    <input type="hidden" id="pasien_id_for_decrypt" name="pasien_id" value="{{ $pasien->id ?? '' }}"> {{-- Patient ID for decrypting patient info --}}
                    <div class="form-group">
                        <label for="decrypt_password">Password</label>
                        <input type="password" class="form-control" id="decrypt_password" name="password" required>
                    </div>
                    <div id="decrypt_error" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="decryptBtn">Decrypt</button>
            </div>
        </div>
    </div>
</div>

{{-- DATA --}}
    <div class="row">   
        <div class="col-xl-12">
            <div class="row">
                <div class="col-sm-12 col-sm-5 col-lg-5">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="fs-20 text-black mb-0">Detail Pasien</h4>
                            <div class="dropdown">
                                RM#  {{$pasien->no_rm}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="locked-detail-pasien">
                                <p><i class="fa fa-lock text-warning"></i> Data pasien terkunci.</p>
                                <button type="button" class="btn btn-primary btn-sm mt-2" id="unlock-pasien-info-btn">
                                    <i class="fa fa-key"></i> Buka Data Pasien
                                </button>
                            </div>
                            <div class="media mb-4 align-items-center" id="decrypted-detail-pasien" style="display: none;">
                                <div class="media-body">
                                    <input type="hidden" id="pasien_id" value="{{$pasien->id}}">
                                    <input type="hidden" id="rekam_id" value="{{$rekamLatest ? $rekamLatest->id : '' }}">

                                    <h3 class="fs-18 font-w600 mb-1"><a href="javascript:void(0)"
                                         class="text-black">{{$pasien->nama}}</a></h3>
                                    <h4 class="fs-14 font-w600 mb-1">{{$pasien->tmp_lahir.", ".$pasien->tgl_lahir}}</h4>
                                    @php
                                        $b_day = \Carbon\Carbon::parse($pasien->tgl_lahir); // Tanggal Lahir
                                        $now = \Carbon\Carbon::now();
                                    @endphp
                                    <h4 class="fs-14 font-w600 mb-1">{{"Usia : ".$b_day->diffInYears($now) }}</h4>
                                    
                                    <h4 class="fs-14 font-w600 mb-1">{{$pasien->jk.", ".$pasien->status_menikah}}</h4>
                                    <span class="fs-14">{{$pasien->alamat_lengkap}}</span>
                                    <span class="fs-14">{{$pasien->keluhan.", ".$pasien->kecamatan.", ".$pasien->kabupaten.", ".$pasien->kewarganegaraan}}</span>
                                    {{-- <textarea name="analysis" class="form-control" id="editor" cols="30" rows="10"></textarea> --}}
                                    <br>
                                    @if ($pasien->isRekamGigi())
                                        <a href="{{Route('rekam.gigi.odontogram',$pasien->id)}}" style="width: 120px"
                                            class="btn-rounded btn-info btn-xs "><i class="fa fa-eye"></i> Odontogram</a>
                                    @endif
                                    
                                </div>
                            </div>
                         
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-sm-7 col-lg-7">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="fs-20 text-black mb-0">Info Pasien</h4>
                            <div class="dropdown">
                                 @if ($rekamLatest)
                                    {!! $rekamLatest->status_display() !!}
                                @endif 
                                @if (auth()->user()->role_display()=="Admin" || auth()->user()->role_display()=="Pendaftaran")
                                <a href="{{Route('pasien.edit',$pasien->id)}}" style="width: 120px"
                                    class="btn-rounded btn-info btn-xs "><i class="fa fa-pencil"></i> Edit Pasien</a>
                                @endif
                              
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div id="locked-info-pasien">
                                    <p><i class="fa fa-lock text-warning"></i> Info pasien terkunci.</p>
                                    {{-- <button type="button" class="btn btn-primary btn-sm mt-2" id="unlock-pasien-info-btn">
                                        <i class="fa fa-key"></i> Buka Data Pasien
                                    </button> --}}
                                </div>
                                <div class="col-xl-12 col-xxl-6 col-sm-6" id="decrypted-info-pasien" style="display: none;">
                                    <div class="d-flex mb-3 align-items-center">
                                        <span class="fs-12 col-6 p-0 text-black">
                                            <svg class="mr-2" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="19" height="19" fill="#5F74BF"/>
                                            </svg>
                                            No HP
                                        </span>
                                        <div class="col-8 p-0">
                                           <p>{{$pasien->no_hp}}</p>
                                        </div>
                                    </div>
                                   
                                    <div class="d-flex align-items-center">
                                        <span class="fs-12 col-6 p-0 text-black">
                                            <svg class="mr-2" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="19" height="19" fill="#5FBF91"/>
                                            </svg>
                                            Pembayaran
                                        </span>
                                        <div class="col-8 p-0">
                                           @if ($rekamLatest)
                                            <p>{{$rekamLatest->cara_bayar}}</p>
                                            <p>{{$pasien->no_bpjs}}</p>
                                           @else 
                                            <p>{{$pasien->cara_bayar}}</p>
                                            <p>{{$pasien->no_bpjs}}</p>
                                           @endif
                                           
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3 align-items-center">
                                        <span class="fs-12 col-6 p-0 text-black">
                                            <svg class="mr-2" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="19" height="19" fill="#5F74BF"/>
                                            </svg>
                                            Alergi
                                        </span>
                                        <div class="col-8 p-0">
                                            {{-- @dd($pasien->alergi) --}}
                                           @if($pasien->alergi)
                                           <p>{{$pasien->alergi}}</p>
                                           @else
                                           <p class="text-muted">Tidak ada alergi</p>
                                           @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-12 col-6 p-0 text-black">
                                            <svg class="mr-2" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="19" height="19" fill="#5FBF91"/>
                                            </svg>
                                            Files
                                        </span>
                                        <div class="col-8 p-0">
                                          @php
                                              $pasienFiles = \App\Models\PasienFile::where('pasien_id', $pasien->id)->get();
                                          @endphp
                                          
                                          @if ($pasienFiles->count() > 0)
                                            <div class="file-list">
                                              @foreach($pasienFiles as $file)
                                                @php
                                                  $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                                  $icon = 'fa-file-o';
                                                  $color = 'text-secondary';
                                                  
                                                  if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $icon = 'fa-file-image-o';
                                                    $color = 'text-info';
                                                  } elseif(strtolower($extension) === 'pdf') {
                                                    $icon = 'fa-file-pdf-o';
                                                    $color = 'text-danger';
                                                  } elseif(in_array(strtolower($extension), ['xls', 'xlsx', 'csv'])) {
                                                    $icon = 'fa-file-excel-o';
                                                    $color = 'text-success';
                                                  }
                                                @endphp
                                                <div class="mb-1">
                                                  <a href="{{ asset($file->file_path) }}" target="_blank" class="file-link">
                                                    <i class="fa {{ $icon }} {{ $color }} mr-1"></i> 
                                                    <span class="file-name">{{ Str::limit($file->original_name, 20) }}</span>
                                                  </a>
                                                </div>
                                              @endforeach
                                            </div>
                                            
                                            {{-- @if (auth()->user()->role_display()=="Admin" || auth()->user()->role_display()=="Pendaftaran")
                                              <a href="{{Route('pasien.file',$pasien->id)}}" class="btn btn-outline-info btn-xs mt-2">
                                                <i class="fa fa-upload"></i> Upload Files
                                              </a>
                                            @endif --}}
                                          @else 
                                            <span class="text-muted">No files available</span>
                                            
                                          @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 text-black mb-0">Rekam Medis Pasien</h4>
                    @if ($rekamLatest)
                        @if ($rekamLatest->status==1)
                            @if (auth()->user()->role_display()=="Admin" ||
                                 auth()->user()->role_display()=="Pendaftaran")
                                <a href="{{Route('rekam.status',[$rekamLatest->id,2])}}" class="btn btn-primary">
                                    Lanjutkan Ke Dokter
                                    <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                </a>
                            @endif
                        @elseif ($rekamLatest->status==2)
                           @if (auth()->user()->role_display()=="Admin" || auth()->user()->role_display()=="Dokter")
                                <a href="{{Route('rekam.status',[$rekamLatest->id,3])}}" class="btn btn-primary">
                                    Selesaikan Pemeriksaan & Perawatan
                                    <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                </a>
                           @endif
                        @elseif ($rekamLatest->status==4)
                           @if (auth()->user()->role_display()=="Admin" || auth()->user()->role_display()=="Pendaftaran")
                                <a href="{{Route('rekam.status',[$rekamLatest->id,5])}}" class="btn btn-primary">
                                    Selesaikan Pembayaran & Rekam Medis ini
                                    <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                </a>
                           @endif
                        @elseif ($rekamLatest->status==3)
                           @if (auth()->user()->role_display()=="Admin")
                                <a href="{{Route('rekam.status',[$rekamLatest->id,5])}}" class="btn btn-primary">
                                    Selesaikan Rekam Medis Ini
                                    <span class="btn-icon-right"><i class="fa fa-check"></i></span>
                                </a>
                           @endif
                       
                        @endif
                    @endif
                    
                </div>
                <div class="card-body">
                   
                    <div class="table-responsive card-table"> 
                        <div class="form-group col-lg-6" style="float: right">
                            <form method="get" action="{{ url()->current() }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control gp-search"
                                        name="keyword" value="{{request('keyword')}}" placeholder="Cari tanggal periksa" value="" autocomplete="off">
                                       
                                    </div>
                                    <div class="col-md-6">
                                        <select name="poli" id="poli" class="form-control"  onchange="this.form.submit()">
                                            <option value="">Semua Rekam</option>
                                            @foreach ($poli as $item)
                                                @if ($rekamLatest)
                                                    @if (request('poli') == $item->nama)
                                                    <option value="{{$item->nama}}" selected>{{$item->nama}}</option>  
                                                    @else 
                                                    <option value="{{$item->nama}}">{{$item->nama}}</option>
                                                    @endif
                                                @else 
                                                    <option value="{{$item->nama}}">{{$item->nama}}</option>
        
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                            </form>
        
                        </div>
                        <table class="table table-responsive-md table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tgl Periksa</th>
                                    <th>Dokter</th>
                                    <th>Anamnesa (S)</th>
                                    <th>Pemeriksaan (O)</th>
                                    <th>Diagnosa (A)</th>
                                    <th>Tindakan (P)</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekams as $key=>$row)
                                {{-- @dd($row) --}}
                                    <tr>
                                        <td>{{ $rekams->firstItem() + $key }}</td>
                                    <td>{{$row->tgl_rekam}}</td>
                                    <td>{{$row->dokter->nama}}
                                        <br><strong>{{$row->poli}}</strong>
                                    </td>
                                    
                                    {{-- Check if the record is encrypted AND needs decryption based on status and flag --}}
                                    @php
                                        // Assuming auth()->user()->role_display() gives the role string like "Dokter", "Admin", etc.
                                        // And $row->status is a numeric code.
                                        // The condition $row->status == auth()->user()->role seems logically incorrect.
                                        // A more typical condition might be checking if the user's role is allowed to decrypt
                                        // AND the record is in a status where decryption is relevant (e.g., status 2 or 3 for Dokter).
                                        // However, following the prompt exactly:
                                        
                                        // dd(auth()->user()->role-1, $row->status);
                                        $needsDecryption = ($row->status <= auth()->user()->role-1) // This condition seems unusual, verify your logic
                                                           && ($row->is_decrypted == 0);
                                                    // dd($needsDecryption)
                                    @endphp

                                    @if($needsDecryption)
                                        <!-- If encrypted and needs decryption, show a single cell with decrypt button -->
                                        <td colspan="4" class="text-center">
                                            <div class="encrypted-content-row">
                                                <i class="fa fa-lock text-warning"></i> 
                                                <span>Medical record data is encrypted</span>
                                                <br>
                                                <button type="button" class="btn btn-primary btn-sm decrypt-row mt-2" 
                                                   data-id="{{ $row->id }}">
                                                    <i class="fa fa-key"></i> Decrypt Record
                                                </button>
                                            </div>
                                        </td>
                                    @else
                                        <!-- If not encrypted, or already decrypted, show normal cells -->
                                        <td>
                                            {!! $row->keluhan !!} {{-- Accessors handle decryption if is_decrypted is 1 --}}
                                        </td>
                                        <td>
                                            {{-- @if ($row->poli=="Poli Gigi")
                                                @foreach ($row->gigi() as $item)
                                                    <li>Gigi {{$item->elemen_gigi}} : {{$item->pemeriksaan}}</li>
                                                @endforeach
                                            @else  --}}
                                                <div>{!! $row->pemeriksaan !!}</div> {{-- Main content --}}
                                                @if ($row->pemeriksaan_file !=null)
                                                  <div style="margin-top: 5px;"> {{-- Add some space if desired --}}
                                                      <a target="__BLANK"
                                                         href="{{$row->getFilePemeriksaan()}}"> <u style="color:rgb(28, 85, 231);">Lihat Foto</u></a>
                                                  </div>
                                                @endif
                                            {{-- @endif --}}
                                        </td>
                                        <td>
                                            {{-- @if ($row->poli=="Poli Gigi")
                                                @foreach ($row->gigi() as $item)
                                                    <li>{{$item->diagnosa.", ".$item->diagnosis->name_id}}</li>
                                                @endforeach
                                            @else  --}}
                                                <div >{!! $row->diagnosa !!}</div> {{-- Main content --}}
                                                @if ($row->diagnosa_file !=null)
                                                  <div > {{-- Add some space if desired --}}
                                                    <a target="__BLANK"
                                                       href="{{$row->getFileDiagnosa()}}"> <u style="color:rgb(28, 85, 231);">Lihat Foto</u></a>
                                                  </div>
                                                @endif
                                            {{-- @endif --}}
                                        </td>
                                        <td>
                                            {{-- @if ($row->poli=="Poli Gigi")
                                                @foreach ($row->gigi() as $item)
                                                    <li>{{$item->tindak->nama}}</li>
                                                @endforeach
                                            @else  --}}
                                                <div>{!! $row->tindakan !!}</div> {{-- Main content --}}
                                                @if ($row->tindakan_file !=null)
                                                  <div style="margin-top: 5px;"> {{-- Add some space if desired --}}
                                                    <a target="__BLANK" href="{{$row->getFileTindakan()}}"> <u style="color:rgb(28, 85, 231);">Lihat Foto</u></a>
                                                  </div>
                                                @endif
                                            {{-- @endif --}}
                                        </td>
                                            {{-- @endif --}}
                                        @endif {{-- End of @if($needsDecryption) else --}}
                                    <td>
                                        
                                    @if ($row->status!=5 && $row->status!=4)
                                    <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                                       {{-- @if ($row->poli!="Poli Gigi") --}}
                                       @if (true)
                                            @if (auth()->user()->role_display() == "Dokter" 
                                            || auth()->user()->role_display() == "Admin"
                                            || auth()->user()->role_display() == "Pendaftaran")
                                                {{-- Only show edit buttons if not encrypted or already decrypted --}}
                                                @if (!$needsDecryption)
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#addPemeriksaan"
                                                    data-id="{{$row->id}}" data-tanggal="{{$row->tgl_rekam}}"
                                                    data-pemeriksaan="{{$row->pemeriksaan}}" style="width: 120px"
                                                    class="btn-rounded btn-info btn-xs addPemeriksaan"><i class="fa fa-pencil"></i> Object</a>
                                                @endif
                                            @endif
                                                
                                            @if (auth()->user()->role_display() == "Dokter" || auth()->user()->role_display() == "Admin")
                                                {{-- Only show edit buttons if not encrypted or already decrypted --}}
                                                @if (!$needsDecryption)
                                                    <a href="javascript:void(0)" data-toggle="modal" 
                                                        data-target="#addDiagnosa"
                                                        data-id="{{$row->id}}" data-tanggal="{{$row->tgl_rekam}}"
                                                        data-tindakan="{{$row->tindakan}}" style="width: 120px"
                                                        class="btn-rounded btn-primary btn-xs addDiagnosa">
                                                        <i class="fa fa-pencil"></i>Assessment</a>
                                                        
                                                        <a href="javascript:void(0)" data-toggle="modal" 
                                                        data-target="#addTindakan"
                                                        data-id="{{$row->id}}" data-tanggal="{{$row->tgl_rekam}}"
                                                        data-tindakan="{{$row->tindakan}}" style="width: 120px"
                                                        class="btn-rounded btn-success btn-xs addTindakan">
                                                        <i class="fa fa-pencil"></i>Plan</a>
                                                @endif
                                            @endif
                                        @else 
                                            {{-- @if (auth()->user()->role_display() == "Dokter" 
                                            || auth()->user()->role_display() == "Admin")
                                                @if (!$needsDecryption)
                                                    <a href="{{Route('rekam.gigi.add',$row->id)}}" style="width: 120px"
                                                    class="btn-rounded btn-info btn-xs "><i class="fa fa-pencil"></i> Rekam</a>

                                                    @if ($row->gigi()->count() > 0)
                                                        <a href="javascript:void(0)" data-toggle="modal" 
                                                        data-target="#addResep"
                                                        data-id="{{$row->id}}" data-tanggal="{{$row->tgl_rekam}}"
                                                        data-resep="{{$row->resep_obat}}" style="width: 120px"
                                                        class="btn-rounded btn-success btn-xs addResep">
                                                        <i class="fa fa-pencil"></i>Resep Obat</a>
                                                    @endif
                                                @endif

                                            @endif --}}
                                        @endif 
                                        
                                       
                                    </div>
                                    @else
                                        <div class="d-flex">
                                            <a href="{{Route('obat.pengeluaran',$row->id)}}" style="width: 120px" class="btn-rounded btn-primary btn-xs ">
                                                <i class="fa fa-eye"></i> Obat</a>
                                        </div>                                                   
                                    @endif
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            
                        </table>
                        <div class="dataTables_info" id="example_info" role="status"
                        aria-live="polite">Showing {{$rekams->firstItem()}} to {{$rekams->perPage() * $rekams->currentPage()}} of {{$rekams->total()}} entries</div>
   
                       {{ $rekams->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
<script src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
<script>
    $(function () {
        var table = $('#icd-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging:true,
            select: false,
            pageLength: 5,
            lengthChange:false ,
            ajax: "{{ route('icd.data') }}",
            columns: [
                {data: 'action', name: 'action'},
                {data: 'code', name: 'code'},
                {data: 'name_id', name: 'name_id'}
            ]
        });
        
    });
    
    CKEDITOR.addCss('.cke_editable p { margin: 0 !important; }');
    CKEDITOR.replace('editor', {
        height  : '250px',
        // filebrowserUploadUrl: "{{route('rekam.upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
        toolbarGroups: [
		{ name: 'document',	   groups: [ 'mode', 'document' ] },		
 		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },			
        // { name: 'insert', groups: [ 'Image'] },
	]
    });

    CKEDITOR.replace('editor2', {
        height  : '250px',
        // filebrowserUploadUrl: "{{route('rekam.upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
        toolbarGroups: [
		{ name: 'document',	   groups: [ 'mode', 'document' ] },		
 		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },			
        // { name: 'insert', groups: [ 'Image'] },
	]
    });

    CKEDITOR.replace('editor3', {
        height  : '250px',
        // filebrowserUploadUrl: "{{route('rekam.upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
        toolbarGroups: [
		{ name: 'document',	   groups: [ 'mode', 'document' ] },		
 		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },			
        // { name: 'insert', groups: [ 'Image'] },
	]
    });
   
    $(document).on("click", ".addPemeriksaan", function () {
        var rekamId = $(this).data('id');
        var pemeriksaan = $(this).data('pemeriksaan');
        $(".modal-body #rekamId").val( rekamId );
        if(pemeriksaan=="--"){
            pemeriksaan = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%">'+
                    '<tbody>'+
                        '<tr>'+
                            '<td style="width:20%">TD</td>'+
                            '<td style="width:2%">:</td>'+
                            '<td>&nbsp;</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td>Temp</td>'+
                            '<td>:</td>'+
                            '<td>&nbsp;</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td>Resp</td>'+
                            '<td>:</td>'+
                            '<td>&nbsp;</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td>Nadi</td>'+
                            '<td>:</td>'+
                            '<td>&nbsp;</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td>BB</td>'+
                            '<td>:</td>'+
                            '<td>&nbsp;</td>'+
                        '</tr>'+
                        
                    '</tbody>'+
                '</table>'+
                '<p>&nbsp;</p>';
        }
        CKEDITOR.instances.editor.setData( pemeriksaan );

    });

    $(document).on("click", ".pilihIcd", function () {
        var diagnosa_id = $(this).data('id');
        var rekam_id = $("#rekam_id").val();
        var pasien_id = $("#pasien_id").val();
        var token = '{{csrf_token()}}';
        $("#addDiagnosa").modal('hide');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });   
        $.ajax({
           type:'POST',
           url:"{{ route('diagnosa.update') }}",
           data:{rekam_id:rekam_id, pasien_id:pasien_id, diagnosa:diagnosa_id,_token:token},
           success:function(data){
                location.reload();
           }
        });

        
    });

    $(document).on("click", ".addTindakan", function () {
        var rekamId = $(this).data('id');
        var tindakan = $(this).data('tindakan');
        $(".modal-body #rekamId").val( rekamId );
        CKEDITOR.instances.editor2.setData( tindakan );
    });

    $(document).on("click", ".addDiagnosa", function () {
        
        var rekamId = $(this).data('id');
        var diagnosa = $(this).data('diagnosa');
        $(".modal-body #rekamId").val( rekamId );
        CKEDITOR.instances.editor2.setData( diagnosa );
    });

    $(document).on("click", ".addResep", function () {
        var rekamId = $(this).data('id');
        var resep = $(this).data('resep');
        $(".modal-body #rekamId").val( rekamId );
        CKEDITOR.instances.editor3.setData( resep );

    });

    

    $(document).on("click", ".addResep", function () {
        var rekamId = $(this).data('id');
        var resep = $(this).data('resep');
        $(".modal-body #rekamId").val( rekamId );
        CKEDITOR.instances.editor3.setData( resep );

    });

    $(document).on('click', '.decrypt-row', function() {
        var recordId = $(this).data('id');
        
        $('#encrypted_record_id').val(recordId);
        $('#decrypt_target').val('rekam_row'); // Set target for record decryption
        $('#decrypt_error').hide();
        $('#decrypt_password').val('');
        $('#passwordModal').modal('show');
    });
    
    $('#unlock-pasien-info-btn').on('click', function() {
        $('#decrypt_target').val('pasien_info'); // Set target for patient info decryption
        // encrypted_record_id is not strictly needed here, but modal expects it. Can be left blank or use pasien_id.
        // $('#encrypted_record_id').val($('#pasien_id_for_decrypt').val()); 
        $('#decrypt_error').hide();
        $('#decrypt_password').val('');
        $('#passwordModal').modal('show');
    });
    
    $('#decryptBtn').on('click', function() {
        var password = $('#decrypt_password').val();
        var target = $('#decrypt_target').val();
        var token = "{{ csrf_token() }}";

        if (!password) {
            $('#decrypt_error').text('Password is required').show();
            return;
        }
        $('#decrypt_error').hide();

        if (target === 'rekam_row') {
            var recordId = $('#encrypted_record_id').val();
            $.ajax({
                url: "{{ route('rekam.decrypt.row') }}", 
                type: "POST",
                data: {
                    _token: token,
                    id: recordId,
                    password: password
                },
                success: function(response) {
                    if (response.success) {
                        $('#passwordModal').modal('hide');
                        location.reload(); 
                    } else {
                        $('#decrypt_error').text(response.message || 'Decryption failed.').show();
                    }
                },
                error: function(xhr) {
                    $('#decrypt_error').text('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred')).show();
                }
            });
        } else if (target === 'pasien_info') {
            var pasienId = $('#pasien_id_for_decrypt').val();
            $.ajax({
                url: "{{ route('obat.pengeluaran.verify-password') }}", // You'll need to create this route
                type: "POST",
                data: {
                    _token: token,
                    rekam_id: 99999999,
                    is_decrypted_pasien: 1,
                    password: password
                },
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.success) {
                        // remove display none from decrypted-detail-pasien
                        $('#decrypted-detail-pasien').show();
                        $('#decrypted-info-pasien').show();
                        
                        $('#locked-detail-pasien').hide(); // Hide the lock message and button
                        $('#locked-info-pasien').hide(); // Hide the lock message and button
                        $('#passwordModal').modal('hide');
                    } else {
                        $('#decrypt_error').text(response.message || 'Failed to decrypt patient information.').show();
                    }
                },
                error: function(xhr) {
                    $('#decrypt_error').text('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred')).show();
                }
            });
        }
    });
    
    $('#decrypt_password').keypress(function(e) {
        if (e.which == 13) { // Enter key pressed
            e.preventDefault();
            $('#decryptBtn').click();
        }
    });

  
</script>
@endsection