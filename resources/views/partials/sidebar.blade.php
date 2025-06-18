        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/dashboard') }}">
                <div class="sidebar-brand-icon ">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo" style="width: 40px; height: auto;">
                </div>
                <div class="sidebar-brand-text mx-2">Sempulur Poultry Shop</div>
            </a>
            

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Manajemen Stok
            </div>

            <li class="nav-item {{ request()->is('products') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/products') }}">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Produk</span>
                </a>
            </li>
      
            <li class="nav-item {{ request()->is('in-stocks-index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/in-stocks-index') }}">
                    <i class="fas fa-fw fa-inbox"></i>
                    <span>Stok Masuk</span></a>
            </li>

            <li class="nav-item {{ request()->is('out-stocks-index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/out-stocks-index') }}">
                    <i class="fas fa-fw fa-folder-minus"></i>
                    <span>Stok Keluar</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            @superadminadmin
            <!-- Heading -->
            <div class="sidebar-heading">
                Administrasi
            </div>

            <li class="nav-item  {{ request()->is('categories') ? 'active' : '' }}">
                <a class="nav-link"href="{{ url('/categories') }}">
                    <i class="fas fa-fw fa-th-large"></i>
                    <span>Kategori</span></a>
            </li>

             <li class="nav-item  {{ request()->is('manufacturers') ? 'active' : '' }}">
                <a class="nav-link"href="{{ url('/manufacturers') }}">
                    <i class="fas fa-fw fa-truck"></i>
                    <span>Distributor</span></a>
            </li>
            @superadmin
            <li class="nav-item  {{ request()->is('users') ? 'active' : '' }}">
                <a class="nav-link"href="{{ url('/users') }}">
                    <i class="fas fa-solid fa-users"></i>
                    <span>Manajemen Akun</span></a>
            </li>
            @endsuperadmin
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            @endsuperadminadmin
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->