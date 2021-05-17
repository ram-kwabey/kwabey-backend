<!DOCTYPE html>
<html lang="en">
  <!-- header -->
  @include('admin.includes.header')
  <!-- /.header -->
<body class="hold-transition sidebar-mini layout-fixed" style="background: #e9ecef;">
<div id="app">
<div class="wrapper">
  <!-- Navbar -->
  @include('admin.includes.navbar')
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  @include('admin.includes.sidebar')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('admin.includes.breadcrumb')
    <!-- /.content-header -->
    
    <!-- Main content -->
    <div class="container-fluid">
      @yield('content')
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @include('admin.includes.footer')
</div>
<script type="text/javascript">
  window.vueAuth = @json([
       "user" => Auth::user(),
       "roles" => Auth::user()->getRoleNames(),
       "permissions" => Auth::user()->getAllPermissions()->pluck('name')
    ]);

</script>
<script src="{{ asset('js/app.js') }}" ></script>

</body>
</html>
