<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EdisiRumahJurnal;

class KontrolJurnalController extends Controller
{
    public function index()
    {
        $perPage = (int) (request('per_page') ?? 100);
        $q = request('q');

        $query = EdisiRumahJurnal::query()
            ->with('rumahJurnal')
            ->withCount([
                'pemakaian as terpakai' => function ($x) {
                    $x->whereIn('status', ['pending','disetujui']);
                }
            ])
            ->orderByDesc('id');

        if (!empty($q)) {
            $query->where(function($w) use ($q) {
                $w->where('tahun','like',"%{$q}%")
                  ->orWhere('bulan','like',"%{$q}%")
                  ->orWhere('label','like',"%{$q}%")
                  ->orWhereHas('rumahJurnal', function($r) use ($q){
                      $r->where('nama','like',"%{$q}%")->orWhere('link','like',"%{$q}%");
                  });
            });
        }

        $edisi = $query->paginate($perPage)->appends(request()->query());

        $rules = DB::table('pengaturan_jurnal')->first();
        if (!$rules) {
            $rules = (object)[
                'id' => null,
                'max_mahasiswa_per_edisi' => 2,
                'unik_dosen_per_edisi' => 1,
            ];
        }

        return view('admin.kontrol-jurnal', [
            'title' => 'Kontrol Ketersediaan Rumah Jurnal',
            'edisi' => $edisi,
            'rules' => $rules,
            'q'     => $q,
        ]);
    }

    public function updateRules(Request $request)
    {
        $validated = $request->validate([
            'max_mahasiswa_per_edisi' => ['required','integer','min:1','max:255'],
            'unik_dosen_per_edisi'    => ['nullable','in:0,1'],
            'apply_all'               => ['nullable','in:0,1'],
        ]);

        $unik = (int)($validated['unik_dosen_per_edisi'] ?? 0);
        $max  = (int)$validated['max_mahasiswa_per_edisi'];
        $applyAll = (int)($validated['apply_all'] ?? 1);

        DB::transaction(function() use ($unik, $max, $applyAll) {
            $exists = DB::table('pengaturan_jurnal')->exists();
            if ($exists) {
                DB::table('pengaturan_jurnal')->update([
                    'max_mahasiswa_per_edisi' => $max,
                    'unik_dosen_per_edisi'    => $unik,
                ]);
            } else {
                DB::table('pengaturan_jurnal')->insert([
                    'max_mahasiswa_per_edisi' => $max,
                    'unik_dosen_per_edisi'    => $unik,
                ]);
            }

            if ($applyAll) {
                EdisiRumahJurnal::query()->update(['kuota' => $max]);
            }
        });

        return redirect()->route('kontroljurnal.index')->with('success', 'Rules berhasil disimpan.');
    }
}
