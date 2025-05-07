<aside class="app-sidebar bg-dark text-white shadow-sm" data-bs-theme="dark">
  <!-- Sidebar Brand -->
  <div class="sidebar-brand p-3 border-bottom border-secondary">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
      <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image me-2" style="width: 35px; height: 35px;" />
      <span class="fs-5 fw-bold">E-COM</span>
    </a>
  </div>

  <!-- Sidebar Menu -->
  <div class="sidebar-wrapper p-2">
    <nav class="mt-2">
      <ul class="nav flex-column sidebar-menu" role="menu">

        <!-- Dashboard -->
        <li class="nav-item mb-2">
          <a href="{{ route('admin.dashboard') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : 'text-white' }}">
            <div><i class="bi bi-speedometer me-2"></i> Dashboard</div>
          </a>
        </li>

        <!-- Manage Users -->
        <li class="nav-item mb-2">
          <a href="{{ route('admin.customers.index') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.customers.*') ? 'active bg-primary text-white' : 'text-white' }}">
            <div><i class="bi bi-people-fill me-2"></i> Manage Users</div>
            <span class="badge bg-secondary">{{ $customerCount }}</span>
          </a>
        </li>

        <!-- Manage Products -->
        <li class="nav-item mb-2">
          <a href="{{ route('admin.products.index') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.products.*') ? 'active bg-primary text-white' : 'text-white' }}">
            <div><i class="bi bi-box-seam me-2"></i> Manage Products</div>
            <span class="badge bg-secondary">{{ $productCount }}</span>
          </a>
        </li>

        <!-- Manage Categories -->
        <li class="nav-item mb-2">
          <a href="{{ route('admin.categories.index') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.categories.*') ? 'active bg-primary text-white' : 'text-white' }}">
            <div><i class="bi bi-folder-fill me-2"></i> Manage Categories</div>
            <span class="badge bg-secondary">{{ $categoryCount }}</span>
          </a>
        </li>

        <!-- Manage Tags -->
        <li class="nav-item mb-2">
          <a href="{{ route('admin.tags.index') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.tags.*') ? 'active bg-primary text-white' : 'text-white' }}">
            <div><i class="bi bi-tag-fill me-2"></i> Manage Tags</div>
            <span class="badge bg-secondary">{{ $tagCount }}</span>
          </a>
        </li>

        <!-- Logout -->
        <li class="nav-item mt-4">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-start text-white w-100 d-flex align-items-center">
              <i class="bi bi-box-arrow-right me-2"></i> Log Out
            </button>
          </form>
        </li>

      </ul>
    </nav>
  </div>
</aside>

<style>
  .nav-link {
    padding: 10px 15px;
    border-radius: 0.375rem;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .nav-link:hover {
    background-color: #0d6efd;
    color: white !important;
  }

  .nav-link.active {
    font-weight: 600;
  }

  .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 1rem;
  }

  .brand-image {
    border-radius: 0.375rem;
    object-fit: cover;
  }
</style>
