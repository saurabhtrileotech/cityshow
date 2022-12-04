@extends('layouts.main')
@section('title','Camp Manager')
@section('content')
  @push('style')
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/intlTelInput.css') }}">
  @endpush
  <style>
.full_w .iti.iti--allow-dropdown {
    width: 100%;
}
  </style>

  <div class="container-fluid">
				<section class="content-header">
					<div class="container-fluid">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>{{ __('Shopkeeper')}}</h1>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-right">
								  <li class="breadcrumb-item"><a href="{{url('shopkeepers')}}">Manage Shopkeeper</a></li>
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
                                <a href="{{route('shopkeepers')}}"><i class="fa fa-list mr-2"></i>Shopkeeper List</a>
                            </li>
                            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
                                <a href="{{route('shopkeeper.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create Shopkeeper</a>
                            </li>
                        </ul>
                    </div>
                  <div class="card-body">
                      <form action="{{route('shopkeeper.update',['id' => $shopkeeper->id])}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                          <div class="row">
                              <div class="col-md-6">
                                <div class="form-group row">
                                  <label for="username" class="col-sm-12 col-form-label">Username <span class="text-danger">*</span></label>
                                  <div class="col-sm-8">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{$shopkeeper->username}}" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Username');">
                                    @error('username')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label for="email" class="col-sm-12 col-form-label">Email <span class="text-danger">*</span></label>
                                  <div class="col-sm-8">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{$shopkeeper->email}}" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Email address');">
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                  </div>
                                </div>
                            </div>    
                                
                              <div class="col-md-6">
                                <div class="form-group row">
                                  <label for="email" class="col-sm-12 col-form-label">Status</label>
                                  <div class="col-sm-8">
                                    <select class="form-control" name="status">
                                        <option value="1" {{($shopkeeper->status == '1') ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{($shopkeeper->status == '0') ? 'selected' : ''}}>In Active</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <button type="submit" class="btn btn-info">Update</button>
                                  <a  href="{{route('shopkeepers')}}" class="btn btn-danger">Cancel</a>
                              </div>
                              <div class="col-md-6">
                                 
                              </div>
                          </div>
                        </div>
                      </form>
                  </div>
                </div>
            </div>
       </div>
    </div>
@endsection
