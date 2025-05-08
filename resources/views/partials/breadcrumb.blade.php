<div class="container-fluid">
  <nav aria-label="breadcrumb" class="mt-3 mb-3">
    <ol class="breadcrumb bg-light">

      {{-- Dashboard Link --}}
      <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
          <i class="bi bi-house-door"></i> Dashboard
        </a>
      </li>

      @php
        $segments = Request::segments();
        $breadcrumbRoutes = [
          'customers' => ['label' => 'Manage Customers', 'route' => route('admin.customers.index')],
          'categories' => ['label' => 'Manage Categories', 'route' => route('admin.categories.index')],
          'tags'       => ['label' => 'Manage Tags', 'route' => route('admin.tags.index')],
          'products'   => ['label' => 'Manage Products', 'route' => route('admin.products.index')],
        ];
      @endphp

      {{-- Loop through URL segments and generate breadcrumbs --}}
      @foreach($segments as $key => $segment)
        @if(array_key_exists($segment, $breadcrumbRoutes))
          <li class="breadcrumb-item {{ $key === count($segments) - 1 ? 'active' : '' }}"
              @if($key === count($segments) - 1) aria-current="page" @endif>
            @if($key !== count($segments) - 1)
              <a href="{{ $breadcrumbRoutes[$segment]['route'] }}" class="text-decoration-none">
                {{ $breadcrumbRoutes[$segment]['label'] }}
              </a>
            @else
              {{ $breadcrumbRoutes[$segment]['label'] }}
            @endif
          </li>
        @endif
      @endforeach

    </ol>
  </nav>
</div>
