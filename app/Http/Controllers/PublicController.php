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
        ]);

        // Normalisasi sederhana (tanpa ribet): hilangkan trailing slash
        $link = rtrim($validated['link'], '/');
        $dosen = DosenPembimbing::find($validated['dosen_pembimbing_id']);

        // Cari journal dgn link EXACT (sesuai permintaan: tidak dibuat ribet)
        $journal = RumahJurnal::whereRaw('LOWER(link) = ?', [strtolower($link)])->first();

        // Jika link tidak ada di DB → tampilkan modal "tidak terdaftar"
        if (!$journal) {
            $result = [
                'status'  => 'not_found',
                'title'   => 'Link Rumah Jurnal Tidak Terdaftar',
                'message' => 'Periksa kembali link yang Anda masukkan. Link harus berupa halaman HOME dari rumah jurnal tersebut. Contoh: https://ejurnal.stmik-budidarma.ac.id/index.php/jurikom/index. Jika masih bermasalah, silakan hubungi koordinator jurnal.',
                'detail'  => [
                    'link_input' => $link,
                ],
            ];
            return $this->renderResult($request, $result, $validated);
        }

        // Ambil rules global (default: max=2, unik_dosen=1)
        $rules = DB::table('pengaturan_jurnal')->first();
        $maxDefault = (int)($rules->max_mahasiswa_per_edisi ?? 2);
        $unikDosen  = (int)($rules->unik_dosen_per_edisi ?? 1) === 1;

        // Cek edisi
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
            // Jika edisi belum ada → asumsikan kapasitas default, terpakai = 0
            $kapasitas = $maxDefault;
            $terpakai  = 0;
            $dosenSudahDipakai = false;
        }

        $sisa = max(0, $kapasitas - $terpakai);

        if ($dosenSudahDipakai) {
            $result = [
                'status'  => 'not_available',
                'title'   => 'Tidak Tersedia (Dosen Sudah Terpakai)',
                'message' => 'Dosen pembimbing yang sama sudah terpakai pada edisi ini.',
                'detail'  => [
                    'jurnal'     => $journal->nama,
                    'link'       => $journal->link,
                    'bulan'      => $validated['bulan'],
                    'tahun'      => $validated['tahun'],
                    'dosen'      => $dosen->nama,
                    'kapasitas'  => $kapasitas,
                    'terpakai'   => $terpakai,
                    'sisa'       => $sisa,
                ],
            ];
        } elseif ($sisa > 0) {
            $result = [
                'status'  => 'available',
                'title'   => 'Tersedia',
                'message' => "Jurnal tersedia. Tersisa {$sisa} slot.",
                'detail'  => [
                    'jurnal'     => $journal->nama,
                    'link'       => $journal->link,
                    'bulan'      => $validated['bulan'],
                    'tahun'      => $validated['tahun'],
                    'dosen'      => $dosen->nama,
                    'kapasitas'  => $kapasitas,
                    'terpakai'   => $terpakai,
                    'sisa'       => $sisa,
                ],
            ];
        } else {
            $result = [
                'status'  => 'full',
                'title'   => 'Slot Penuh',
                'message' => 'Jurnal tidak tersedia karena slot edisi ini sudah penuh.',
                'detail'  => [
                    'jurnal'     => $journal->nama,
                    'link'       => $journal->link,
                    'bulan'      => $validated['bulan'],
                    'tahun'      => $validated['tahun'],
                    'dosen'      => $dosen->nama,
                    'kapasitas'  => $kapasitas,
                    'terpakai'   => $terpakai,
                    'sisa'       => $sisa,
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
            'result'    => $result,  // <— dipakai untuk modal
            'input'     => $input,
        ]);
    }
}
