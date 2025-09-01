@extends('layouts.main')
@section('content')
<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>

        <div class="search-box">
            <i class="uil uil-search"></i>
            <form action="{{ route('jurnalcek.index') }}" method="GET" class="d-flex align-items-center" style="gap:.5rem;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari mahasiswa/dosen/jurnal...">
                <button class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>

        <img src="/images/profil.png" alt="">
    </div>

    <div class="dash-content">
        <div class="activity">
            <div class="title d-flex align-items-center gap-2">
                <i class="uil uil-clipboard-notes"></i>
                <span class="text">Data Pemakaian Rumah Jurnal</span>
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

            <div class="row justify-content-end mb-3">
                <div class="col-lg-3 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="uil uil-plus"></i> Tambah Pemakaian
                    </button>
                </div>
            </div>

            <table id="datatable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Mahasiswa</th>
                        <th>Dosen</th>
                        <th>Rumah Jurnal</th>
                        <th>Link</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $namaBulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                    @endphp
                    @forelse ($pemakaian as $item)
                        <tr>
                            <td>{{ ($pemakaian->currentPage() - 1) * $pemakaian->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->dosenPembimbing->nama ?? '-' }}</td>
                            <td>{{ $item->rumahJurnal->nama ?? '-' }}</td>
                            <td>
                                @if($item->rumahJurnal?->link)
                                    <a href="{{ $item->rumahJurnal->link }}" target="_blank" rel="noopener noreferrer">
                                        {{ \Illuminate\Support\Str::limit($item->rumahJurnal->link, 36) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $namaBulan[$item->edisi->bulan ?? 0] ?? '-' }}</td>
                            <td>{{ $item->edisi->tahun ?? '-' }}</td>
                            <td class="text-capitalize">{{ $item->status }}</td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-link text-primary p-0 m-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="uil uil-edit"></i>
                                </button>
                                <form action="{{ route('jurnalcek.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0" title="Hapus">
                                        <i class="uil uil-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($pemakaian, 'links'))
                <div class="mt-3">
                    {{ $pemakaian->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

@foreach ($pemakaian as $item)
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('jurnalcek.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pemakaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Mahasiswa</label>
                        <input type="text" name="nama_mahasiswa" class="form-control" value="{{ $item->mahasiswa->nama ?? '' }}" required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dosen Pembimbing</label>
                        <select name="dosen_pembimbing_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($dosenList as $d)
                                <option value="{{ $d->id }}" {{ ($item->dosen_pembimbing_id == $d->id) ? 'selected' : '' }}>{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rumah Jurnal</label>
                        <select name="rumah_jurnal_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($jurnalList as $j)
                                <option value="{{ $j->id }}" {{ ($item->rumah_jurnal_id == $j->id) ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $k=>$v)
                                    <option value="{{ $k }}" {{ (int)($item->edisi->bulan ?? 0) === $k ? 'selected' : '' }}>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ $item->edisi->tahun ?? '' }}" required min="2000" max="2100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach (['pending'=>'Pending','disetujui'=>'Disetujui','dibatalkan'=>'Dibatalkan'] as $k=>$v)
                                <option value="{{ $k }}" {{ $item->status===$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('jurnalcek.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Pemakaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Mahasiswa</label>
                        <input type="text" name="nama_mahasiswa" class="form-control" required maxlength="150" placeholder="Contoh: Paris Alvito">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dosen Pembimbing</label>
                        <select name="dosen_pembimbing_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($dosenList as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rumah Jurnal</label>
                        <select name="rumah_jurnal_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($jurnalList as $j)
                                <option value="{{ $j->id }}">{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $k=>$v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" required min="2000" max="2100" placeholder="contoh: 2025">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
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
