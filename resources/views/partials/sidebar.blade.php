<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="{{ route("admin.dashboard") }}" class="brand-link">
            <!--begin::Brand Image-->
          
            <img
              src="{{ asset('assets/img/AdminLTELogo.png') }}"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">E-COM</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false"
            >
       
              
             
            
              <li class="nav-item menu-open">
                <a href="{{ route("admin.dashboard") }}" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                  </p>
                </a>
             
              </li>




             
              <li class="nav-item">
                <a href="{{ route('admin.customers.index') }}" class="nav-link">
                  <i class="nav-icon bi bi-people-fill"></i>
                  
                  <p>Manage User</p>
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-box-seam-fill"></i>
                  <p>
                    Manage Products
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add_product.php" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Add Product</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="view_product.php" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>View Products</p>
                    </a>
                  </li>
                 
                </ul>
              </li>
              <li class="nav-item">
                <a href="category.php" class="nav-link">
                  <i class="nav-icon bi bi-box-seam-fill"></i>
                  <p>
                    Manage Category
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
               
              </li>

          
              
             
              
              <li class="nav-item">
                <a href="./logout.php" class="nav-link">
                  <i class="nav-icon bi bi-box-arrow-in-right"></i>
                  <p>
                    Logout
                   
                  </p>
                </a>
               
              </li>
              
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>