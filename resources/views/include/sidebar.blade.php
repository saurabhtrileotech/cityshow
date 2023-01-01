<!-- Brand Logo -->
    <a href="{{url('dashboard')}}" class="brand-link">
      <img src="{{asset('/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">City Show</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
              <a href="{{url('dashboard')}}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
          </li>

          <li class="nav-item">
              <a href="{{url('categories')}}" class="nav-link {{ Request::is('categories','category/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-th-large"></i>
                <p>Category</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{url('sub-categories')}}" class="nav-link {{ Request::is('sub-categories','sub-category/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-th"></i>
                <p>Sub Category</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{url('shopkeepers')}}" class="nav-link {{ Request::is('shopkeepers','shopkeeper/*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tie"></i>
                <p>ShopKeepers</p>
              </a>
          </li>

          <li class="nav-item">
              <a href="{{url('shops')}}" class="nav-link {{ Request::is('shops','shop/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-store"></i>
                <p>Shops</p>
              </a>
          </li>
          
          <li class="nav-item">
              <a href="{{url('products')}}" class="nav-link {{ Request::is('products','product/*') ? 'active' : '' }}">
              <i class="nav-icon fab fa-product-hunt"></i>
                <p>Products</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{url('discounts')}}" class="nav-link {{ Request::is('discounts','discount/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-percent"></i>
                <p>Discounts</p>
              </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>