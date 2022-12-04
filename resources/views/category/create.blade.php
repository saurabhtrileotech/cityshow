@extends('layouts.main')
@section('title','Category')
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
          <h1>{{ __('Category')}}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('categories')}}">Categories</a></li>
            <li class="breadcrumb-item active">{{ __('Category')}}</a></li>
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
              <a href="{{route('categories')}}"><i class="fa fa-list mr-2"></i>Category List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('category.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create Category</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('category.store')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="first_name" class="col-sm-12 col-form-label">Name <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="name" name="name" value="{{old('name')}}" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Category name');">
                      @error('name')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="images" class="col-sm-12 col-form-label">Images</label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <button type="submit" class="btn btn-info">Create</button>
                  <a href="{{route('categories')}}" class="btn btn-danger">Cancel</a>
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