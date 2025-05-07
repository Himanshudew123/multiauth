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

    <!-- Section: Latest Records in Tables -->
    <div class="row g-4 mt-4">

      <!-- Products -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest Products</h6>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary border">View All</a>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($latestProducts as $index => $product)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-muted text-center">No products found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Categories -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest Categories</h6>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-primary border">View All</a>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($latestCategories as $index => $category)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $category->name }}</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-muted text-center">No categories found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tags -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest Tags</h6>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-sm btn-primary border">View All</a>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($latestTags as $index => $tag)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tag->name }}</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-muted text-center">No tags found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Customers -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest Customers</h6>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-primary border">View All</a>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($latestCustomers as $index => $customer)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-muted text-center">No customers found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

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
