<nav class="main-header navbar navbar-expand navbar-white navbar-light py-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <dropdown-notification ref="dropdown_notification"></dropdown-notification>
     <!-- Authentication Links -->
                        
      <li class="nav-item dropdown border-left py-2">
          <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center px-0 " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
          <div class="user-panel d-flex">
        <div class="image pl-0">
          @if( Auth::user()->avatar != null && Auth::user()->avatar != "" )
          <img src="/storage/profile_pic/{{ Auth::user()->avatar }}" class="img-circle elevation-0" alt="User Image">
          @else
          <img src="/admin/img/dummy_profile.png" class="img-circle elevation-0" alt="User Image">
          @endif
        </div>
        <div class="info">
          <router-link to="/profile" class="d-inline-block text-truncate user-block" title="View Profile" >{{ Auth::user()->name }}</router-link>
        </div>
      </div> 
       <!-- {{ ucwords(Auth::user()->name) }} -->
          </a>

          <div class="dropdown-menu dropdown-menu-right border-0 rounded-0 py-0" aria-labelledby="navbarDropdown">
              <router-link class="dropdown-item" to="/profile">
                Profile
              </router-link>
              <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                  localStorage.removeItem('isModalVisible');
                                document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
              </form>
          </div>
      </li>           
    </ul>
      
  </nav>