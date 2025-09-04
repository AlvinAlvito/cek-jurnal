@extends('layouts.main')
@section('content')
    <style>

    </style>
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>

            <div class="search-box">
                <i class="uil uil-search"></i>
                <form action="{{ route('jurnal.index') }}" method="GET" class="d-flex align-items-center" style="gap:.5rem;">
                    <input type="text" name="q" value="{{ $q ?? '' }}"
                        placeholder="Cari nama/link/edisi/sinta/tahun...">
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

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nama</th>
                                <th>Link</th>
                                <th class="text-center" style="width:90px;">Sinta</th>
                                <th class="text-center" style="width:150px;">Tahun Akreditasi</th>
                                <th class="text-center" style="width:200px;">Edisi</th>
                                <th style="width:110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $labelBulan = [
                                    'jan' => 'Jan',
                                    'feb' => 'Feb',
                                    'mar' => 'Mar',
                                    'apr' => 'Apr',
                                    'mei' => 'Mei',
                                    'jun' => 'Jun',
                                    'jul' => 'Jul',
                                    'agu' => 'Agu',
                                    'sep' => 'Sep',
                                    'okt' => 'Okt',
                                    'nov' => 'Nov',
                                    'des' => 'Des',
                                ];
                            @endphp

                            @forelse ($jurnal as $item)
                                <tr>
                                    <td>{{ ($jurnal->currentPage() - 1) * $jurnal->perPage() + $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->nama }}</td>
                                    <td>
                                        <a href="{{ $item->link }}" target="_blank" rel="noopener noreferrer">
                                            {{ \Illuminate\Support\Str::limit($item->link, 45) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match ($item->sinta) {
                                                4 => 'bg-secondary',
                                                3 => 'bg-primary',
                                                2 => 'bg-success',
                                                default => 'bg-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">S{{ $item->sinta }}</span>
                                    </td>

                                    <td class="text-center">{{ $item->tahun_akreditasi }}</td>
                                    <td class="text-center">
                                        @forelse ((array) $item->edisi as $m)
                                            <span class="badge bg-secondary me-1 text-uppercase">
                                                {{ $labelBulan[$m] ?? strtoupper($m) }}
                                            </span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <button class="btn btn-link text-primary p-0 m-0" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                                <i class="uil uil-edit"></i>
                                            </button>

                                            <form action="{{ route('jurnal.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus jurnal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 m-0"
                                                    title="Hapus">
                                                    <i class="uil uil-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data rumah jurnal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </section>

    {{-- Modal Edit --}}
    @php
        $bulanAll = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
        $labelBulan = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'mei' => 'Mei',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'agu' => 'Agu',
            'sep' => 'Sep',
            'okt' => 'Okt',
            'nov' => 'Nov',
            'des' => 'Des',
        ];
    @endphp

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
                                <input type="text" name="nama" class="form-control" value="{{ $item->nama }}"
                                    required maxlength="150">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Link</label>
                                <input type="url" name="link" class="form-control" value="{{ $item->link }}"
                                    required maxlength="255" placeholder="https://contoh.com/rumah-jurnal">
                            </div>

                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label class="form-label">Sinta</label>
                                    <select name="sinta" class="form-select" required>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}"
                                                {{ (int) $item->sinta === $i ? 'selected' : '' }}>S{{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-8 mb-3">
                                    <label class="form-label">Tahun Akreditasi</label>
                                    <input type="number" name="tahun_akreditasi" class="form-control"
                                        value="{{ $item->tahun_akreditasi }}" min="2000" max="2100" required>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label d-block">Edisi (boleh pilih lebih dari satu)</label>
                                <div class="row g-2">
                                    @php $edChecked = (array) $item->edisi; @endphp
                                    @foreach ($bulanAll as $b)
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="ed-{{ $item->id }}-{{ $b }}" name="edisi[]"
                                                    value="{{ $b }}"
                                                    {{ in_array($b, $edChecked, true) ? 'checked' : '' }}>
                                                <label class="form-check-label text-uppercase"
                                                    for="ed-{{ $item->id }}-{{ $b }}">
                                                    {{ $labelBulan[$b] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Contoh: centang Jan, Mar, Mei.</small>
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
                            <input type="text" name="nama" class="form-control" required maxlength="150"
                                placeholder="Contoh: Rumah Jurnal ABC">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" class="form-control" required maxlength="255"
                                placeholder="https://contoh.com/rumah-jurnal">
                        </div>

                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label">Sinta</label>
                                <select name="sinta" class="form-select" required>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">S{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-8 mb-3">
                                <label class="form-label">Tahun Akreditasi</label>
                                <input type="number" name="tahun_akreditasi" class="form-control" min="2000"
                                    max="2100" required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label d-block">Edisi (boleh pilih lebih dari satu)</label>
                            <div class="row g-2">
                                @foreach ($bulanAll as $b)
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="add-ed-{{ $b }}" name="edisi[]"
                                                value="{{ $b }}">
                                            <label class="form-check-label text-uppercase"
                                                for="add-ed-{{ $b }}">
                                                {{ $labelBulan[$b] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Contoh: centang Jan, Mar, Mei.</small>
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

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#datatable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Tidak ada data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data tersedia',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        });
    </script>
@endsection
