@extends('layouts.main')
@section('content')
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            <img src="/images/profil.png" alt="">
        </div>

        <div class="dash-content">
            <div class="overview">
                <div class="title">
                    <i class="uil uil-chart-line"></i>
                    <span class="text">Dashboard Penjualan</span>
                </div>
            </div>
            <div class="boxes">
                <div class="box box1">
                    <i class="uil uil-thumbs-up"></i>
                    <span class="text">Total Produk</span>
                    <span class="number">123</span>
                </div>
                <div class="box box2">
                    <i class="uil uil-comments"></i>
                    <span class="text">Total Pegawai</span>
                    <span class="number">6</span>
                </div>
                <div class="box box3">
                    <i class="uil uil-share"></i>
                    <span class="text">Total Penjualan</span>
                    <span class="number">1606</span>
                </div>
            </div>

            {{-- Grafik Chart Apex --}}
            <div class="activity">
                <div class="title mb-3">
                    <i class="uil uil-chart-bar"></i>
                    <span class="text">Analisis dan Prediksi</span>
                </div>

                <div class="row">
                    {{-- Chart 1: Total Penjualan per Produk --}}
                    <div class="col-md-6 mb-4">
                        <div id="chartTotalProduk"></div>
                    </div>


                    {{-- Chart 3: MAPE tiap produk --}}
                    <div class="col-md-6 mb-4">
                        <div id="chartMape"></div>
                    </div>

                    {{-- Chart 4: Total penjualan per bulan --}}
                    <div class="col-md-6 mb-4">
                        <div id="chartTotalBulan"></div>
                    </div>

                    {{-- Chart 5: Top 5 Produk Terlaris --}}
                    <div class="col-md-6 mb-4">
                        <div id="chartTopProduk"></div>
                    </div>

                    {{-- Chart 6: Data Aktual vs Prediksi Satu Produk (ambil pertama saja) --}}
                    <div class="col-md-6 mb-4">
                        <div id="chartBandingAktual"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- <script>
        // Chart 1: Total Penjualan per Produk
        new ApexCharts(document.querySelector("#chartTotalProduk"), {
            chart: {
                type: 'bar'
            },
            series: [{
                name: 'Total Jan–Jun',
                data: {!! json_encode($totalPerProduk->pluck('total')) !!}
            }],
            xaxis: {
                categories: {!! json_encode($totalPerProduk->pluck('name')) !!}
            },
            title: {
                text: 'Total Penjualan per Produk'
            }
        }).render();

        // Chart 3: MAPE per Produk
        new ApexCharts(document.querySelector("#chartMape"), {
            chart: {
                type: 'bar'
            },
            series: [{
                name: 'MAPE (%)',
                data: {!! json_encode($mapePerProduk->pluck('mape')) !!}
            }],
            xaxis: {
                categories: {!! json_encode($mapePerProduk->pluck('name')) !!}
            },
            title: {
                text: 'Tingkat Akurasi (MAPE)'
            }
        }).render();

        // Chart 4: Total penjualan gabungan per bulan
        new ApexCharts(document.querySelector("#chartTotalBulan"), {
            chart: {
                type: 'line'
            },
            series: [{
                name: 'Total Penjualan',
                data: {!! json_encode(array_values($totalPerBulan)) !!}
            }],
            xaxis: {
                categories: {!! json_encode(array_keys($totalPerBulan)) !!}
            },
            title: {
                text: 'Tren Penjualan (Jan–Jun)'
            }
        }).render();

        // Chart 5: Top 5 Produk
        new ApexCharts(document.querySelector("#chartTopProduk"), {
            chart: {
                type: 'donut'
            },
            series: {!! json_encode($topProduk->pluck('total')) !!},
            labels: {!! json_encode($topProduk->pluck('name')) !!},
            title: {
                text: 'Top 5 Produk Terlaris'
            }
        }).render();

        // Chart 6: Banding Aktual & Prediksi (produk pertama)
        const dataAktual = {!! json_encode(array_values($totalPerBulan)) !!};
        const produkPertama = {!! json_encode($prediksiPerProduk->first()) !!};
        new ApexCharts(document.querySelector("#chartBandingAktual"), {
            chart: {
                type: 'line'
            },
            series: [{
                    name: 'Aktual Jan–Jun',
                    data: dataAktual
                },
                {
                    name: 'Prediksi Jul–Des',
                    data: produkPertama.data
                }
            ],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            },
            title: {
                text: `Perbandingan: ${produkPertama.name}`
            }
        }).render();
    </script> --}}
@endsection
