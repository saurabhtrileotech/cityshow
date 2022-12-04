@extends('layouts.main')
@section('title','Products')
@section('content')
@push('style')
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
          <h1>{{ __('Products')}}</h1>
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
              <a href="{{route('products')}}"><i class="fa fa-list mr-2"></i>Product List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('product.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create Product</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('product.store')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Shopkeeper <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="shop_keeper_id" id="shop_keeper_id" class="form-control select2" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select ShopKeeper');">
                        <option value="">Select Shopkeeper</option>
                        @foreach ($shop_keepers as $shop_keeper)
                        <option {{ old('shop_keeper_id') == $shop_keeper->id ? "selected" : "" }} value="{{ $shop_keeper->id }}">{{ $shop_keeper->username }} </option>
                        @endforeach
                      </select>
                      @error('shop_keeper_id')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_name" class="col-sm-12 col-form-label">Product Name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="product_name" value="{{old('product_name')}}" name="product_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Category</label>
                    <div class="col-sm-8">
                       <select name="category_id" id="category_id" class="form-control select2">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                        <option {{ old('category_id') == $category->id ? "selected" : "" }} value="{{ $category->id }}">{{ $category->name}} </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="description" class="col-sm-12 col-form-label">Description</label>
                    <div class="col-sm-8">
                      <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Shop <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="shop_id" id="shop_id" class="form-control select2" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select Shop');">
                        <option value="">Select Shop</option>
                      </select>
                      @error('shop_id')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Product Price<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" id="product_price" value="{{old('product_price')}}" name="product_price" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter price');">
                      @error('product_price')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Sub Category</label>
                    <div class="col-sm-8">
                       <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                        <option value="">Select Sub Category</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="images" class="col-sm-12 col-form-label">Images<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <button type="submit" class="btn btn-info">Create</button>
                  <a href="{{route('shops')}}" class="btn btn-danger">Cancel</a>
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

  $('#shop_keeper_id').change(function(){
        var id = $(this).val();
        $.ajax({
          type : 'POST',
          headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url  : "{{route('get-shop-by-shopkeeper')}}",
          dataType : 'json',
          data : {shopkeeper_id : id },
          success : function(response){
              $("#shop_id").html(response.html);
          }
        });
  });

  $('#category_id').change(function(){
        var id = $(this).val();
        $.ajax({
          type : 'POST',
          headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url  : "{{route('get-subcat-by-category')}}",
          dataType : 'json',
          data : {category_id : id },
          success : function(response){
              $("#sub_category_id").html(response.html);
          }
        });
  });
</script>
@endpush