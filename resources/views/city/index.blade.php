@extends('layouts.main')
@section('title','Cities')
@section('content')
  @push('style')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
  @endpush

  <div class="container-fluid">
				<section class="content-header">
					<div class="container-fluid">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>{{ __('Cities')}}</h1>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-right">
									<li class="breadcrumb-item active">{{ __('Cities')}}</a></li>
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
                            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
                                <a href="{{route('city.create')}}"><i class="fa fa-list mr-2"></i>Create City</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <table id="city_table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name')}}</th>
                                    <th>{{ __('Postcode')}}</th>
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
    var cityListAjax = "{{route('city-list-ajax')}}";
   </script>
   <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
   <script src="{{asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!--server side roles table script-->
    <script src="{{ asset('dist/js/city.js') }}"></script>
@endpush