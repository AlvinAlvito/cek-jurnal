<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Cek Jurnal</title>
    <style>
        @page { margin: 20px 25px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#222; }
        h2 { margin: 0 0 8px 0; }
        .meta { font-size: 11px; color:#666; margin-bottom: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:6px 8px; vertical-align: top; }
        th { background:#f4f5f7; text-align:left; }
        .nowrap { white-space: nowrap; }
        .small { font-size: 11px; color:#555; }
        a { color:#0a58ca; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Daftar Cek Jurnal</h2>
    <div class="meta">
        Dibuat: {{ $generated }}
        @isset($q)
            &middot; Filter: <strong>{{ $q }}</strong>
        @endisset
    </div>

    <table>
        <thead>
            <tr>
                <th class="nowrap">No</th>
                <th class="nowrap">Nama Mahasiswa</th>
                <th class="nowrap">Bulan</th>
                <th>Judul Jurnal</th>
                <th class="nowrap">Nama Rumah Jurnal / Link</th>
                <th class="nowrap">Sinta</th>
                <th class="nowrap">Tahun Akreditasi</th>
                <th>Edisi (Jadwal Terbit)</th>
                <th class="nowrap">Waktu</th>
            </tr>
        </thead>
        <tbody>
        @php
            $bulanFull = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
        @endphp

        @forelse ($pemakaian as $row)
            <tr>
                <td class="nowrap">{{ $loop->iteration }}</td>
                <td>{{ $row->mahasiswa->nama ?? '-' }}</td>
                <td class="nowrap">{{ $bulanFull[$row->edisi->bulan ?? 0] ?? '-' }}</td>
                <td>{{ $row->judul_jurnal ?? '-' }}</td>
                <td class="nowrap">
                    {{ $row->rumahJurnal->nama ?? '-' }}<br>
                    @if(!empty($row->rumahJurnal?->link))
                        <span class="small">
                            <a href="{{ $row->rumahJurnal->link }}">{{ $row->rumahJurnal->link }}</a>
                        </span>
                    @endif
                </td>
                <td class="nowrap">
                    @if(!is_null($row->rumahJurnal?->sinta))
                        Sinta {{ $row->rumahJurnal->sinta }}
                    @else
                        -
                    @endif
                </td>
                <td class="nowrap">{{ $row->rumahJurnal->tahun_akreditasi ?? '-' }}</td>
                <td>{{ $row->edisi_label ?? '-' }}</td>
                <td> {{ optional($row->created_at)->format('d M Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#888;">Tidak ada data</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
