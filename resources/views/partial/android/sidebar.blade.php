<ul class="menu">
    <li class="sidebar-title">Menu</li>
    <li class="sidebar-item @if(Request::is('android/dashboard') || Request::is('/android/dashboard')) active @endif">
        <a href="/android/dashboard" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- LCL -->
    <li class="sidebar-item has-sub @if(Request::is('android/lcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>LCL</span>
        </a>
        <ul class="submenu @if(Request::is('android/lcl/*')) active @endif">
            <!-- Stripping -->
            <li class="sidebar-item has-sub @if(Request::is('android/photo/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>Stripping</span>
                </a>
                <ul class="submenu @if(Request::is('android/lcl/stripping/*')) active @endif">
                    <li class="submenu-item @if(Request::is('android/lcl/stripping/index') || Request::is('/android/lcl/stripping/index')) active @endif">
                        <a href="{{ url('/android/lcl/stripping/index')}}">Container</a>
                    </li>
                    <li class="submenu-item @if(Request::is('android/lcl/stripping/manifest') || Request::is('/android/lcl/stripping/manifest')) active @endif">
                        <a href="{{ url('/android/lcl/stripping/manifest')}}">Manifest</a>
                    </li>
                </ul>
            </li>
            <!--  -->
            <li class="submenu-item @if(Request::is('android/lcl/placementCont') || Request::is('android/lcl/placementCont/*')) active @endif">
                <a href="{{ url('/android/lcl/placementCont')}}">Placement Container</a>
            </li>
            <li class="submenu-item @if(Request::is('android/lcl/racking') || Request::is('android/lcl/racking/*')) active @endif">
                <a href="{{ url('/android/lcl/racking')}}">Racking</a>
            </li>
            <li class="submenu-item @if(Request::is('android/lcl/behandle') || Request::is('android/lcl/behandle/*')) active @endif">
                <a href="{{ url('/android/lcl/behandle')}}">Behandle</a>
            </li>
            <li class="submenu-item @if(Request::is('lcl/manifest') || Request::is('lcl/manifest/*')) active @endif">
                <a href="{{ url('/lcl/manifest')}}">Manifest</a>
            </li>
        </ul>
    </li>

    <!-- Photo -->
    <li class="sidebar-item has-sub @if(Request::is('photo/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-camera"></i>
            <span>Photo</span>
        </a>
        <ul class="submenu @if(Request::is('android/photo/*')) active @endif">
            <!-- Realisasi -->
            <li class="sidebar-item has-sub @if(Request::is('android/photo/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>LCL</span>
                </a>
                <ul class="submenu @if(Request::is('android/photo/*')) active @endif">
                    <li class="submenu-item @if(Request::is('android/photo/photoManifest') || Request::is('android/photo/photoManifest/*')) active @endif">
                        <a href="{{ url('/android/photo/photoManifest')}}">Manifest</a>
                    </li>
                    <li class="submenu-item @if(Request::is('android/photo/photoCont') || Request::is('android/photo/photoCont/*')) active @endif">
                        <a href="{{ url('/android/photo/photoCont')}}">Container</a>
                    </li>
                </ul>
            </li>

            <!-- Delivery -->
            <li class="sidebar-item has-sub @if(Request::is('photo/fcl/*')) active @endif">
                <a href="#" class='sidebar-link'>
                    <span>FCL</span>
                </a>
                <ul class="submenu @if(Request::is('photo/fcl/*')) active @endif">
                   
                </ul>
            </li>
        </ul>
    </li>
</ul> 