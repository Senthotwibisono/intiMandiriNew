<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item @if(Request::is('bc-p2/dashboard') || Request::is('/bc-p2/dashboard')) active @endif">
        <a href="/bc-p2/dashboard" class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub @if(Request::is('bc/lcl/*') || Request::is('lcl/*')) active @endif">
        <a href="#" class='sidebar-link'>
            <i class="fa-solid fa-window-restore"></i>
            <span>LCL</span>
        </a>
        <ul class="submenu @if(Request::is('bc-p2/lcl/*') || Request::is('/bc-p2/lcl/*')) active @endif">
            <!-- Realisasi -->
            <li class="submenu-item @if(Request::is('bc-p2/lcl/list-manifest') || Request::is('/bc-p2/lcl/list-manifest')) active @endif">
                <a href="{{ url('/bc-p2/lcl/list-manifest')}}">List Container</a>
            </li>
            <li class="submenu-item @if(Request::is('bc-p2/list-segelMerah') || Request::is('/bc-p2/list-segelMerah')) active @endif">
                <a href="{{ url('/bc-p2/list-segelMerah')}}">List Container</a>
            </li>
        </ul>
    </li>
 
</ul> 