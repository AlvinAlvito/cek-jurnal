@extends('layouts.main')
@section('content')
<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>
        <div class="search-box">
            <i class="uil uil-search"></i>
            <input type="text" placeholder="Cari di dashboard...">
        </div>
        <img src="/images/profil.png" alt="">
    </div>

    <div class="dash-content">
        <div class="overview">
            <div class="title">
                <i class="bi bi-speedometer2"></i>
                <span class="text">Dashboard Rumah Jurnal</span>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="boxes">
            <div class="box box1 d-flex align-items-center gap-3">
                <i class="bi bi-journal-richtext" style="font-size: 1.75rem;"></i>
                <div>
                    <span class="text d-block">Total Rumah Jurnal</span>
                    <span class="number">{{ $totalJurnal }}</span>
                </div>
            </div>
            <div class="box box2 d-flex align-items-center gap-3">
                <i class="bi bi-person-badge" style="font-size: 1.75rem;"></i>
                <div>
                    <span class="text d-block">Total Dosen</span>
                    <span class="number">{{ $totalDosen }}</span>
                </div>
            </div>
            <div class="box box3 d-flex align-items-center gap-3">
                <i class="bi bi-mortarboard" style="font-size: 1.75rem;"></i>
                <div>
                    <span class="text d-block">Total Mahasiswa</span>
                    <span class="number">{{ $totalMahasiswa }}</span>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="activity">
            <div class="title mb-3">
                <i class="bi bi-graph-up"></i>
                <span class="text">Analitik</span>
            </div>

            <div class="row g-4">
                {{-- Chart 1 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-calendar2-week"></i>
                            <h6 class="mb-0">Pemakaian per Edisi (12 Terakhir)</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartPemakaianEdisi"></div>
                        </div>
                    </div>
                </div>

                {{-- Chart 2 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-diagram-3"></i>
                            <h6 class="mb-0">Sisa vs Terpakai per Jurnal (Top 10)</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartSisaTerpakai"></div>
                        </div>
                    </div>
                </div>

                {{-- Chart 3 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-people"></i>
                            <h6 class="mb-0">Distribusi Dosen Pembimbing (Top 10)</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartDistribusiDosen"></div>
                        </div>
                    </div>
                </div>

                {{-- Chart 4 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h6 class="mb-0">Tren Mahasiswa Baru (12 Bulan)</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartTrenMahasiswa"></div>
                        </div>
                    </div>
                </div>

                {{-- Chart 5 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart"></i>
                            <h6 class="mb-0">Status Pemakaian Jurnal</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartStatusPemakaian"></div>
                        </div>
                    </div>
                </div>

                {{-- Chart 6 --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-trophy"></i>
                            <h6 class="mb-0">Top 5 Rumah Jurnal Terfavorit</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartTopJurnal"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- /.dash-content --}}
</section>

{{-- Bootstrap Icons CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Data dari Controller
    const c1_labels = @json($chart1_labels);
    const c1_terpakai = @json($chart1_terpakai);

    const c2_labels = @json($chart2_labels);
    const c2_terpakai = @json($chart2_terpakai);
    const c2_sisa = @json($chart2_sisa);

    const c3_labels = @json($chart3_labels);
    const c3_data = @json($chart3_data);

    const c4_labels = @json($chart4_labels);
    const c4_data = @json($chart4_data);

    const c5_labels = @json($chart5_labels);
    const c5_data = @json($chart5_data);

    const c6_labels = @json($chart6_labels);
    const c6_data = @json($chart6_data);

    // Chart 1: Pemakaian per Edisi
    new ApexCharts(document.querySelector("#chartPemakaianEdisi"), {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{ name: 'Terpakai', data: c1_terpakai }],
        xaxis: { categories: c1_labels },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (val)=> `${val} mhs` } }
    }).render();

    // Chart 2: Sisa vs Terpakai per Jurnal
    new ApexCharts(document.querySelector("#chartSisaTerpakai"), {
        chart: { type: 'bar', stacked: true, height: 320, toolbar: { show: false } },
        series: [
            { name: 'Terpakai', data: c2_terpakai },
            { name: 'Sisa Slot', data: c2_sisa }
        ],
        xaxis: { categories: c2_labels },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (val)=> `${val} slot` } },
        legend: { position: 'top' }
    }).render();

    // Chart 3: Distribusi Dosen
    new ApexCharts(document.querySelector("#chartDistribusiDosen"), {
        chart: { type: 'donut', height: 320 },
        labels: c3_labels,
        series: c3_data,
        legend: { position: 'bottom' },
        tooltip: { y: { formatter: (val)=> `${val} mhs` } }
    }).render();

    // Chart 4: Tren Mahasiswa Baru
    new ApexCharts(document.querySelector("#chartTrenMahasiswa"), {
        chart: { type: 'area', height: 320, toolbar: { show: false } },
        series: [{ name: 'Mahasiswa Baru', data: c4_data }],
        xaxis: { categories: c4_labels },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        tooltip: { y: { formatter: (val)=> `${val} orang` } }
    }).render();

    // Chart 5: Status Pemakaian
    new ApexCharts(document.querySelector("#chartStatusPemakaian"), {
        chart: { type: 'radialBar', height: 320, toolbar: { show: false } },
        labels: c5_labels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        series: (() => {
            const total = c5_data.reduce((a,b)=>a+b,0) || 1;
            return c5_data.map(v=> Math.round(v/total*100));
        })(),
        plotOptions: {
            radialBar: {
                dataLabels: {
                    total: { show: true, label: 'Total', formatter: function() { return '100%'; } }
                }
            }
        },
        tooltip: { y: { formatter: (val)=> `${val}%` } }
    }).render();

    // Chart 6: Top 5 Jurnal Terfavorit
    new ApexCharts(document.querySelector("#chartTopJurnal"), {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{ name: 'Terpakai', data: c6_data }],
        xaxis: { categories: c6_labels },
        plotOptions: { bar: { horizontal: true } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (val)=> `${val} mhs` } }
    }).render();
</script>
@endsection
