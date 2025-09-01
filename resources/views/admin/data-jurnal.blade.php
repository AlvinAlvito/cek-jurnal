@extends('layouts.main')
@section('content')
<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>

        <div class="search-box">
            <i class="uil uil-search"></i>
            <form action="{{ route('jurnal.index') }}" method="GET" class="d-flex align-items-center" style="gap:.5rem;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama/link rumah jurnal...">
                <button class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>

        <img src="/images/profil.png" alt="">
    </div>

    <div class="dash-content">
        <div class="activity">
            <div class="title d-flex align-items-center gap-2">
                <i class="uil uil-clipboard-notes"></i>
                <span class="text">Data Rumah Jurnal</span>
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
                        <i class="uil uil-plus"></i> Tambah Jurnal
                    </button>
                </div>
            </div>

            <table id="datatable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Nama</th>
                        <th>Link</th>
                        <th>Deskripsi</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jurnal as $item)
                        <tr>
                            <td>{{ ($jurnal->currentPage() - 1) * $jurnal->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>
                                <a href="{{ $item->link }}" target="_blank" rel="noopener noreferrer">
                                    {{ \Illuminate\Support\Str::limit($item->link, 45) }}
                                </a>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 80) }}</td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-link text-primary p-0 m-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="uil uil-edit"></i>
                                </button>

                                <form action="{{ route('jurnal.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus jurnal ini?')">
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
                            <td colspan="5" class="text-center">Belum ada data rumah jurnal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($jurnal, 'links'))
                <div class="mt-3">
                    {{ $jurnal->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

@foreach ($jurnal as $item)
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"
    aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('jurnal.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel{{ $item->id }}">Edit Rumah Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" value="{{ $item->link }}" required maxlength="255" placeholder="https://contoh.com/rumah-jurnal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ $item->deskripsi }}</textarea>
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
        <form action="{{ route('jurnal.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Rumah Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required maxlength="150" placeholder="Contoh: Rumah Jurnal ABC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" required maxlength="255" placeholder="https://contoh.com/rumah-jurnal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Opsional"></textarea>
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
