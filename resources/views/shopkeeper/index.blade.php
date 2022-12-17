@extends('layouts.main')
@section('title','Shopkeeper')
@section('content')
  @push('style')
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  @endpush

  <div class="container-fluid">
				<section class="content-header">
					<div class="container-fluid">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>{{ __('Shopkeeper')}}</h1>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-right">
									<li class="breadcrumb-item active">{{ __('Shopkeeper')}}</a></li>
								</ol>
							</div>
						</div>
					</div>
    		 </section>
      <div class="row">
          @include('include.message')
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item" style="padding-left: 10px;">
                                <a href="{{route('shopkeepers')}}"><i class="fa fa-list mr-2"></i>Shopkeeper</a>
                            </li>
                            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
                                <a href="#"><i class="fa fa-list mr-2"></i>Create ShopKeeper</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <table id="shopkeeper_table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Username')}}</th>
                                    <th>{{ __('Email')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
       </div>
    </div>
@endsection
 @push('script')
   <script>
    var shopkeeperListAjax = "{{route('shopkeeper-list-ajax')}}";
   </script>
   <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!--server side roles table script-->
    <script src="{{ asset('dist/js/shopkeeper.js') }}"></script>
@endpush