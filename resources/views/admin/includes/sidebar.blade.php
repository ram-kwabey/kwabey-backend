<aside class="main-sidebar sidebar-dark-primary elevation-4 bg-primary">
    <!-- Brand Logo -->
    <router-link to="/home" class="brand-link">
      <img src="{{ asset('/admin/img/kwabey_logo.jpeg') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">{{ config('app.name', 'Kwabey') }}</span>
    </router-link>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          @if( Auth::user()->avatar != null && Auth::user()->avatar != "" )
          <img src="/storage/profile_pic/{{ Auth::user()->avatar }}" class="img-circle elevation-2" alt="User Image">
          @else
          <img src="/admin/img/dummy_profile.png" class="img-circle elevation-2" alt="User Image">
          @endif
        </div>
        <div class="info">
          <router-link to="/profile" class="d-block" title="View Profile" >{{ Auth::user()->name }}</router-link>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          @can('list_dashboard')
          <li class="nav-item menu-open">
          <router-link class="nav-link" to="/home"><i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
              </router-link>
          </li>
          @endcan
          @can('list_users')
          <li class="nav-item menu-open">
            <router-link class="nav-link" to="/users"><i class="nav-icon fas fa-user-alt"></i>
              <p>
                Users
              </p>
              </router-link>
          </li>
          @endcan

          @can('product_mgt_settings')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Product Mgt
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
              @can('category_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/category-listing">
                  <i class="nav-icon fab fa-slideshare"></i>
                  <p>
                    Category
                  </p>
                </router-link>
              </li>
              @endcan
               @can('sub_category_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/sub_category-listing">
                  <i class="nav-icon fab fa-slideshare"></i>
                  <p>
                    Sub-Category
                  </p>
                </router-link>
              </li>
              @endcan
              @can('figure_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/figure-listing">
                  <i class="nav-icon fab fa-slideshare"></i>
                  <p>
                    Figure
                  </p>
                </router-link>
              </li>
              @endcan
              @can('size_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/size-listing">
                  <i class="nav-icon far fa-id-badge"></i>
                  <p>
                    Size
                  </p>
                </router-link>
              </li>
              @endcan
              @can('color_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/color-listing">
                  <i class="nav-icon fab fa-slideshare"></i>
                  <p>
                    Color
                  </p>
                </router-link>
              </li>
              @endcan
              @can('material_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/material-listing">
                  <i class="nav-icon fas fa-envelope-open-text"></i>
                  <p>
                    Material
                  </p>
                </router-link>
              </li>
              @endcan
              @can('fit_lists')
              <li class="nav-item">
                <router-link class="nav-link" to="/fit-listing">
                  <i class="nav-icon fas fa-envelope-open-text"></i>
                  <p>
                    Fit
                  </p>
                </router-link>
              </li>
              @endcan              
            </ul>
          </li>
          @endcan
          @can('list_settings')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Settings
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <router-link class="nav-link" to="/email-templates">
                  <i class="nav-icon fas fa-envelope-open-text"></i>
                  <p>
                    Email Templates
                  </p>
                </router-link>
              </li>
              <li class="nav-item">
                  <router-link class="nav-link" to="/roles"><i class="nav-icon fab fa-slideshare"></i>
                  <p>
                    Roles
                  </p>
                </router-link>
                </li>
              <li class="nav-item">
              <router-link class="nav-link" to="/permissions"><i class="nav-icon far fa-id-badge"></i>
                  <p>
                    Permissions
                  </p>
              </router-link>
              </li>
            </ul>
          </li>
          @endcan

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
