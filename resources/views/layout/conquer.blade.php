<!DOCTYPE html>
<!--
Template Name: Conquer - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.2.0
Version: 2.0
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/conquer-responsive-admin-dashboard-template/3716838?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Conquer | Admin Dashboard Template</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="MobileOptimized" content="320">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('conquer/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('conquer/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('conquer/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="{{ asset('conquer/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('conquer/plugins/fullcalendar/fullcalendar/fullcalendar.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('conquer/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="{{ asset('conquer/css/style-conquer.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/css/style-responsive.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/css/pages/tasks.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('conquer/css/themes/default.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{ asset('conquer/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="header-inner">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                @if (auth()->user())
                    <a href="{{ route('homeProduk') }}">
                        <div class="flex items-center space-x-4 mb-3 pb-2">
                            <h1 class="text-3xl font-bold text-white mt-4">Purigama</h1>
                        </div>
                    </a>
                @else
                    <a href="{{ route('welcome') }}">
                        <div class="flex items-center space-x-4 mb-3 pb-2">
                            <h1 class="text-3xl font-bold text-white mt-4">Purigama</h1>
                        </div>
                    </a>
                @endif
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <img src="{{ asset('conquer/img/menu-toggler.png') }}" alt="" />
            </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <ul class="nav navbar-nav pull-right">
                <li class="devider">
                    &nbsp;
                </li>
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown user">
                    @if (auth()->user())
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                            data-close-others="true">
                            <span class="username username-hide-on-mobile">{{ auth()->user()->nama }}</span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                    @else
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                            data-close-others="true">
                            <span class="username username-hide-on-mobile">Guest</span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                    @endif
                    <ul class="dropdown-menu">
                        @if (auth()->user())
                            <li>
                                <a href="{{ route('profile') }}"><i class="fa fa-user"></i> My Profile</a>
                            </li>
                            <li class="divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <i class="fa fa-key"></i>
                                    <input type="submit" value="logout" class='btn btn-danger' />
                                </form>
                            </li>
                        @else
                            <li>
                                <form action="{{ route('login') }}" method="get">
                                    @csrf
                                    <i class="fa fa-key"></i>
                                    <input type="submit" value="login" class='btn btn-primary mb-3' />
                                </form>
                            </li>
                        @endif
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <div class="clearfix">
    </div>
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
                <!-- BEGIN SIDEBAR MENU -->
                <!-- DOC: for circle icon style menu apply page-sidebar-menu-circle-icons class right after sidebar-toggler-wrapper -->
                <ul class="page-sidebar-menu">
                    <li class="sidebar-toggler-wrapper">
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                        <div class="sidebar-toggler">
                        </div>
                        <div class="clearfix">
                        </div>
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    </li>
                    @if (auth()->user())
                        <li>
                            <a href="{{ route('homeProduk') }}">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profiltoko') }}">
                                <i class="icon-note"></i>
                                <span class="title">Profil Apotek</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                                <i class="icon-book-open"></i>
                                <span class="title">Transaksi</span>
                                <span class="arrow "></span>
                            </a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="{{ url('notajuals/create') }}">
                                        <i class="icon-basket-loaded"></i>
                                        Jual Produk</a>
                                </li>
                                <li>
                                    <a href="{{ url('notabelis/create') }}">
                                        <i class="icon-basket-loaded"></i>
                                        Beli Produk</a>
                                </li>
                                <li>
                                    <a href="{{ route('transaksi') }}">
                                        <i class="icon-book-open"></i> Daftar Nota <span class="arrow"></span>
                                    </a>
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="{{ url('notajuals') }}">
                                                <i class="icon-docs"></i>
                                                Nota Penjualan
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('notabelis') }}">
                                                <i class="icon-docs"></i>
                                                Nota Pembelian</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('produks.daftarTerima') }}">
                                                <i class="icon-docs"></i>
                                                Nota Penerimaan</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('parcels.notaParcel') }}">
                                                <i class="icon-docs"></i>
                                                Nota Parcel
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('produk') }}">
                                <i class="icon-present"></i>
                                <span class="title">Produk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('parcel') }}">
                                <i class="icon-briefcase"></i>
                                <span class="title">Parcel</span>
                            </a>
                        </li>
                        @auth
                            <li>
                                @if (auth()->user()->tipeuser->tipe === 'admin')
                                    <a href="javascript:;">
                                        <i class="icon-user"></i>
                                        <span class="title">Karyawan</span>
                                        <span class="arrow "></span>
                                    </a>
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="{{ route('register') }}">+👥 Register User</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('user') }}">👥 Daftar Karyawan</a>
                                        </li>
                                    </ul>
                                @endif
                            </li>
                        @endauth
                        <li>
                            <a href="{{ route('distributor') }}">
                                <i class="icon-share"></i>
                                <span class="title">Distributor</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('gudang') }}">
                                <i class="icon-briefcase"></i>
                                <span class="title">Gudang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('satuan') }}">
                                <i class="icon-layers"></i>
                                <span class="title">Satuan produk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tipeproduk') }}">
                                <i class="icon-layers"></i>
                                <span class="title">Tipe produk</span>
                            </a>
                        </li>
                        <li class="last ">
                            <a href="{{ route('logout') }}">
                                <i class="icon-user"></i>
                                <span class="title">Log out</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('welcome') }}">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profiltoko') }}">
                                <i class="icon-note"></i>
                                <span class="title">Profil Toko</span>
                            </a>
                        </li>
                        <li class="last ">
                            <a href="{{ route('login') }}">
                                <i class="icon-user"></i>
                                <span class="title">Log in</span>
                            </a>
                        </li>
                    @endif
                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">
                @yield('content')
                <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            </div>
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="footer">
        <div class="footer-inner">
            2013 &copy; Conquer by keenthemes.
        </div>
        <div class="footer-tools">
            <span class="go-top">
                <i class="fa fa-angle-up"></i>
            </span>
        </div>
    </div>
    <!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ asset('conquer/plugins/jquery-1.11.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jquery-migrate-1.2.1.min.js') }}" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="{{ asset('conquer/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('conquer/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/jquery.vmap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('conquer/plugins/jquery.peity.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jquery.pulsate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/jquery-knob/js/jquery.knob.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/flot/jquery.flot.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/flot/jquery.flot.resize.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/bootstrap-daterangepicker/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('conquer/plugins/gritter/js/jquery.gritter.js') }}" type="text/javascript"></script>
    <!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
    <script src="{{ asset('conquer/plugins/fullcalendar/fullcalendar/fullcalendar.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('conquer/plugins/jquery-easypiechart/jquery.easypiechart.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('conquer/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('conquer/scripts/app.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/scripts/index.js') }}" type="text/javascript"></script>
    <script src="{{ asset('conquer/scripts/tasks.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function() {
            App.init(); // initlayout and core plugins
            Index.init();
            Index.initJQVMAP(); // init index page's custom scripts
            Index.initCalendar(); // init index page's custom scripts
            Index.initCharts(); // init index page's custom scripts
            Index.initChat();
            Index.initMiniCharts();
            Index.initPeityElements();
            Index.initKnowElements();
            Index.initDashboardDaterange();
            Tasks.initDashboardWidget();
        });
    </script>
    @yield('javascript')
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->

</html>
