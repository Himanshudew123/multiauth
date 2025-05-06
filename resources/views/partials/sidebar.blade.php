<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!-- Sidebar Brand -->
  <div class="sidebar-brand">
    <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center">
      <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image opacity-75 shadow me-2" />
      <span class="brand-text fw-light">E-COM</span>
    </a>
  </div>

  <!-- Sidebar Menu -->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav flex-column sidebar-menu" data-lte-toggle="treeview" role="menu" data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Manage Users -->
        <li class="nav-item">
          <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Manage Users</p>
          </a>
        </li>

        <!-- Manage Products -->
        <li class="nav-item">
          <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.prducts.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-tags-fill"></i>
            <p>Manage Products</p>
          </a>
        </li>

        <!-- Manage Category -->
        <li class="nav-item">
          <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-tags-fill"></i>
            <p>Manage Categories</p>
          </a>
        </li>


        <!-- Tags -->
        <li class="nav-item">
          <a href="{{ route('admin.tags.index') }}" class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-tags-fill"></i>
            <p>Manage Tags</p>
          </a>
        </li>

        <!-- Logout -->
        <li class="nav-item ">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-start w-100 text-white">
              <i class="bi bi-box-arrow-right nav-icon"></i>
              <p>Log Out</p>
            </button>
          </form>
        </li>

      </ul>
    </nav>
  </div>
</aside>
