<div class="sidebar" data-background-color="dark" style="background-color: #2e2e2e;">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark" style="background-color: #3a3a3a;">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('admin/assets/img/kaiadmin/favicon.png') }}" alt="Merra's Logo" class="navbar-brand" height="70" style="padding-top:10px;" />
            </a>
            <div class="nav-toggle d-flex align-items-center">
                <!-- Toggle open (misalnya saat sidebar tertutup) -->
                <button class="btn btn-toggle toggle-sidebar" type="button" aria-label="Open sidebar">
                    <i class="gg-menu-right"></i>
                </button>
            
                <!-- Toggle close (misalnya saat sidebar terbuka) -->
                <button class="btn btn-toggle sidenav-toggler d-none" type="button" aria-label="Close sidebar">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            
            <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner" style="background-color: #2e2e2e;">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard')}}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if(auth()->user()->hasRole(roles: 'Admin'))
                <li class="nav-item {{ Request::routeIs('kategori.index') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}">
                        <i class="fas fa-tags"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                @endif
                
                <li class="nav-item {{ Request::routeIs('bahan.*') ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#bahan">
                        <i class="fas fa-cocktail"></i>
                        <p>Bahan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::routeIs('bahan.*') ? 'show' : '' }}" id="bahan">
                        <ul class="nav nav-collapse">
                            <li><a href="{{ route('bahan.index')}}" class="{{ Request::routeIs('bahan.index') ? 'active' : '' }}"><span class="sub-item">Bahan Baku</span></a></li>
                            <li><a href="{{ route('bahan.process')}}" class="{{ Request::routeIs('bahan.process') ? 'active' : '' }}"><span class="sub-item">Bahan Process</span></a></li>
                            <li><a href="{{ route('bahan.bahanmasuk')}}" class="{{ Request::routeIs('bahan.bahanmasuk') ? 'active' : '' }}"><span class="sub-item">Bahan Masuk</span></a></li>
                            <li><a href="{{ route('bahan.bahanakhir')}}" class="{{ Request::routeIs('bahan.bahanakhir') ? 'active' : '' }}"><span class="sub-item">Bahan Akhir</span></a></li>
                            <li><a href="{{ route('bahan.bahankeluar')}}" class="{{ Request::routeIs('bahan.bahankeluar') ? 'active' : '' }}"><span class="sub-item">Bahan Keluar</span></a></li>
                        </ul>
                    </div>
                </li>
                
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Headkitchen')|| auth()->user()->hasRole('Bar')|| auth()->user()->hasRole('Kitchen'))
                <li class="nav-item {{ Request::routeIs('menu.index') ? 'active' : '' }}">
                    <a href="{{ route('menu.index') }}" class="nav-link">
                        <i class="fa-solid fa-receipt"></i>
                        <p>Menu</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Bar'))
                <li class="nav-item {{ Request::routeIs('menu.terjual.index') ? 'active' : '' }}">
                    <a href="{{ route('menu.terjual.index') }}" class="nav-link">
                        <i class="fa-solid fa-receipt"></i>
                        <p>Menu Terjual</p>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar')|| auth()->user()->hasRole('Headkitchen'))
                <li class="nav-item {{ Request::routeIs('inventaris.index') ? 'active' : '' }}">
                    <a href="{{ route('inventaris.index') }}" class="nav-link">
                        <i class="fas fa-box-open"></i>
                        <p>Inventaris</p>
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->hasRole('Admin')|| auth()->user()->hasRole('Headbar')|| auth()->user()->hasRole('Headkitchen'))
                <li class="nav-item {{ Request::routeIs('laporan.*') ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#laporan">
                        <i class="fas fa-file-alt"></i>
                        <p>Laporan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::routeIs('laporan.*') ? 'show' : '' }}" id="laporan">
                        <ul class="nav nav-collapse">
                            <li><a href="{{route('laporan.pemantauan')}}"class="{{ Request::routeIs('laporan.pemantauan') ? 'active' : '' }}"><span class="sub-item">PEMANTAUAN BAHAN BAKU</span></a></li>
                            <li><a href="{{route('laporan.bahan')}}" class="{{ Request::routeIs('laporan.bahan') ? 'active' : '' }}"><span class="sub-item">Bahan Baku</span></a></li>
                            <li><a href="{{route('laporan.bahanmasuk')}}" class="{{ Request::routeIs('laporan.bahanmasuk') ? 'active' : '' }}"><span class="sub-item">Bahan Masuk</span></a></li>
                            <li><a href="{{route('laporan.bahanakhir')}}"><span class="sub-item">Bahan Akhir</span></a></li>
                            <li><a href="{{route('laporan.bahankeluar')}}"class="{{ Request::routeIs('laporan.bahankeluar') ? 'active' : '' }}"><span class="sub-item">Bahan Keluar</span></a></li>
                            <li><a href="{{route('laporan.keseluruhanbahanbaku')}}"class="{{ Request::routeIs('laporan.keseluruhanbahanbaku') ? 'active' : '' }}"><span class="sub-item">Semua Bahan Baku</span></a></li>
                        </ul>
                    </div>
                </li>
                @endif

                @if (auth()->user()->hasRole('Admin'))
                <li class="nav-item {{ Request::routeIs('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index')}}">
                        <i class="fas fa-user-cog"></i>
                        <p>Pengguna</p>
                    </a>
                </li>
                @endif
                <li class="nav-item {{ Request::routeIs('logout') ? 'active' : '' }}">
                    <a href="{{ route('logout') }}" class="nav-link">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <p>Log Out</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
