<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cek Jurnal</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <link href="css/templatemo-topic-listing.css" rel="stylesheet">

</head>

<body id="top">

    <main>

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <i class="bi-back"></i>
                    <span>Cek Jurnal</span>
                </a>

                <div class="d-lg-none ms-auto me-4">
                    <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Modal -->
                <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="/" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Login Admin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if ($errors->has('login'))
                                    <div class="alert alert-danger">{{ $errors->first('login') }}</div>
                                @endif
                                <div class="mb-3">
                                    <label>Username:</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Password:</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-5 me-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_1">Beranda</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_2">Tentang Sistem</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_3">Alur Sistem</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_4">Kontrol Jurnal</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_5">Kontak</a>
                        </li>
                    </ul>

                    <div class="d-none d-lg-block">
                        <a href="#top" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="navbar-icon bi-person smoothscroll" title="Login Admin"></a>
                    </div>
                </div>

            </div>
        </nav>


        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-12 mx-auto text-center">
                        <h1 class="text-white">Sistem Cek Ketersediaan Rumah Jurnal</h1>
                        <h6 class="text-white mt-3">Masukkan link, edisi, tahun, dan dosen pembimbing untuk cek
                            ketersediaan</h6>

                        <div class="card mt-4 text-start">
                            <div class="card-body">
                                <form action="{{ route('public.cek') }}" method="POST" class="row g-3">
                                    @csrf

                                    <div class="col-12">
                                        <label class="form-label">Link Rumah Jurnal</label>
                                        <input type="url" name="link"
                                            value="{{ old('link', $input['link'] ?? '') }}" class="form-control"
                                            placeholder="https://contoh.com/rumah-jurnal" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Bulan (Edisi)</label>
                                        <select name="bulan" class="form-select" required>
                                            @php $bulanMap = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
                                            @foreach ($bulanMap as $k => $v)
                                                <option value="{{ $k }}"
                                                    {{ (int) old('bulan', $input['bulan'] ?? 0) === $k ? 'selected' : '' }}>
                                                    {{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Tahun</label>
                                        <input type="number" name="tahun"
                                            value="{{ old('tahun', $input['tahun'] ?? '') }}" class="form-control"
                                            placeholder="mis. 2025" min="2000" max="2100" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Nama Dosen Pembimbing</label>
                                        <select name="dosen_pembimbing_id" class="form-select" required>
                                            <option value="">-- Pilih Dosen --</option>
                                            @foreach ($dosenList as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ (int) old('dosen_pembimbing_id', $input['dosen_pembimbing_id'] ?? 0) === $d->id ? 'selected' : '' }}>
                                                    {{ $d->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($errors->any())
                                        <div class="col-12">
                                            <div class="alert alert-danger">
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($errors->all() as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12 text-end">
                                        <button class="btn btn-primary">Cek Ketersediaan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @if ($result)
                            <div class="mt-4">
                                @php
                                    $b = $bulanMap[$result['detail']['bulan'] ?? ($input['bulan'] ?? 0)] ?? '-';
                                    $t = $result['detail']['tahun'] ?? ($input['tahun'] ?? '-');
                                    $status = $result['status'] ?? 'unknown';
                                    $kelas = match ($status) {
                                        'available' => 'alert-success',
                                        'full' => 'alert-danger',
                                        'not_found', 'not_available' => 'alert-warning',
                                        default => 'alert-info',
                                    };
                                @endphp
                                <div class="alert {{ $kelas }} text-start">
                                    @if (isset($result['detail']))
                                        <strong>{{ $result['detail']['jurnal'] ?? 'Jurnal' }}</strong>
                                        ({{ $b }} {{ $t }})<br>
                                        Dosen pembimbing: <strong>{{ $result['detail']['dosen'] ?? '-' }}</strong><br>
                                        Link:
                                        @if (!empty($result['detail']['link'] ?? ''))
                                            <a href="{{ $result['detail']['link'] }}" target="_blank"
                                                rel="noopener noreferrer">
                                                {{ $result['detail']['link'] }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                        <hr class="my-2">
                                    @endif
                                    {{ $result['message'] ?? '' }}
                                    @if (isset($result['detail']['kapasitas']))
                                        <div class="mt-1">
                                            Kapasitas: {{ $result['detail']['kapasitas'] ?? '-' }},
                                            Terpakai: {{ $result['detail']['terpakai'] ?? '-' }},
                                            Sisa: {{ $result['detail']['sisa'] ?? '-' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>

        <section class="featured-section">
            <div class="container">
                <div class="row justify-content-center">

                    <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mb-2">Cek Ketersediaan Jurnal</h5>
                                    <p class="mb-0">
                                        Masukkan <em>link</em> rumah jurnal, pilih edisi (bulan & tahun), serta nama
                                        dosen pembimbing untuk melihat apakah jurnal tersebut masih tersedia dan berapa
                                        sisa slotnya.
                                    </p>
                                </div>
                                <span class="badge bg-success rounded-pill ms-auto">✓</span>
                            </div>

                            <img src="images/topics/undraw_Educator_re_ju47.png" class="custom-block-image img-fluid"
                                alt="Cek Ketersediaan Rumah Jurnal">
                        </div>
                    </div>

                    <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mb-2">Aturan Fleksibel</h5>
                                    <p class="mb-0">
                                        Admin dapat mengatur maksimal mahasiswa per edisi dan memastikan dosen
                                        pembimbing tidak sama dalam edisi yang sama—semuanya dapat diubah lewat panel
                                        aturan.
                                    </p>
                                </div>
                                <span class="badge bg-primary rounded-pill ms-auto">✓</span>
                            </div>

                            <img src="images/topics/colleagues-working-cozy-office-medium-shot.png"
                                class="custom-block-image img-fluid" alt="Aturan Ketersediaan Jurnal">
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mb-2">Ringkasan per Edisi</h5>
                                    <p class="mb-0">
                                        Lihat nama jurnal, link otomatis, jumlah mahasiswa yang sudah terpakai di setiap
                                        edisi, serta sisa slot secara real-time dari dashboard admin.
                                    </p>
                                </div>
                                <span class="badge bg-info rounded-pill ms-auto">✓</span>
                            </div>

                            <img src="images/topics/undraw_Finance_re_gnv2.png" class="custom-block-image img-fluid"
                                alt="Ringkasan Slot Jurnal">
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="timeline-section section-padding mt-5" id="section_3">
            <div class="section-overlay"></div>

            <div class="container">
                <div class="row">

                    <div class="col-12 text-center">
                        <h2 class="text-white mb-4">Bagaimana Sistem Ini Bekerja?</h2>
                    </div>

                    <div class="col-lg-10 col-12 mx-auto">
                        <div class="timeline-container">
                            <ul class="vertical-scrollable-timeline" id="vertical-scrollable-timeline">
                                <div class="list-progress">
                                    <div class="inner"></div>
                                </div>

                                <li>
                                    <h4 class="text-white mb-3">1. Masukkan Data Cek Jurnal</h4>
                                    <p class="text-white">
                                        Pengguna mengisi form dengan <em>link</em> rumah jurnal, memilih edisi (bulan &
                                        tahun),
                                        serta dosen pembimbing yang membimbing tugas akhir.
                                    </p>
                                    <div class="icon-holder">
                                        <i class="bi-pencil-square"></i>
                                    </div>
                                </li>

                                <li>
                                    <h4 class="text-white mb-3">2. Sistem Mengecek Ketersediaan</h4>
                                    <p class="text-white">
                                        Sistem memproses input, mencari data rumah jurnal di database, dan mencocokkan
                                        dengan aturan
                                        ketersediaan yang sudah ditentukan oleh admin (kuota maksimal dan dosen
                                        pembimbing unik).
                                    </p>
                                    <div class="icon-holder">
                                        <i class="bi-gear-fill"></i>
                                    </div>
                                </li>

                                <li>
                                    <h4 class="text-white mb-3">3. Hitung Slot Tersisa</h4>
                                    <p class="text-white">
                                        Sistem menghitung jumlah mahasiswa yang sudah terdaftar pada edisi tersebut,
                                        kemudian menampilkan sisa slot yang masih tersedia.
                                    </p>
                                    <div class="icon-holder">
                                        <i class="bi-bar-chart-line-fill"></i>
                                    </div>
                                </li>

                                <li>
                                    <h4 class="text-white mb-3">4. Tampilkan Hasil Cek</h4>
                                    <p class="text-white">
                                        Hasil ditampilkan secara jelas: nama rumah jurnal, link, edisi (bulan & tahun),
                                        nama dosen pembimbing, serta status ketersediaan (tersedia, penuh, atau tidak
                                        bisa digunakan).
                                    </p>
                                    <div class="icon-holder">
                                        <i class="bi-file-earmark-bar-graph"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-5">
                        <p class="text-white">
                            Ingin cek ketersediaan rumah jurnal Anda?
                            <a href="#section_1" class="btn custom-btn custom-border-btn ms-3">Mulai Cek</a>
                        </p>
                    </div>

                </div>
            </div>
        </section>


    </main>
    <footer class="site-footer section-padding">
        <div class="container">
            <div class="row">

                <!-- Brand dan Deskripsi -->
                <div class="col-lg-3 col-12 mb-4 pb-2">
                    <a class="navbar-brand mb-2" href="/">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>Rumah Jurnal</span>
                    </a>
                    <p class="text-white">
                        Sistem pengecekan ketersediaan rumah jurnal untuk mahasiswa tugas akhir.
                        Membantu memastikan slot jurnal, dosen pembimbing, dan edisi yang sesuai dengan aturan yang
                        berlaku.
                    </p>
                </div>

                <!-- Navigasi Menu -->
                <div class="col-lg-3 col-md-4 col-6">
                    <h6 class="site-footer-title mb-3">Menu</h6>
                    <ul class="site-footer-links">
                        <li class="site-footer-link-item"><a href="/" class="site-footer-link">Beranda</a></li>
                        <li class="site-footer-link-item"><a href="/admin/data-mahasiswa"
                                class="site-footer-link">Data Mahasiswa</a></li>
                        <li class="site-footer-link-item"><a href="/admin/data-dosen" class="site-footer-link">Data
                                Dosen</a></li>
                        <li class="site-footer-link-item"><a href="/admin/data-jurnal" class="site-footer-link">Data
                                Jurnal</a></li>
                        <li class="site-footer-link-item"><a href="/admin/kontrol-jurnal"
                                class="site-footer-link">Kontrol Jurnal</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
                    <h6 class="site-footer-title mb-3">Kontak</h6>
                    <p class="text-white d-flex mb-1">
                        <a href="tel:081234567890" class="site-footer-link">0812-3456-7890</a>
                    </p>
                    <p class="text-white d-flex">
                        <a href="mailto:admin@rumahjurnal.id" class="site-footer-link">admin@rumahjurnal.id</a>
                    </p>
                </div>

                <!-- Bahasa dan Hak Cipta -->
                <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                    <a href="http://s01.flagcounter.com/more/LkX5"><img
                            src="https://s01.flagcounter.com/count2/LkX5/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_10/viewers_Pengunjung/labels_0/pageviews_0/flags_0/percent_0/"
                            alt="Flag Counter" border="0"></a>

                    <p class="copyright-text mt-lg-5 mt-4">
                        © 2025 <a href="https://avinto.my.id">Alvin Alvtio</a><br>
                        All rights reserved.<br><br>
                    </p>
                </div>

            </div>
        </div>
    </footer>




    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>
    <!-- JAVASCRIPT FILES -->
    <script src="script/jquery.min.js"></script>
    <script src="script/bootstrap.bundle.min.js"></script>
    <script src="script/jquery.sticky.js"></script>
    <script src="script/click-scroll.js"></script>
    <script src="script/custom.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</body>

</html>
