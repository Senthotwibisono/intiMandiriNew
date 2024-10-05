<div class="container">
  <ul>

    <li class="menu-item  ">
      <a href="/invoice/menu" class='menu-link'>
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <!-- <li class="menu-item has-sub">
      <a href="/invoice" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Billing System
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a class="submenu-link" href="/invoice">Invoice</a>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/invoice/delivery">Delivery</a>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/invoice/add/extend">Delivery Extend</a>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/export">Receiving</a>
            </li>
            <li class="submenu-item has-sub">
              <a type="button" class="submenu-link">Export Stuffing</a>
              <ul class="subsubmenu">
                <li class="subsubmenu-item">
                  <a href="/export/stuffing-in" class="subsubmenu-link">Export Stuffing Dalam</a>
                </li>
                <li class="subsubmenu-item">
                  <a href="/export/stuffing-out" class="subsubmenu-link">Export Stuffing Dalam</a>
                </li>
              </ul>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/invoice/customer">Customer</a>
            </li>
            <li class="submenu-item has-sub">
              <a type="button" class="submenu-link">Master Tarif</a>
              <ul class="subsubmenu">
                <li class="subsubmenu-item">
                  <a href="/invoice/mastertarif" class="subsubmenu-link">Master Tarif</a>
                </li>
                <li class="subsubmenu-item">
                  <a href="/spps/mastertarif" class="subsubmenu-link">Master Tarif SPPS</a>
                </li>
              </ul>
            </li>
            <li class="submenu-item">
              <a href="/invoice/payment" class="submenu-link">Metode Pembayaran</a>
            </li>
          </ul>
        </div>
      </div>
    </li> -->

    <li class="menu-item has-sub">
      <a type="button" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Delivery
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a href="{{ route('billinImportgMain')}}" class="submenu-link">Bongkaran Billing System</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('deliveryMenu')}}" class="submenu-link">Bongkaran Form</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('index-extend')}}" class="submenu-link">Bongkaran Form Extend</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('invoice-master-tarifImport')}}" class="submenu-link">Master Tarif</a>
            </li>
          </ul>
        </div>
      </div>
    </li>
    <li class="menu-item has-sub">
      <a type="button" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Receiving
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a href="{{ route('billingExportMain')}}" class="submenu-link">Muat Billing System</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('deliveryMenuExport')}}" class="submenu-link">Muat Form</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('invoice-master-tarifExport')}}" class="submenu-link">Master Tarif</a>
            </li>
          </ul>
        </div>
      </div>
    </li>

    <!--Plugging -->
    <li class="menu-item has-sub">
      <a type="button" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Plugging
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a href="{{ route('plugging-main')}}" class="submenu-link">Plugging Billing System</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('plugging-form-index')}}" class="submenu-link">Plugging Form</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('plugging-tarif-index')}}" class="submenu-link">Master Tarif</a>
            </li>
          </ul>
        </div>
      </div>
    </li>
    <!-- Steva -->
    <li class="menu-item has-sub">
      <a type="button" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Stevadooring
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a href="{{ route('index-stevadooring')}}" class="submenu-link">Stevadooring Billing System</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('index-stevadooring-listForm')}}" class="submenu-link">Stevadooring Form</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('index-stevadooring-Tarif')}}" class="submenu-link">Master Tarif</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('index-stevadooring-RBM')}}" class="submenu-link">Realisasi Bongkar Muat Kapal</a>
            </li>
            <!-- <li class="submenu-item">
              <a href="" class="submenu-link">Stuffing Dalam</a>
            </li>
            <li class="submenu-item">
              <a href="" class="submenu-link">Stuffing Luar</a>
            </li> -->
          </ul>
        </div>
      </div>
    </li>

    <!--Rental Repair -->
    <li class="menu-item has-sub">
      <a type="button" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        Others
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a href="{{ route('rental-repair-main')}}" class="submenu-link">Rental & Repair Billing System</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('rental-repair-form-index')}}" class="submenu-link">Rental & Repair Form</a>
            </li>
            <li class="submenu-item">
              <a href="{{ route('rental-repair-tarif-index')}}" class="submenu-link">Master Tarif</a>
            </li>
            <!-- <li class="submenu-item">
              <a href="" class="submenu-link">Stuffing Dalam</a>
            </li>
            <li class="submenu-item">
              <a href="" class="submenu-link">Stuffing Luar</a>
            </li> -->
          </ul>
        </div>
      </div>
    </li>
    <!-- end -->
    <li class="menu-item">
      <a href="{{ route('Customer')}}" class="menu-link">
        <i class="fa-solid fa-user">
        </i>
        Customer
      </a>
    </li>

    <!-- <li class="menu-item has-sub">
      <a href="/spps" class="menu-link">
        <i class="fa-solid fa-ship"></i>
        SPPS Billing System
      </a>
      <div class="submenu">
        <div class="submenu-group-wrapper">
          <ul class="submenu-group">
            <li class="submenu-item">
              <a class="submenu-link" href="/spps">Invoice</a>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/spps/delivery">Delivery</a>
            </li>
            <li class="submenu-item">
              <a class="submenu-link" href="/spps/customer">Customer</a>
            </li>
            <li class="submenu-item">
              <a href="/spps/mastertarif" class="submenu-link">Master Tarif</a>
            </li>
          </ul>
        </div>
      </div>
    </li> -->


    <!-- 
    <li class="menu-item  has-sub">
      <a href="#" class='menu-link'>
        <i class="bi bi-stack"></i>
        <span>E-Job Order</span>
      </a>
      <div class="submenu "> -->
    <!-- Wrap to submenu-group-wrapper if you want 3-level submenu. Otherwise remove it. -->
    <!-- <div class="submenu-group-wrapper">


          <ul class="submenu-group">

            <li class="submenu-item  ">
              <a href="component-alert.html" class='submenu-link'>Delivery</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-badge.html" class='submenu-link'>Delivery Extend</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-breadcrumb.html" class='submenu-link'>Delivery Batal Muat</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-button.html" class='submenu-link'>Delivery Transhipment</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-card.html" class='submenu-link'>Delivery Dry Port</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-carousel.html" class='submenu-link'>Receiving</a>


            </li>



            <li class="submenu-item  ">
              <a href="component-collapse.html" class='submenu-link'>Receiving Dry Port </a>


            </li>



            <li class="submenu-item  ">
              <a href="component-dropdown.html" class='submenu-link'>Behandle & Gerakan Ekstra</a>


            </li>

          </ul>
        </div>
      </div>
    </li> -->



    <li class="menu-item active has-sub">
      <a href="#" class='menu-link'>
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Documents</span>
      </a>
      <div class="submenu ">
        <!-- Wrap to submenu-group-wrapper if you want 3-level submenu. Otherwise remove it. -->
        <div class="submenu-group-wrapper">


          <ul class="submenu-group">

            <li class="submenu-item  ">
              <a href="{{ route('doMain')}}" class='submenu-link'>Do Online Check</a>


            </li>



            <li class="submenu-item  ">
              <a href="{{ route('coparnMain')}}" class='submenu-link'>Upload Coparn</a>


            </li>


