<ul class="menu">
    <li class="sidebar-title">Menu</li>
    @if(Auth::check() && Auth::user()->hasRole('admin'))
    <li class="sidebar-item @if(Request::is('home') || Request::is('/home')) active @endif">
        <a href="/home" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Barcode -->
    <li class="sidebar-item @if(Request::is('autoGate-barcode')) active @endif">
        <a href="/autoGate-barcode" class='sidebar-link'><i class="fa-solid fa-barcode"></i><span>Barcode</span></a>
    </li>

    <!-- Dokumen -->

    <li class="sidebar-item has-sub @if(Request::is('dokumen/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-file"></i>
            <span>Dokumen Beacukai</span>
        </a>
        <ul class="submenu @if(Request::is('dokumen/*')) active @endif">
            <li class="submenu-item @if(Request::is('dokumen/plp') || Request::is('dokumen/plp/*')) active @endif">
                <a href="{{ url('/dokumen/plp')}}">Dokumen Respon PLP</a>
            </li>
            <li class="submenu-item @if(Request::is('dokumen/spjm') || Request::is('dokumen/spjm/*')) active @endif">
                <a href="{{ url('/dokumen/spjm')}}">Dokumen SPJM</a>
            </li>
            <li class="submenu-item @if(Request::is('dokumen/bc23') || Request::is('dokumen/bc23/*')) active @endif">
                <a href="{{ url('/dokumen/bc23')}}">Dokumen SPPB BC23</a>
            </li>
            <li class="submenu-item @if(Request::is('dokumen/sppb') || Request::is('dokumen/sppb/*')) active @endif">
                <a href="{{ url('/dokumen/sppb')}}">Dokumen SPPB</a>
            </li>
            <li class="submenu-item @if(Request::is('dokumen/pabean') || Request::is('dokumen/pabean/*')) active @endif">
                <a href="{{ url('/dokumen/pabean')}}">Dokumen Pabean</a>
            </li>
            <li class="submenu-item @if(Request::is('dokumen/manual') || Request::is('dokumen/manual/*')) active @endif">
                <a href="{{ url('/dokumen/manual')}}">Dokumen Manual</a>
            </li>
        </ul>
    </li>

    <!-- LCL -->
    <li class="sidebar-item has-sub @if(Request::is('lcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>LCL</span>
        </a>
        <ul class="submenu @if(Request::is('lcl/*')) active @endif">
            <li class="submenu-item @if(Request::is('lcl/register') || Request::is('lcl/register/*')) active @endif">
                <a href="{{ url('/lcl/register')}}">Register</a>
            </li>
            <li class="submenu-item @if(Request::is('lcl/manifest') || Request::is('lcl/manifest/*')) active @endif">
                <a href="{{ url('/lcl/manifest')}}">Manifest</a>
            </li>
            <!-- Realisasi -->
            <li class="sidebar-item has-sub @if(Request::is('lcl/realisasi/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Realisasi</span>
                </a>
                <ul class="submenu @if(Request::is('lcl/realisasi/*')) active @endif">
                    <li class="submenu-item @if(Request::is('lcl/realisasi/seal') || Request::is('lcl/realisasi/seal/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/seal')}}">Dispathce E-Seal</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/realisasi/gateIn') || Request::is('lcl/realisasi/gateIn/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/gateIn')}}">Gate In Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/realisasi/placementCont') || Request::is('lcl/realisasi/placementCont/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/placementCont')}}">Placement Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/realisasi/stripping') || Request::is('lcl/realisasi/stripping/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/stripping')}}">Stripping</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/realisasi/racking') || Request::is('lcl/realisasi/racking/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/racking')}}">Racking</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/realisasi/buangMT') || Request::is('lcl/realisasi/buangMT/*')) active @endif">
                        <a href="{{ url('/lcl/realisasi/buangMT')}}">Buang Empty</a>
                    </li>
                </ul>
            </li>

            <!-- Delivery -->
            <li class="sidebar-item has-sub @if(Request::is('lcl/delivery/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Delivery</span>
                </a>
                <ul class="submenu @if(Request::is('lcl/delivery/*')) active @endif">
                    <li class="submenu-item @if(Request::is('lcl/delivery/behandle/index') || Request::is('lcl/delivery/behandle/*')) active @endif">
                        <a href="{{ url('/lcl/delivery/behandle/index')}}">Behandle</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/delivery/gateOut') || Request::is('lcl/delivery/gateOut/*')) active @endif">
                        <a href="{{ url('/lcl/delivery/gateOut')}}">Gate Out</a>
                    </li>
                </ul>
            </li>

            <!-- Report -->
            <li class="sidebar-item has-sub @if(Request::is('lcl/report/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Report</span>
                </a>
                <ul class="submenu @if(Request::is('lcl/report/*')) active @endif">
                    <li class="submenu-item @if(Request::is('lcl/report/cont')) active @endif">
                        <a href="{{ url('/lcl/report/cont')}}">Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/report/manifest')) active @endif">
                        <a href="{{ url('/lcl/report/manifest')}}">Manifest</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/report/daily*')) active @endif">
                        <a href="{{ route('report.lcl.daily')}}">Daily</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- FCL -->
    <li class="sidebar-item has-sub @if(Request::is('fcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>FCL</span>
        </a>
        <ul class="submenu @if(Request::is('fcl/*')) active @endif">
            <li class="submenu-item @if(Request::is('fcl/register') || Request::is('fcl/register/*')) active @endif">
                <a href="{{ url('/fcl/register/index')}}">Register</a>
            </li>
            <li class="submenu-item @if(Request::is('fcl/containerList') || Request::is('fcl/containerList/*')) active @endif">
                <a href="{{ url('/fcl/containerList/index')}}">Container List</a>
            </li>
            <!-- Realisasi -->
            <li class="sidebar-item has-sub @if(Request::is('fcl/realisasi/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Realisasi</span>
                </a>
                <ul class="submenu @if(Request::is('fcl/realisasi/*')) active @endif">
                    <li class="submenu-item @if(Request::is('fcl/realisasi/seal') || Request::is('fcl/realisasi/seal/*')) active @endif">
                        <a href="{{ url('/fcl/realisasi/seal')}}">Dispathce E-Seal</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/realisasi/gateIn') || Request::is('fcl/realisasi/gateIn/*')) active @endif">
                        <a href="{{ url('/fcl/realisasi/gateIn')}}">Gate In Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/realisasi/placementCont') || Request::is('fcl/realisasi/placementCont/*')) active @endif">
                        <a href="{{ url('/fcl/realisasi/placementCont')}}">Placement Container</a>
                    </li>
                </ul>
            </li>

            <!-- Delivery -->
            <li class="sidebar-item has-sub @if(Request::is('fcl/delivery/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Delivery</span>
                </a>
                <ul class="submenu @if(Request::is('fcl/delivery/*')) active @endif">
                    <li class="submenu-item @if(Request::is('fcl/delivery/behandle') || Request::is('fcl/delivery/behandle/*')) active @endif">
                        <a href="{{ url('/fcl/delivery/behandle')}}">Behandle</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/delivery/gateOut') || Request::is('fcl/delivery/gateOut/*')) active @endif">
                        <a href="{{ url('/fcl/delivery/gateOut')}}">Gate Out</a>
                    </li>
                </ul>
            </li>

            <!-- Report -->
            <li class="sidebar-item has-sub @if(Request::is('fcl/report/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Report</span>
                </a>
                <ul class="submenu @if(Request::is('fcl/report/*')) active @endif">
                    <li class="submenu-item @if(Request::is('fcl/report/index')) active @endif">
                        <a href="{{ url('/fcl/report/index')}}">Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/report/daily/*')) active @endif">
                        <a href="{{ route('report.lcl.daily')}}">Daily</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- Photo -->
    <li class="sidebar-item has-sub @if(Request::is('photo/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-camera"></i>
            <span>Photo</span>
        </a>
        <ul class="submenu @if(Request::is('photo/*')) active @endif">
            <!-- Realisasi -->
            <li class="sidebar-item has-sub @if(Request::is('photo/lcl/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>LCL</span>
                </a>
                <ul class="submenu @if(Request::is('photo/lcl/*')) active @endif">
                    <li class="submenu-item @if(Request::is('photo/lcl/manifest') || Request::is('photo/lcl/manifest/*')) active @endif">
                        <a href="{{ url('/photo/lcl/manifest')}}">Manifest</a>
                    </li>
                    <li class="submenu-item @if(Request::is('photo/lcl/container') || Request::is('photo/lcl/container/*')) active @endif">
                        <a href="{{ url('/photo/lcl/container')}}">Container</a>
                    </li>
                </ul>
            </li>

            <!-- Delivery -->
            <li class="submenu-item  @if(Request::is('photo/fcl/*')) active @endif">
                <a href="{{ url('/photo/fcl/container')}}">FCL</a>
            </li>
        </ul>
    </li>

    <!-- Master -->
    <li class="sidebar-item  has-sub @if(Request::is('master/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-stack"></i>
            <span>Master</span>
        </a>
        <ul class="submenu @if(Request::is('master/*')) active @endif">
            <li class="submenu-item @if(Request::is('master/customer')) active @endif">
                <a href="{{ url('/master/customer')}}">Customer</a>
            </li>
            <li class="submenu-item @if(Request::is('master/consolidator')) active @endif">
                <a href="{{ route('master.consolidator.index')}}">Consolidator</a>
            </li>
            <li class="submenu-item @if(Request::is('master/depoMT')) active @endif">
                <a href="{{ route('master.depoMT.index')}}">Depo Empty</a>
            </li>
            <li class="submenu-item @if(Request::is('master/eseal')) active @endif">
                <a href="{{ route('master.eseal.index')}}">E-Seal</a>
            </li>
            <li class="submenu-item @if(Request::is('master/gudang')) active @endif">
                <a href="{{ route('master.gudang.index')}}">Gudang</a>
            </li>
            <li class="submenu-item @if(Request::is('master/photo')) active @endif">
                <a href="/master/photo">Keterangan Photo</a>
            </li>
            <li class="submenu-item @if(Request::is('master/lokasiSandar')) active @endif">
                <a href="{{ route('master.lokasiSandar.index')}}">Lokasi Sandar</a>
            </li>
            <li class="submenu-item @if(Request::is('master/negara')) active @endif">
                <a href="{{ route('master.negara.index')}}">Negara</a>
            </li>
            <li class="submenu-item @if(Request::is('master/packing')) active @endif">
                <a href="{{ route('master.packing.index')}}">Packing</a>
            </li>
            <li class="submenu-item @if(Request::is('master/pelabuhan')) active @endif">
                <a href="{{ route('master.pelabuhan.index')}}">Pelabuhan</a>
            </li>
            <li class="submenu-item @if(Request::is('master/perusahaan')) active @endif">
                <a href="{{ route('master.perusahaan.index')}}">Perusahaan</a>
            </li>
            <li class="submenu-item @if(Request::is('master/ppjk')) active @endif">
                <a href="{{ route('master.ppjk.index')}}">PPJK</a>
            </li>
            <li class="submenu-item @if(Request::is('master/shippingLines')) active @endif">
                <a href="{{ route('master.shippingLines.index')}}">Shipping Lines</a>
            </li>
            <li class="submenu-item @if(Request::is('master/ves')) active @endif">
                <a href="{{ route('master.ves.index')}}">Vessel</a>
            </li>
            <li class="submenu-item @if(Request::is('master/rack')) active @endif">
                <a href="{{ route('master.rack.index')}}">Rack Manifest</a>
            </li>
            <li class="submenu-item @if((Request::is('master/yard') || Request::is('master/yard*'))) active @endif">
                <a href="{{ route('master.yard.index')}}">Yard</a>
            </li>
           
        </ul>
    </li>  
    
    <!-- System -->
    <li class="sidebar-item has-sub @if(Request::is('user/*') || Request::is('role/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-wrench"></i>
            <span>User & Role</span>
        </a>
        <ul class="submenu @if(Request::is('user/*') || Request::is('role/*')) active @endif">
            <li class="submenu-item @if(Request::is('user/index-user')) active @endif">
                <a href="{{ url('/user/index-user')}}">User Management</a>
            </li>
            <li class="submenu-item @if(Request::is('role/index-role')) active @endif">
                <a href="{{ url('/role/index-role')}}">Role Management</a>
            </li>
        </ul>
    </li>
    <hr>
    <!-- Invoice LCL -->
    <li class="sidebar-item has-sub @if(Request::is('invoice/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-dollar"></i>
            <span>Invoice</span>
        </a>
        <ul class="submenu @if(Request::is('invoice/*')) active @endif">
            <li class="sidebar-item has-sub @if(Request::is('invoice/master/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Master</span>
                </a>
                <ul class="submenu @if(Request::is('invoice/master/*')) active @endif">
                    <li class="submenu-item @if(Request::is('invoice/master/tarif')) active @endif">
                        <a href="{{ url('/invoice/master/tarif')}}">Tarif</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item has-sub @if(Request::is('invoice/form/*') && !Request::is('invoice/form/perpanjangan/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Form Invoice</span>
                </a>
                <ul class="submenu @if(Request::is('invoice/form/*') && !Request::is('invoice/form/perpanjangan/*')) active @endif">
                    <li class="submenu-item @if(Request::is('invoice/form/index')) active @endif">
                        <a href="{{ url('/invoice/form/index')}}">Created Invoice</a>
                    </li>
                    <li class="submenu-item @if(Request::is('invoice/form/unpaid')) active @endif">
                        <a href="{{ url('/invoice/form/unpaid')}}">Unpaid Invoice</a>
                    </li>
                    <li class="submenu-item @if(Request::is('invoice/form/paid')) active @endif">
                        <a href="{{ url('/invoice/form/paid')}}">Paid Invoice</a>
                    </li>
                </ul>
            </li>
            <!-- Perpanjangan -->
            <li class="sidebar-item has-sub @if(Request::is('invoice/form/perpanjangan/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Form Invoice Perpanjangan</span>
                </a>
                <ul class="submenu @if(Request::is('invoice/form/perpanjangan/*')) active @endif">
                    <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/index')) active @endif">
                        <a href="{{ url('/invoice/form/perpanjangan/index')}}">Created Invoice Perpanjangan</a>
                    </li>
                    <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/unpaid')) active @endif">
                        <a href="{{ url('/invoice/form/perpanjangan/unpaid')}}">Unpaid Invoice Perpanjangan</a>
                    </li>
                    <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/paid')) active @endif">
                        <a href="{{ url('/invoice/form/perpanjangan/paid')}}">Paid Invoice Perpanjangan</a>
                    </li>
                </ul>
            </li>
            <li class="submenu-item @if(Request::is('invoice/report')) active @endif">
                <a href="{{ url('/invoice/report')}}">Rport Invoice</a>
            </li>
        </ul>
    </li>
    <!-- Invoice FCL -->
    <li class="sidebar-item has-sub @if(Request::is('invoiceFCL/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-dollar"></i>
            <span>Invoice FCL</span>
        </a>
        <ul class="submenu @if(Request::is('invoiceFCL/*')) active @endif">
            <li class="sidebar-item @if(Request::is('invoiceFCL/dashboard') || Request::is('/invoiceFCL/dashboard')) active @endif">
                <a href="/invoiceFCL/dashboard" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard Invoice FCL</span>
                </a>
            </li>
            <!--  -->
            <li class="sidebar-item has-sub @if(Request::is('invoiceFCL/masterTarif/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Master</span>
                </a>
                <ul class="submenu @if(Request::is('invoiceFCL/masterTarif/*')) active @endif">
                    <li class="submenu-item @if(Request::is('invoiceFCL/masterTarif/index')) active @endif">
                        <a href="{{ url('/invoiceFCL/masterTarif/index')}}">Tarif</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item has-sub @if(Request::is('invoiceFCL/form/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Form</span>
                </a>
                <ul class="submenu @if(Request::is('invoiceFCL/form/*')) active @endif">
                    <li class="submenu-item @if(Request::is('invoiceFCL/form/index')) active @endif">
                        <a href="{{ url('/invoiceFCL/form/index')}}">Form Index</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item @if(Request::is('invoiceFCL/invoice/index') || Request::is('/invoiceFCL/invoice/index')) active @endif">
                <a href="/invoiceFCL/invoice/index" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>List Invoice FCL</span>
                </a>
            </li>
            
            <li class="submenu-item @if(Request::is('invoice/report')) active @endif">
                <a href="{{ url('/invoice/report')}}">Rport Invoice</a>
            </li>
        </ul>
    </li>
    @elseif(Auth::check() && Auth::user()->hasRole('invoice'))
    <li class="sidebar-item @if(Request::is('dashboard-invoice') || Request::is('/dashboard-invoice')) active @endif">
        <a href="/dashboard-invoice" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('invoice/master/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <span>Master</span>
        </a>
        <ul class="submenu @if(Request::is('invoice/master/*')) active @endif">
            <li class="submenu-item @if(Request::is('invoice/master/tarif')) active @endif">
                <a href="{{ url('/invoice/master/tarif')}}">Tarif</a>
            </li>
        </ul>
    </li>
    <li class="sidebar-item has-sub @if(Request::is('invoice/form/*') && !Request::is('invoice/form/perpanjangan/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <span>Form Invoice</span>
        </a>
        <ul class="submenu @if(Request::is('invoice/form/*') && !Request::is('invoice/form/perpanjangan/*')) active @endif">
            <li class="submenu-item @if(Request::is('invoice/form/index')) active @endif">
                <a href="{{ url('/invoice/form/index')}}">Created Invoice</a>
            </li>
            <li class="submenu-item @if(Request::is('invoice/form/unpaid')) active @endif">
                <a href="{{ url('/invoice/form/unpaid')}}">Unpaid Invoice</a>
            </li>
            <li class="submenu-item @if(Request::is('invoice/form/paid')) active @endif">
                <a href="{{ url('/invoice/form/paid')}}">Paid Invoice</a>
            </li>
        </ul>
    </li>
    <!-- Perpanjangan -->
    <li class="sidebar-item has-sub @if(Request::is('invoice/form/perpanjangan/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <span>Form Invoice Perpanjangan</span>
        </a>
        <ul class="submenu @if(Request::is('invoice/form/perpanjangan/*')) active @endif">
            <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/index')) active @endif">
                <a href="{{ url('/invoice/form/perpanjangan/index')}}">Created Invoice Perpanjangan</a>
            </li>
            <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/unpaid')) active @endif">
                <a href="{{ url('/invoice/form/perpanjangan/unpaid')}}">Unpaid Invoice Perpanjangan</a>
            </li>
            <li class="submenu-item @if(Request::is('invoice/form/perpanjangan/paid')) active @endif">
                <a href="{{ url('/invoice/form/perpanjangan/paid')}}">Paid Invoice Perpanjangan</a>
            </li>
        </ul>
    </li>
    
    <li class="sidebar-item @if(Request::is('invoice/report')) active @endif">
        <a href="{{ url('/invoice/report')}}" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Report Invoice</span>
        </a>
    </li>
    @elseif(Auth::check() && Auth::user()->hasRole('bcP2'))
    <li class="sidebar-item @if(Request::is('bc-p2/dashboard') || Request::is('/bc-p2/dashboard')) active @endif">
        <a href="/bc-p2/dashboard" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('bc-p2/lcl/*') || Request::is('/bc-p2/lcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>LCL</span>
        </a>
        <ul class="submenu @if(Request::is('bc-p2/lcl/*') || Request::is('/bc-p2/lcl/*')) active @endif">
            <!-- Realisasi -->
            <li class="submenu-item @if(Request::is('bc-p2/lcl/list-manifest') || Request::is('/bc-p2/lcl/list-manifest')) active @endif">
                <a href="{{ url('/bc-p2/lcl/list-manifest')}}">List Manifest</a>
            </li>
            <li class="submenu-item @if(Request::is('bc-p2/list-segelMerah') || Request::is('/bc-p2/list-segelMerah')) active @endif">
                <a href="{{ url('/bc-p2/list-segelMerah')}}">List Segel Merah</a>
            </li>
        </ul>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('bc-p2/fcl/*') || Request::is('/bc-p2/fcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>FCL</span>
        </a>
        <ul class="submenu @if(Request::is('bc-p2/fcl/*') || Request::is('/bc-p2/fcl/*')) active @endif">
            <!-- Realisasi -->
            <li class="submenu-item @if(Request::is('bc-p2/fcl/list-container') || Request::is('/bc-p2/fcl/list-container')) active @endif">
                <a href="{{ url('/bc-p2/fcl/list-container')}}">List Container</a>
            </li>
            <li class="submenu-item @if(Request::is('/bc-p2/fcl/list-segelMerah') || Request::is('/bc-p2/fcl/list-segelMerah')) active @endif">
                <a href="{{ url('/bc-p2/fcl/list-segelMerah')}}">List Segel Merah</a>
            </li>
        </ul>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('lcl/report/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <span>Report LCL</span>
        </a>
        <ul class="submenu @if(Request::is('lcl/report/*')) active @endif">
            <li class="submenu-item @if(Request::is('lcl/report/cont')) active @endif">
                <a href="{{ url('/lcl/report/cont')}}">Container</a>
            </li>
            <li class="submenu-item @if(Request::is('lcl/report/manifest')) active @endif">
                <a href="{{ url('/lcl/report/manifest')}}">Manifest</a>
            </li>
            <li class="submenu-item @if(Request::is('lcl/report/daily/*')) active @endif">
                <a href="{{ route('report.lcl.daily')}}">Daily</a>
            </li>
        </ul>
    </li>
    @else
    <li class="sidebar-item @if(Request::is('bc/dashboard') || Request::is('/home')) active @endif">
        <a href="/bc/dashboard" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('bc/lcl/*') || Request::is('lcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>LCL</span>
        </a>
        <ul class="submenu @if(Request::is('bc/lcl/*') || Request::is('lcl/*')) active @endif">
            <!-- Realisasi -->
            <li class="sidebar-item has-sub @if(Request::is('bc/lcl/realisasi/*') || Request::is('lcl/realisasi/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Realisasi</span>
                </a>
                <ul class="submenu @if(Request::is('bc/lcl/realisasi/*') || Request::is('lcl/realisasi/*')) active @endif">
                    <li class="submenu-item @if(Request::is('bc/lcl/realisasi/buangMT') || Request::is('bc/lcl/realisasi/buangMT/*')) active @endif">
                        <a href="{{ url('/bc/lcl/realisasi/buangMT')}}">Buang Empty</a>
                    </li>
                    <li class="submenu-item @if(Request::is('bc/lcl/realisasi/stripping/*') || Request::is('/bc/lcl/realisasi/stripping/*')) active @endif">
                        <a href="{{ url('/bc/lcl/realisasi/stripping')}}">Stripping</a>
                    </li>
                </ul>
            </li>

            <!-- Delivery -->
            <li class="sidebar-item has-sub @if(Request::is('bc/lcl/delivery/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Delivery</span>
                </a>
                <ul class="submenu @if(Request::is('bc/lcl/delivery/*')) active @endif">
                    <li class="submenu-item @if(Request::is('bc/lcl/delivery/behandle') || Request::is('bc/lcl/delivery/behandle/*')) active @endif">
                        <a href="{{ url('/bc/lcl/delivery/behandle')}}">Behandle</a>
                    </li>
                    <li class="submenu-item @if(Request::is('bc/lcl/delivery/gateOut') || Request::is('bc/lcl/delivery/gateOut/*')) active @endif">
                        <a href="{{ url('/bc/lcl/delivery/gateOut')}}">Gate Out</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item has-sub @if(Request::is('lcl/report/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Report</span>
                </a>
                <ul class="submenu @if(Request::is('lcl/report/*')) active @endif">
                    <li class="submenu-item @if(Request::is('lcl/report/cont')) active @endif">
                        <a href="{{ url('/lcl/report/cont')}}">Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/report/manifest')) active @endif">
                        <a href="{{ url('/lcl/report/manifest')}}">Manifest</a>
                    </li>
                    <li class="submenu-item @if(Request::is('lcl/report/daily/*')) active @endif">
                        <a href="{{ route('report.lcl.daily')}}">Daily</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('bc/fcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>FCL</span>
        </a>
        <ul class="submenu @if(Request::is('bc/fcl/*')) active @endif">
            <!-- Realisasi -->
            <li class="submenu-item @if(Request::is('bc/fcl/holdContainerIndex') || Request::is('/bc/fcl/holdContainerIndex')) active @endif">
                <a href="{{ url('/bc/fcl/holdContainerIndex')}}">Hold Container</a>
            </li>
            <li class="submenu-item @if(Request::is('bc/fcl/releaseContainerIndex') || Request::is('/bc/fcl/releaseContainerIndex')) active @endif">
                <a href="{{ url('/bc/fcl/releaseContainerIndex')}}">Release Container</a>
            </li>

            <!-- Delivery -->
            <!-- <li class="sidebar-item has-sub @if(Request::is('fcl/delivery/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Delivery</span>
                </a>
                <ul class="submenu @if(Request::is('fcl/delivery/*')) active @endif">
                    <li class="submenu-item @if(Request::is('fcl/delivery/behandle') || Request::is('fcl/delivery/behandle/*')) active @endif">
                        <a href="{{ url('/fcl/delivery/behandle')}}">Behandle</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/delivery/gateOut') || Request::is('fcl/delivery/gateOut/*')) active @endif">
                        <a href="{{ url('/fcl/delivery/gateOut')}}">Gate Out</a>
                    </li>
                </ul>
            </li> -->

            <!-- Report -->
            <li class="sidebar-item has-sub @if(Request::is('fcl/report/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Report</span>
                </a>
                <ul class="submenu @if(Request::is('fcl/report/*')) active @endif">
                    <li class="submenu-item @if(Request::is('fcl/report/cont')) active @endif">
                        <a href="{{ url('/fcl/report/cont')}}">Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('fcl/report/daily/*')) active @endif">
                        <a href="{{ route('report.lcl.daily')}}">Daily</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    @endif
</ul> 