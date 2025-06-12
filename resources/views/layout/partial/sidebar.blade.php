<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li><a href="{{Route('dashboard')}}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>


            @if (auth()->user()->role_display()=='Pendaftaran'
            )
             <li><a class="has-arrow ai-icon patient-icon" href="javascript:void()" aria-expanded="false">
                <i class="fa fa-user"></i>
                    <span class="nav-text">Pasien</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{Route('pasien')}}">Data Pasien</a></li>
                    <li><a href="{{Route('pasien.add')}}">Pasien Baru</a></li>
                </ul>
            </li>
            <li><a href="{{Route('rekam')}}" class="ai-icon medical-icon" aria-expanded="false">
                <i class="fa fa-medkit"></i>
                    <span class="nav-text">Rekam Medis</span>
                </a>
            </li>
            @elseif (auth()->user()->role_display()=='Dokter')
            <li><a href="{{Route('rekam',['tab'=>2])}}" class="ai-icon medical-icon" aria-expanded="false">
                <i class="fa fa-medkit"></i>
                    <span class="nav-text">Rekam Medis</span>
                </a>
            </li>
            @endif
            @if (auth()->user()->role_display()=='Apotek')
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-battery"></i>
                        <span class="nav-text">Apotek</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{Route('obat')}}">Data Obat</a></li>
                        <li><a href="{{Route('obat.riwayat')}}">Riwayat Keluar Obat</a></li>

                    </ul>
                </li>

            @endif
            
            @if (auth()->user()->role_display()=='Admin')
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-notepad"></i>
                    <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{Route('pengguna')}}">Pengguna</a></li>
                    <li><a href="{{Route('tindakan')}}">Tindakan</a></li>
                    <li><a href="{{Route('petugas')}}">Petugas</a></li>
                    <li><a href="{{Route('poli')}}">Poli</a></li>
                    <li><a href="{{Route('dokter')}}">Dokter</a></li>
                    <li><a href="{{Route('icd')}}">ICD</a></li>
                </ul>
                </li>
            @endif

            @if (auth()->user()->role != 5)
            <li>
                <a href="{{ route('chat.staff') }}" class="ai-icon" aria-expanded="false">
                    <i class="fa fa-comment"></i>
                    <span class="nav-text">Staff Chat</span>
                </a>
            </li>
            @endif
        </ul>

        <div class="copyright">
            <p><strong>NeoSIMRS - Sistem Informasi Rumah Sakit</strong> © 2025 All Rights Reserved</p>
        </div>
    </div>
</div>
