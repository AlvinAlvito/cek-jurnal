<nav>
    <div class="logo-name">
        <div class="logo-image">
            <img src="/images/logo.jpg"  alt="">
        </div>

        <span class="logo_name">Admin</span>
    </div>

    <div class="menu-items">
        <ul class="nav-links">
            <li >
                <a onclick="window.location.href='/admin'" href="javascript:void(0)" class="{{ Request::is('/admin') ? 'active' : '' }}">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Beranda</span>
                </a>
            </li>
             <li >
                <a href="/admin/data-dosen" class="{{ Request::is('/admin/data-dosen') ? 'active' : '' }}">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Data Dosen</span>
                </a>
            </li>
            <li >
                <a href="/admin/data-mahasiswa" class="{{ Request::is('/admin/data-mahasiswa') ? 'active' : '' }}">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Data Mahasiswa</span>
                </a>
            </li>
             <li >
                <a href="/admin/data-jurnal" class="{{ Request::is('/admin/data-jurnal') ? 'active' : '' }}">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Data jurnal</span>
                </a>
            </li>
            <li >
                <a href="/admin/data-jurnalcek" class="{{ Request::is('/admin/data-jurnalcek') ? 'active' : '' }}">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Data Cek jurnal</span>
                </a>
            </li>
             <li >
                <a href="/admin/kontrol-jurnal" class="{{ Request::is('/admin/kontrol-jurnal') ? 'active' : '' }}">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Kontrol jurnal</span>
                </a>
            </li>
           
           
        </ul>
        

        <ul class="logout-mode">
            <li><a href="/">
                    <i class="uil uil-signout"></i>
                    <span class="link-name">Logout</span>
                </a></li>

            <li class="mode">
                <a href="#">
                    <i class="uil uil-moon"></i>
                    <span class="link-name">Dark Mode</span>
                </a>

                <div class="mode-toggle">
                    <span class="switch"></span>
                </div>
            </li>
        </ul>
    </div>
</nav>
