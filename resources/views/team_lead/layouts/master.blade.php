<!DOCTYPE html>
<html lang="en">
  <!-- header -->
  @include('team_lead.includes.header')
  <!-- /.header -->
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  @include('team_lead.includes.navbar')
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  @include('team_lead.includes.sidebar')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('team_lead.includes.breadcrumb')
    <!-- /.content-header -->
    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @include('team_lead.includes.footer')
</body>
</html>
