<!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">City Show</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
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
              <a href="{{url('dashboard')}}" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
          </li>

          <li class="nav-item">
              <a href="{{url('categories')}}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Category Management</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{url('sub-categories')}}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Sub Category Management</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{url('shopkeepers')}}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>ShopKeepers</p>
              </a>
          </li>

          <li class="nav-item">
              <a href="{{url('shops')}}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Shops</p>
              </a>
          </li>
          
          <li class="nav-item">
              <a href="{{url('products')}}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Products</p>
              </a>
          </li>
          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>
                Forms
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages/forms/general.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>General Elements</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/advanced.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Advanced Elements</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/editors.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Editors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/validation.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Validation</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>