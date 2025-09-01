@extends('layouts.main')
@section('content')
<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>

        {{-- Pencarian (query ke ?q=...) --}}
        <div class="search-box">
            <i class="uil uil-search"></i>
            <form action="{{ route('dosen.index') }}" method="GET" class="d-flex align-items-center" style="gap:.5rem;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama dosen...">
                <button class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>

        <img src="/images/profil.png" alt="">
    </div>

    <div class="dash-content">
        <div class="activity">
            <div class="title d-flex align-items-center gap-2">
                <i class="uil uil-clipboard-notes"></i>
                <span class="text">Data Dosen Pembimbing</span>
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
                        <i class="uil uil-plus"></i> Tambah Dosen
                    </button>
                </div>
            </div>

            <table id="datatable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Nama Dosen</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dosen as $item)
                        <tr>
                            <td>{{ ($dosen->currentPage() - 1) * $dosen->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td class="d-flex gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-link text-primary p-0 m-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="uil uil-edit"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('dosen.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus dosen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0" title="Hapus">
                                        <i class="uil uil-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data dosen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginasi (kalau tidak pakai DataTables server-side tetap berguna) --}}
            @if(method_exists($dosen, 'links'))
                <div class="mt-3">
                    {{ $dosen->links() }}
                </div>
            @endif

        </div>
    </div>
</section>

{{-- Modal Edit untuk tiap item --}}
@foreach ($dosen as $item)
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"
    aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('dosen.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel{{ $item->id }}">Edit Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Dosen</label>
                        <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required maxlength="150">
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

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('dosen.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Dosen Pembimbing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Dosen</label>
                        <input type="text" name="nama" class="form-control" required maxlength="150" placeholder="Contoh: Dr. Budi Santoso, M.Kom">
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

{{-- DataTables (client-side) --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(function() {
        $('#datatable').DataTable({
            // Karena kolom sedikit, opsi default cukup
            // Jika ingin nonaktifkan paging DataTables karena pakai paginasi Laravel, tambahkan:
            // paging: false, searching: false, info: false
        });
    });
</script>
@endsection
