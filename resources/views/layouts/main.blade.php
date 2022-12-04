<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title')</title>
  @include('include.head')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
   @include('include.header')
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    @include('include.sidebar')
  </aside>

  <div class="content-wrapper">
     @yield('content')
  </div>
  @include('include.footer')
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
 @include('include.script')
</body>
</html>
