@extends('layout.apps')
@section('content')
<div class="mr-auto">
    <h2 class="text-black font-w600">Manajemen Pengguna</h2>
</div>

{{-- Removed Add Modal as user creation is via registration --}}

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                {{-- Removed Add Button --}}
                {{-- <div class="form-group col-lg-6" style="float: left"> --}}
                    {{-- <a href="javascript:void(0)" class="btn btn-primary mr-3" data-toggle="modal" data-target="#addOrderModal">+Tambah Pengguna</a> --}}
                {{-- </div> --}}
                <div class="form-group col-lg-12" style="float: right"> {{-- Adjusted width --}}
                    <form method="get" action="{{ route('pengguna') }}"> {{-- Changed route --}}
                        <div class="input-group">
                            <input type="text" class="form-control gp-search" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Cari Nama, Email, atau Telepon" autocomplete="off">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default no-border btn-sm gp-search">
                                <i class="ace-icon fa fa-search icon-on-right bigger-110"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Peran Saat Ini</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $key => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $key }}</td> {{-- Correct numbering for pagination --}}
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    {{-- @dd($user) --}}
                                    <td>
                                        {{-- Use different badge colors based on role --}}
                                        @switch($user->role)
                                            @case(1)
                                                <span class="badge badge-danger">{{ $user->role_display() }}</span> {{-- Admin --}}
                                                @break
                                            @case(2)
                                                <span class="badge badge-info">{{ $user->role_display() }}</span> {{-- Pendaftaran --}}
                                                @break
                                            @case(3)
                                                <span class="badge badge-success">{{ $user->role_display() }}</span> {{-- Dokter --}}
                                                @break
                                            @case(4)
                                                <span class="badge badge-warning">{{ $user->role_display() }}</span> {{-- Apotek --}}
                                                @break
                                            @case(5)
                                                <span class="badge badge-primary">{{ $user->role_display() }}</span> {{-- Pasien --}}
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $user->role_display() }}</span> {{-- Default/Other --}}
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            {{-- Edit Button triggers modal --}}
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#editUser{{$user->id}}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-pencil"></i></a>

                                            {{-- Delete button (optional, implement if needed) --}}
                                            <a href="#" class="btn btn-danger shadow btn-xs sharp delete" r-link="Route('pengguna.delete', $user->id) "
                                             r-name="{{$user->name}}" r-id="{{$user->id}}"><i class="fa fa-trash"></i></a>

                                            <!-- Edit User Modal -->
                                            <div class="modal fade" id="editUser{{$user->id}}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Peran Pengguna: {{ $user->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('pengguna.update', $user->id) }}" method="POST">
                                                                @csrf {{-- Use @csrf directive --}}
                                                                {{-- Removed method spoofing as we use POST route --}}
                                                                {{-- @method('PUT') --}}

                                                                <div class="form-group">
                                                                    <label class="text-black font-w500">Nama Pengguna</label>
                                                                    <input type="text" value="{{ $user->name }}" class="form-control" readonly>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label class="text-black font-w500">Ubah Peran ke*</label>
                                                                    <select name="role" class="form-control" required>
                                                                        <option value="">Pilih Peran Baru</option>
                                                                        @foreach ($roles as $roleId => $roleName)
                                                                            <option value="{{ $roleId }}" {{ $user->role == $roleId ? 'selected' : '' }}>
                                                                                {{ $roleName }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('role')
                                                                    <div class="invalid-feedback animated fadeInUp" style="display: block;">{{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="form-group">
                                                                    <button type="submit" class="btn btn-primary">UPDATE PERAN</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pengguna ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- Pagination Links --}}
                    <div class="mt-3">
                        {{ $users->appends(['keyword' => $keyword ?? ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- Include script for delete confirmation if delete functionality is added --}}
<script>
        $().ready( function () {
            $(".delete").click(function() {
                 var id = $(this).attr('r-id');
                 var name = $(this).attr('r-name');
                 var link = $(this).attr('r-link');
                 Swal.fire({
                  title: 'Ingin Menghapus?',
                  text: "Yakin ingin menghapus pengguna : "+name+" ini ?" ,
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ya, hapus !'
                }).then((result) => {
                  if (result.value) {
                      // If using a form for delete: document.getElementById('delete-form-' + id).submit();
                      // If using a link: window.location = link;
                  }
                });
            });
        } );
    </script>
@endsection