<!-- 
            <li class="submenu-item  ">
              <a href="layout-vertical-navbar.html" class='submenu-link'>Coparn Online Check</a>


            </li> -->



            <li class="submenu-item  ">
              <a href="/bea/req-dok" class='submenu-link'>Request Clearance BC</a>


            </li>



            <!-- <li class="submenu-item active ">
              <a href="layout-horizontal.html" class='submenu-link'>Checking Part Of</a>


            </li> -->

            <li class="submenu-item active ">
              <a href="{{route('invoice-master-item')}}" class='submenu-link'>Master Item</a>
            </li>
            <li class="submenu-item active ">
              <a href="{{route('invoice-master-os')}}" class='submenu-link'>Order Service</a>
            </li>

          </ul>


        </div>
      </div>
    </li>



    <!-- <li class="menu-item  has-sub">
      <a href="#" class='menu-link'>
        <i class="bi bi-file-earmark-medical-fill"></i>
        <span>Monitoring</span>
      </a>
      <div class="submenu "> -->
    <!-- Wrap to submenu-group-wrapper if you want 3-level submenu. Otherwise remove it. -->
    <!-- <div class="submenu-group-wrapper">


          <ul class="submenu-group">

            <li class="submenu-item  has-sub">
              <a href="#" class='submenu-link'>Job & Invoice</a>
            </li>



            <li class="submenu-item  ">
              <a href="form-layout.html" class='submenu-link'>Monitoring Job Dry Port</a>


            </li> -->
  </ul>


</div>
</div>
</li>
</ul>
</div>