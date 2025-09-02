<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RumahJurnal;
use App\Models\EdisiRumahJurnal;
use App\Models\PemakaianRumahJurnal;
use App\Models\DosenPembimbing;

class PublicController extends Controller
{
    public function index()
    {
        $dosenList = DosenPembimbing::orderBy('nama')->get(['id','nama']);
        return view('login', [
            'dosenList' => $dosenList,
            'result'    => null,
            'input'     => null,
        ]);
    }

    public function cek(Request $request)
    {
        $validated = $request->validate([
            'link'                 => ['required','string','max:255','url'],
            'bulan'                => ['required','integer','between:1,12'],
            'tahun'                => ['required','integer','between:2000,2100'],
            'dosen_pembimbing_id'  => ['required','integer','exists:dosen_pembimbing,id'],
        ], [
            'link.required' => 'Link rumah jurnal wajib diisi.',
        ]);

        $dosen = DosenPembimbing::find($validated['dosen_pembimbing_id']);

        // Cari rumah jurnal by link
        $journal = RumahJurnal::where('link', $validated['link'])->first();

        if (!$journal) {
            $result = [
                'status' => 'not_found',
                'message' => 'Link rumah jurnal tidak terdaftar di sistem admin.',
            ];
            return $this->renderResult($request, $result, $validated);
        }

        // Ambil rules global (default: max=2, unik_dosen=1)
        $rules = DB::table('pengaturan_jurnal')->first();
        $maxDefault = (int)($rules->max_mahasiswa_per_edisi ?? 2);
        $unikDosen  = (int)($rules->unik_dosen_per_edisi ?? 1) === 1;

        // Cari edisi; kalau belum ada, JANGAN bikin—anggap terpakai=0 dan kapasitas=rules default
        $edisi = EdisiRumahJurnal::where('rumah_jurnal_id', $journal->id)
            ->where('tahun', $validated['tahun'])
            ->where('bulan', $validated['bulan'])
            ->first();

        if ($edisi) {
            $kapasitas = (int)$edisi->kuota;
            $terpakai  = PemakaianRumahJurnal::where('edisi_id', $edisi->id)
                ->whereIn('status', ['pending','disetujui'])
                ->count();

            $dosenSudahDipakai = $unikDosen
                ? PemakaianRumahJurnal::where('edisi_id', $edisi->id)
                    ->where('dosen_pembimbing_id', $validated['dosen_pembimbing_id'])
                    ->whereIn('status', ['pending','disetujui'])
                    ->exists()
                : false;
        } else {
            $kapasitas = $maxDefault;
            $terpakai = 0;
            $dosenSudahDipakai = false; // belum ada edisi, berarti belum ada pemakaian
        }

        $sisa = max(0, $kapasitas - $terpakai);

        if ($dosenSudahDipakai) {
            $result = [
                'status' => 'not_available',
                'message' => 'Tidak tersedia karena dosen pembimbing yang sama sudah dipakai pada edisi tersebut.',
                'detail'  => [
                    'jurnal' => $journal->nama,
                    'link'   => $journal->link,
                    'bulan'  => $validated['bulan'],
                    'tahun'  => $validated['tahun'],
                    'dosen'  => $dosen->nama,
                    'kapasitas' => $kapasitas,
                    'terpakai'  => $terpakai,
                    'sisa'      => $sisa,
                ]
            ];
        } elseif ($sisa > 0) {
            $result = [
                'status' => 'available',
                'message' => "Tersedia. Sisa {$sisa} slot.",
                'detail'  => [
                    'jurnal' => $journal->nama,
                    'link'   => $journal->link,
                    'bulan'  => $validated['bulan'],
                    'tahun'  => $validated['tahun'],
                    'dosen'  => $dosen->nama,
                    'kapasitas' => $kapasitas,
                    'terpakai'  => $terpakai,
                    'sisa'      => $sisa,
                ],
            ];
        } else {
            $result = [
                'status' => 'full',
                'message' => 'Tidak tersedia. Slot edisi ini sudah penuh.',
                'detail'  => [
                    'jurnal' => $journal->nama,
                    'link'   => $journal->link,
                    'bulan'  => $validated['bulan'],
                    'tahun'  => $validated['tahun'],
                    'dosen'  => $dosen->nama,
                    'kapasitas' => $kapasitas,
                    'terpakai'  => $terpakai,
                    'sisa'      => $sisa,
                ],
            ];
        }

        return $this->renderResult($request, $result, $validated);
    }

    private function renderResult(Request $request, array $result, array $input)
    {
        $dosenList = DosenPembimbing::orderBy('nama')->get(['id','nama']);
        return view('login', [
            'dosenList' => $dosenList,
            'result'    => $result,
            'input'     => $input,
        ]);
    }
}
