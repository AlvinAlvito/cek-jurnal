@extends('layouts.main')
@section('content')
<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>

        <div class="search-box">
            <i class="uil uil-search"></i>
            <form action="{{ route('kontroljurnal.index') }}" method="GET" class="d-flex align-items-center" style="gap:.5rem;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama/link/label/bulan/tahun...">
                <button class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>

        <img src="/images/profil.png" alt="">
    </div>

    <div class="dash-content">
        <div class="activity">
            <div class="d-flex justify-content-between align-items-center">
                <div class="title d-flex align-items-center gap-2">
                    <i class="uil uil-clipboard-notes"></i>
                    <span class="text">Kontrol Ketersediaan Rumah Jurnal</span>
                </div>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalRules">
                    <i class="uil uil-setting"></i> Atur Rules
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-2">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mt-2">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mt-2">
                    <ul class="m-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            @endphp

            <table id="datatable" class="table table-hover table-striped mt-3">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Rumah Jurnal</th>
                        <th>Link</th>
                        <th>Edisi</th>
                        <th>Terpakai</th>
                        <th>Sisa Slot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($edisi as $item)
                        @php
                            $kapasitas = (int) $item->kuota;
                            $terpakai  = (int) ($item->terpakai ?? 0);
                            $sisa      = max(0, $kapasitas - $terpakai);
                        @endphp
                        <tr>
                            <td>{{ ($edisi->currentPage() - 1) * $edisi->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->rumahJurnal->nama ?? '-' }}</td>
                            <td>
                                @if($item->rumahJurnal?->link)
                                    <a href="{{ $item->rumahJurnal->link }}" target="_blank" rel="noopener noreferrer">
                                        {{ \Illuminate\Support\Str::limit($item->rumahJurnal->link, 48) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ ($namaBulan[$item->bulan] ?? $item->bulan) . ' ' . $item->tahun }}</td>
                            <td>{{ $terpakai }}</td>
                            <td>
                                <span class="badge {{ $sisa > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $sisa }}
                                </span>
                                <small class="text-muted">/ {{ $kapasitas }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada data edisi.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($edisi, 'links'))
                <div class="mt-3">
                    {{ $edisi->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<div class="modal fade" id="modalRules" tabindex="-1" aria-labelledby="modalRulesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('kontroljurnal.rules') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRulesLabel">Pengaturan Rules</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Maksimal jumlah mahasiswa dalam 1 edisi</label>
                        <input type="number" name="max_mahasiswa_per_edisi" class="form-control"
                               value="{{ (int)($rules->max_mahasiswa_per_edisi ?? 2) }}" min="1" max="255" required>
                        <div class="form-text">Mengubah nilai ini dapat diterapkan ke semua edisi.</div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="unik_dosen_per_edisi" value="1"
                               id="unikDosen" {{ (int)($rules->unik_dosen_per_edisi ?? 1) === 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="unikDosen">
                            Dosen pembimbing tidak boleh sama pada satu rumah jurnal di edisi yang sama
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="apply_all" value="1" id="applyAll" checked>
                        <label class="form-check-label" for="applyAll">
                            Terapkan kuota ke seluruh edisi yang ada
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan Rules</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(function() {
        $('#datatable').DataTable();
    });
</script>
@endsection
