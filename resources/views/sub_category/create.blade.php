@extends('layouts.main')
@section('title','Sub Category')
@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/intlTelInput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
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
          <h1>{{ __('SubCategory')}}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('sub-categories')}}">SubCategory</a></li>
            <li class="breadcrumb-item active">{{ __('SubCategory')}}</a></li>
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
              <a href="{{route('sub-categories')}}"><i class="fa fa-list mr-2"></i>SubCategory List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('sub-category.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create SubCategory</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('sub-category.store')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                <div class="form-group row">
                    <label for="category_id" class="col-sm-12 col-form-label">Category <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="category_id" class="form-control select2" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select Category');">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                        <option {{ old('category_id') == $category->id ? "selected" : "" }} value="{{ $category->id }}">{{ $category->name }} </option>
                        @endforeach
                      </select>
                      @error('category_id')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-12 col-form-label">Name <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="name" name="name" value="{{old('name')}}" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Subcategory name');">
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
@push('script')
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
  $('.select2').select2();
</script>
@endpush