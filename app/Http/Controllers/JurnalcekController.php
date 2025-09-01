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

class JurnalcekController extends Controller
{
    public function index()
    {
        $q = request('q');
        $perPage = (int) (request('per_page') ?? 10);

        $query = PemakaianRumahJurnal::query()
            ->with(['mahasiswa','dosenPembimbing','rumahJurnal','edisi'])
            ->orderByDesc('id');

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->whereHas('mahasiswa', fn($m) => $m->where('nama','like',"%{$q}%"))
                  ->orWhereHas('dosenPembimbing', fn($d) => $d->where('nama','like',"%{$q}%"))
                  ->orWhereHas('rumahJurnal', fn($r) => $r->where('nama','like',"%{$q}%")->orWhere('link','like',"%{$q}%"))
                  ->orWhereHas('edisi', fn($e) => $e->where('tahun','like',"%{$q}%")->orWhere('bulan','like',"%{$q}%"));
            });
        }

        $data = $query->paginate($perPage)->appends(request()->query());
        $dosenList  = DosenPembimbing::orderBy('nama')->get(['id','nama']);
        $jurnalList = RumahJurnal::orderBy('nama')->get(['id','nama','link']);

        return view('admin.data-jurnalcek', [
            'title'      => 'Data Pemakaian Rumah Jurnal',
            'pemakaian'  => $data,
            'dosenList'  => $dosenList,
            'jurnalList' => $jurnalList,
            'q'          => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mahasiswa'      => ['required','string','max:150'],
            'dosen_pembimbing_id' => ['required','integer','exists:dosen_pembimbing,id'],
            'rumah_jurnal_id'     => ['required','integer','exists:rumah_jurnal,id'],
            'tahun'               => ['required','integer','between:2000,2100'],
            'bulan'               => ['required','integer','between:1,12'],
        ]);

        $mhs = Mahasiswa::where('nama', $validated['nama_mahasiswa'])->first();
        if (!$mhs) {
            $mhs = Mahasiswa::create([
                'nama' => $validated['nama_mahasiswa'],
                'nim'  => 'AUTO-'.Str::upper(Str::random(8)),
            ]);
        }

        $edisi = EdisiRumahJurnal::firstOrCreate(
            [
                'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
                'tahun'           => $validated['tahun'],
                'bulan'           => $validated['bulan'],
            ],
            ['kuota' => 2, 'label' => null]
        );

        PemakaianRumahJurnal::create([
            'rumah_jurnal_id'      => $validated['rumah_jurnal_id'],
            'edisi_id'             => $edisi->id,
            'mahasiswa_id'         => $mhs->id,
            'dosen_pembimbing_id'  => $validated['dosen_pembimbing_id'],
            'status'               => 'disetujui',
        ]);

        return redirect()->back()->with('success', 'Data pemakaian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $row = PemakaianRumahJurnal::findOrFail($id);

        $validated = $request->validate([
            'nama_mahasiswa'      => ['required','string','max:150'],
            'dosen_pembimbing_id' => ['required','integer','exists:dosen_pembimbing,id'],
            'rumah_jurnal_id'     => ['required','integer','exists:rumah_jurnal,id'],
            'tahun'               => ['required','integer','between:2000,2100'],
            'bulan'               => ['required','integer','between:1,12'],
            'status'              => ['required', Rule::in(['pending','disetujui','dibatalkan'])],
        ]);

        $mhs = $row->mahasiswa;
        if ($mhs->nama !== $validated['nama_mahasiswa']) {
            $mhs->update(['nama' => $validated['nama_mahasiswa']]);
        }

        $edisi = EdisiRumahJurnal::firstOrCreate(
            [
                'rumah_jurnal_id' => $validated['rumah_jurnal_id'],
                'tahun'           => $validated['tahun'],
                'bulan'           => $validated['bulan'],
            ]
        );

        $row->update([
            'rumah_jurnal_id'      => $validated['rumah_jurnal_id'],
            'edisi_id'             => $edisi->id,
            'dosen_pembimbing_id'  => $validated['dosen_pembimbing_id'],
            'status'               => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Data pemakaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = PemakaianRumahJurnal::findOrFail($id);
        $row->delete();

        return redirect()->back()->with('success', 'Data pemakaian berhasil dihapus.');
    }
}
