<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Dashboard | Merra Inventory')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('admin/assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    {{-- External Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Web Fonts --}}
    <script src="{{ asset('admin/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('admin/assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    {{-- Custom Sidebar Style --}}
    <style>
        .sidebar {
            transition: all 0.3s ease;
            transform: translateX(-100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1000;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: none !important;
                position: static;
            }
        }
    </style>

    {{-- Kaiadmin Core Styles --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />
</head>

<body>
    <div class="wrapper">
        @include('layouts.sidebar')

        <div class="main-panel">
            {{-- Main Header --}}
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ asset('admin/assets/img/kaiadmin/logomerra.png') }}" alt="Merra Logo"
                                class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="fa-solid fa-burger"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="fa-solid fa-burger"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                </div>
                @include('layouts.header')
            </div>

            @yield('content')
            @yield('kategori')
            @yield('daftarbahan')
            @yield('BahanMasuk')
            @yield('BahanAkhir')
            @yield('BahanAkhirKitchen')
            @yield('pengguna')
            @yield('BahanKeluar')
            @yield('inventaris')
            @yield('menu')
            @yield('profile')
            @yield('laporanbahan')
            @yield('laporanbahanmasuk')
            @yield('laporan-bahan-baku')
            @yield('laporanbahanakhir')
            @yield('laporanbahankeluar')
            @yield('MenuTerjual')
            @yield('indexLaporan')
            @yield('pemantauan')
            @yield('detailInventaris')
            @yield('databahanakhir')
            @yield('bahanProcess')

        </div>

        <!-- Custom template | don't include it in your project! -->
        <div class="custom-template">
            <div class="title">Settings</div>
            <div class="custom-content">
                <div class="switcher">
                    <div class="switch-block">
                        <h4>Logo Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="selected changeLogoHeaderColor" data-color="dark"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeLogoHeaderColor" data-color="dark2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Navbar Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeTopBarColor" data-color="dark"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="green"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange"></button>
                            <button type="button" class="changeTopBarColor" data-color="red"></button>
                            <button type="button" class="selected changeTopBarColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeTopBarColor" data-color="dark2"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple2"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="green2"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange2"></button>
                            <button type="button" class="changeTopBarColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Sidebar</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeSideBarColor" data-color="white"></button>
                            <button type="button" class="selected changeSideBarColor" data-color="dark"></button>
                            <button type="button" class="changeSideBarColor" data-color="dark2"></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Custom template -->
    </div>
{{-- Core Scripts --}}
<script src="{{ asset('admin/assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/core/bootstrap.min.js') }}"></script>

{{-- Plugins --}}
<script src="{{ asset('admin/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/chart-circle/circles.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/jsvectormap/world.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

{{-- Kaiadmin Scripts --}}
<script src="{{ asset('admin/assets/js/kaiadmin.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('admin/assets/js/demo.js') }}"></script>

{{-- Custom Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
        const sidenavTogglerBtn = document.querySelector('.sidenav-toggler');
        const topbarToggler = document.querySelector('.topbar-toggler');
        const sidebar = document.querySelector('.sidebar');

        function toggleSidebar() {
            document.body.classList.toggle('nav_open');
            sidebar.classList.toggle('open');
        }

        if (toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', e => {
                e.stopPropagation();
                document.body.classList.toggle('sidebar_minimize');
            });
        }

        if (sidenavTogglerBtn) {
            sidenavTogglerBtn.addEventListener('click', e => {
                e.stopPropagation();
                toggleSidebar();
            });
        }

        if (topbarToggler) {
            topbarToggler.addEventListener('click', e => {
                e.stopPropagation();
                document.body.classList.toggle('topbar_open');
            });
        }

        document.addEventListener('click', e => {
            if (sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !sidenavTogglerBtn.contains(e.target)) {
                sidebar.classList.remove('open');
                document.body.classList.remove('nav_open');
            }
        });

        if (sidebar) {
            sidebar.addEventListener('click', e => e.stopPropagation());
        }

        // Pusher for stok notification
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            forceTLS: true
        });

        var channel = pusher.subscribe('stok-menipis');
        channel.bind('stokMenipis', function (data) {
            alert("⚠️ Stok " + data.bahan.nama_bahan + " menipis! Sisa: " + data.bahan.sisa_stok);
        });

        // Sparkline charts
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    });
</script>
    </script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>
