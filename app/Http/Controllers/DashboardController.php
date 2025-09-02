<?php

namespace App\Http\Controllers;

use App\Models\RumahJurnal;
use App\Models\EdisiRumahJurnal;
use App\Models\PemakaianRumahJurnal;
use App\Models\DosenPembimbing;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJurnal     = RumahJurnal::count();
        $totalDosen      = DosenPembimbing::count();
        $totalMahasiswa  = Mahasiswa::count();
        $totalPemakaian  = PemakaianRumahJurnal::whereIn('status', ['pending','disetujui'])->count();

        // 1) Pemakaian per edisi (last 12 edisi)
        $edisi = EdisiRumahJurnal::withCount([
            'pemakaian as terpakai' => fn($q) => $q->whereIn('status', ['pending','disetujui'])
        ])->orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get()->reverse()->values();

        $chart1_labels = $edisi->map(fn($e) => sprintf('%02d-%d', $e->bulan, $e->tahun));
        $chart1_terpakai = $edisi->pluck('terpakai');

        // 2) Sisa vs Terpakai per Jurnal (top 10 by terpakai)
        $edisiAgg = EdisiRumahJurnal::select('rumah_jurnal_id',
            DB::raw('SUM(kuota) as total_kuota')
        )->groupBy('rumah_jurnal_id');

        $pakaiAgg = PemakaianRumahJurnal::select('rumah_jurnal_id',
            DB::raw('COUNT(*) as total_terpakai')
        )->whereIn('status', ['pending','disetujui'])
         ->groupBy('rumah_jurnal_id');

        $perJurnal = RumahJurnal::leftJoinSub($edisiAgg, 'e', 'e.rumah_jurnal_id', '=', 'rumah_jurnal.id')
            ->leftJoinSub($pakaiAgg, 'p', 'p.rumah_jurnal_id', '=', 'rumah_jurnal.id')
            ->select('rumah_jurnal.id','rumah_jurnal.nama',
                DB::raw('COALESCE(e.total_kuota,0) as total_kuota'),
                DB::raw('COALESCE(p.total_terpakai,0) as total_terpakai')
            )
            ->orderByDesc(DB::raw('COALESCE(p.total_terpakai,0)'))
            ->limit(10)->get();

        $chart2_labels   = $perJurnal->pluck('nama');
        $chart2_terpakai = $perJurnal->pluck('total_terpakai');
        $chart2_sisa     = $perJurnal->map(fn($r) => max(0, (int)$r->total_kuota - (int)$r->total_terpakai));

        // 3) Distribusi Dosen (top 10 dosen by pemakaian aktif)
        $dosenDist = DosenPembimbing::join('pemakaian_rumah_jurnal as prj','prj.dosen_pembimbing_id','=','dosen_pembimbing.id')
            ->whereIn('prj.status',['pending','disetujui'])
            ->groupBy('dosen_pembimbing.id','dosen_pembimbing.nama')
            ->select('dosen_pembimbing.nama', DB::raw('COUNT(*) as total'))
            ->orderByDesc('total')->limit(10)->get();

        $chart3_labels = $dosenDist->pluck('nama');
        $chart3_data   = $dosenDist->pluck('total');

        // 4) Tren Mahasiswa Baru (12 bulan terakhir)
        $bulan12 = Mahasiswa::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw("COUNT(*) as total")
            )->where('created_at','>=', now()->subMonths(12))
             ->groupBy('ym')->orderBy('ym')->get();

        // Normalisasi label 12 bulan terakhir
        $period = collect(range(11,0))->map(fn($i)=> now()->subMonths($i)->format('Y-m'));
        $chart4_labels = $period->map(fn($ym)=> date('M Y', strtotime($ym.'-01')));
        $byYm = $bulan12->keyBy('ym');
        $chart4_data   = $period->map(fn($ym)=> (int)($byYm[$ym]->total ?? 0));

        // 5) Status Pemakaian
        $statusAgg = PemakaianRumahJurnal::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total','status')->toArray();
        $chart5_labels = ['pending','disetujui','dibatalkan'];
        $chart5_data   = array_map(fn($k)=> (int)($statusAgg[$k] ?? 0), $chart5_labels);

        // 6) Top 5 Rumah Jurnal Terfavorit
        $topJurnal = RumahJurnal::join('pemakaian_rumah_jurnal as prj','prj.rumah_jurnal_id','=','rumah_jurnal.id')
            ->whereIn('prj.status',['pending','disetujui'])
            ->groupBy('rumah_jurnal.id','rumah_jurnal.nama')
            ->select('rumah_jurnal.nama', DB::raw('COUNT(*) as total'))
            ->orderByDesc('total')->limit(5)->get();

        $chart6_labels = $topJurnal->pluck('nama');
        $chart6_data   = $topJurnal->pluck('total');

        return view('admin.index', [
            'totalJurnal'    => $totalJurnal,
            'totalDosen'     => $totalDosen,
            'totalMahasiswa' => $totalMahasiswa,
            'totalPemakaian' => $totalPemakaian,

            'chart1_labels' => $chart1_labels,
            'chart1_terpakai' => $chart1_terpakai,

            'chart2_labels' => $chart2_labels,
            'chart2_terpakai' => $chart2_terpakai,
            'chart2_sisa' => $chart2_sisa,

            'chart3_labels' => $chart3_labels,
            'chart3_data' => $chart3_data,

            'chart4_labels' => $chart4_labels,
            'chart4_data' => $chart4_data,

            'chart5_labels' => $chart5_labels,
            'chart5_data' => $chart5_data,

            'chart6_labels' => $chart6_labels,
            'chart6_data' => $chart6_data,
        ]);
    }
}
