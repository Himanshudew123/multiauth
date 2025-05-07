@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<main class="app-main py-4">
  <div class="container-fluid">
    <!-- Section: Stats -->
    <div class="row g-4">
      @php
        $cards = [
          ['label' => 'Tags', 'count' => $tagCount, 'icon' => 'bi-tags', 'route' => route('admin.tags.index'), 'bg' => 'primary'],
          ['label' => 'Categories', 'count' => $categoryCount, 'icon' => 'bi-folder-fill', 'route' => route('admin.categories.index'), 'bg' => 'danger'],
          ['label' => 'Products', 'count' => $productCount, 'icon' => 'bi-cart-fill', 'route' => route('admin.products.index'), 'bg' => 'success'],
          ['label' => 'Customers', 'count' => $customerCount, 'icon' => 'bi-people-fill', 'route' => route('admin.customers.index'), 'bg' => 'warning'],
        ];
      @endphp

      @foreach ($cards as $card)
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ $card['route'] }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 hover-shadow transition">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-wrapper bg-{{ $card['bg'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                  <i class="bi {{ $card['icon'] }} fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-1 text-dark">{{ $card['label'] }}</h6>
                  <h5 class="mb-0 fw-bold text-{{ $card['bg'] }}">{{ $card['count'] }}</h5>
                </div>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

    <!-- Section: Latest Records -->
    <div class="row g-4 mt-4">
      <div class="col-12">
        <h4 class="mb-3 fw-bold text-secondary">Latest Records</h4>
      </div>

      @php
        $latestItems = [
          ['title' => 'Latest Product', 'item' => $latestProduct, 'fields' => ['name', 'price'], 'bg' => 'light'],
          ['title' => 'Latest Category', 'item' => $latestCategory, 'fields' => ['name'], 'bg' => 'light'],
          ['title' => 'Latest Tag', 'item' => $latestTag, 'fields' => ['name'], 'bg' => 'light'],
          ['title' => 'Latest Customer', 'item' => $latestCustomer, 'fields' => ['name', 'email'], 'bg' => 'light'],
        ];
      @endphp

      @foreach ($latestItems as $latest)
        <div class="col-12 col-sm-6 col-md-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0">
              <h6 class="card-title fw-bold text-dark">{{ $latest['title'] }}</h6>
            </div>
            <div class="card-body">
              @if ($latest['item'])
                @foreach ($latest['fields'] as $field)
                  <p class="mb-1"><strong class="text-muted">{{ ucfirst($field) }}:</strong> {{ $latest['item']->$field }}</p>
                @endforeach
              @else
                <p class="text-muted">No {{ strtolower($latest['title']) }} found.</p>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</main>

<style>
  .hover-shadow:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  }
  .transition {
    transition: all 0.3s ease-in-out;
  }
</style>
@endsection
