<?php

namespace App\Http\Controllers;

use App\Models\EdisiRumahJurnal;
use App\Models\PemakaianRumahJurnal;
use App\Models\RumahJurnal;
use App\Models\DosenPembimbing;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class JurnalcekController extends Controller
{
    public function index()
    {
        $q = request('q');
        $perPage = (int) (request('per_page') ?? 100);

        $query = PemakaianRumahJurnal::query()
            ->with(['mahasiswa', 'dosenPembimbing', 'rumahJurnal', 'edisi'])
            ->orderByDesc('id');

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->whereHas('mahasiswa', fn($m) => $m->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('dosenPembimbing', fn($d) => $d->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('rumahJurnal', fn($r) => $r->where('nama', 'like', "%{$q}%")->orWhere('link', 'like', "%{$q}%"))
                    ->orWhereHas('edisi', fn($e) => $e->where('tahun', 'like', "%{$q}%")->orWhere('bulan', 'like', "%{$q}%"))
                    ->orWhere('judul_jurnal', 'like', "%{$q}%"); // <-- filter judul_jurnal
            });
        }

        $data = $query->paginate($perPage)->appends(request()->query());
        $dosenList = DosenPembimbing::orderBy('nama')->get(['id', 'nama']);
        $jurnalList = RumahJurnal::orderBy('nama')->get(['id', 'nama', 'link']);

        return view('admin.data-jurnalcek', [
            'title' => 'Data Pemakaian Rumah Jurnal',
            'pemakaian' => $data,
            'dosenList' => $dosenList,
            'jurnalList' => $jurnalList,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:150'],
            'dosen_pembimbing_id' => ['nullable', 'integer', 'exists:dosen_pembimbing,id'],
            'rumah_jurnal_id' => ['required', 'integer', 'exists:rumah_jurnal,id'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'judul_jurnal' => ['nullable', 'string', 'max:300'], // <-- validasi judul
        ]);

        $mhs = Mahasiswa::where('nama', $validated['nama_mahasiswa'])->first();
        if (!$mhs) {
            $mhs = Mahasiswa::create([
                'nama' => $validated['nama_mahasiswa'],
                'nim' => 'AUTO-' . Str::upper(Str::random(8)),
            ]);
        }

        $edisi = EdisiRumahJurnal::firstOrCreate(
            [
                'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            ['kuota' => 2, 'label' => null]
        );

        PemakaianRumahJurnal::create([
            'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
            'edisi_id' => $edisi->id,
            'mahasiswa_id' => $mhs->id,
            'dosen_pembimbing_id' => $request->input('dosen_pembimbing_id') ?: null,
            'judul_jurnal' => $request->input('judul_jurnal'), // <-- simpan judul
            'status' => 'disetujui',
        ]);

        return redirect()->back()->with('success', 'Data pemakaian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $row = PemakaianRumahJurnal::findOrFail($id);

        $validated = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:150'],
            'dosen_pembimbing_id' => ['nullable', 'integer', 'exists:dosen_pembimbing,id'],
            'rumah_jurnal_id' => ['required', 'integer', 'exists:rumah_jurnal,id'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'status' => ['required', Rule::in(['pending', 'disetujui', 'dibatalkan'])],
            'judul_jurnal' => ['nullable', 'string', 'max:300'], // <-- validasi judul
        ]);

        $mhs = $row->mahasiswa;
        if ($mhs && $mhs->nama !== $validated['nama_mahasiswa']) {
            $mhs->update(['nama' => $validated['nama_mahasiswa']]);
        } elseif (!$mhs) {
            $mhs = Mahasiswa::create([
                'nama' => $validated['nama_mahasiswa'],
                'nim' => 'AUTO-' . Str::upper(Str::random(8)),
            ]);
            $row->mahasiswa_id = $mhs->id;
        }

        $edisi = EdisiRumahJurnal::firstOrCreate(
            [
                'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ]
        );

        $row->update([
            'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
            'edisi_id' => $edisi->id,
            'dosen_pembimbing_id' => $request->input('dosen_pembimbing_id') ?: null,
            'judul_jurnal' => $request->input('judul_jurnal'), // <-- update judul
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Data pemakaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = PemakaianRumahJurnal::findOrFail($id);
        $row->delete();

        return redirect()->back()->with('success', 'Data pemakaian berhasil dihapus.');
    }
   public function exportPdf(Request $request)
    {
        $q = $request->q;

        $pemakaian = PemakaianRumahJurnal::with(['mahasiswa','dosenPembimbing','rumahJurnal','edisi'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->whereHas('mahasiswa', fn($m) => $m->where('nama','like',"%{$q}%"))
                      ->orWhereHas('dosenPembimbing', fn($d) => $d->where('nama','like',"%{$q}%"))
                      ->orWhereHas('rumahJurnal', fn($r) => $r->where('nama','like',"%{$q}%")->orWhere('link','like',"%{$q}%"))
                      ->orWhereHas('edisi', fn($e) => $e->where('tahun','like',"%{$q}%")->orWhere('bulan','like',"%{$q}%"));
                });
            })
            ->orderByDesc('id')
            ->get();

        // Peta kode -> label bulan
        $bulanMap = [
            'jan' => 'Januari', 'feb' => 'Februari', 'mar' => 'Maret', 'apr' => 'April',
            'mei' => 'Mei', 'jun' => 'Juni', 'jul' => 'Juli', 'agu' => 'Agustus',
            'sep' => 'September', 'okt' => 'Oktober', 'nov' => 'November', 'des' => 'Desember',
        ];

        // Siapkan label edisi untuk tiap baris (mis. "Maret & September")
        foreach ($pemakaian as $row) {
            $arr = is_array($row->rumahJurnal?->edisi) ? $row->rumahJurnal->edisi : [];
            // Urutkan sesuai urutan bulan di atas
            $arr = array_values(array_intersect(array_keys($bulanMap), $arr));
            $labels = array_map(fn($k) => $bulanMap[$k], $arr);

            if (count($labels) > 1) {
                $last = array_pop($labels);
                $row->edisi_label = implode(', ', $labels).' & '.$last;
            } else {
                $row->edisi_label = $labels[0] ?? '-';
            }
        }

        $pdf = Pdf::loadView('admin.jurnalcek_pdf', [
                'pemakaian' => $pemakaian,
                'generated' => now()->format('d M Y, H:i'),
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('cek-jurnal-'.now()->format('Ymd_His').'.pdf');
    }

}
