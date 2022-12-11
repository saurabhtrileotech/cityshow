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
                    <label for="product_name" class="col-sm-12 col-form-label">Product Name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="product_name" value="{{old('product_name')}}" name="product_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="model_name" class="col-sm-12 col-form-label">Model Name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="model_name" value="{{old('model_name')}}" name="model_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_selling_price" class="col-sm-12 col-form-label">Product Selling Price<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" id="product_selling_price" value="{{old('product_selling_price')}}" name="product_selling_price">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Size</label>
                    <div class="col-sm-8">
                      <input type="checkbox" value="XXL" name="size[]">  XXL
                      <input type="checkbox"  value="XL" name="size[]">  XL
                      <input type="checkbox"  value="L" name="size[]">  L
                      <input type="checkbox"  value="S" name="size[]">  S
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Warranty?</label>
                    <div class="col-sm-8">
                      <input type="checkbox" value="1" name="warranty">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="material" class="col-sm-12 col-form-label">Material</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="material" value="{{old('material')}}" name="material">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Gold?</label>
                    <div class="col-sm-8">
                      <input type="radio" value="1" name="is_gold"> Yes
                      <input type="radio" value="0" name="is_gold"> No
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="ram" class="col-sm-12 col-form-label">Ram</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="ram" value="{{old('ram')}}" name="ram">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="ram" class="col-sm-12 col-form-label">Connectivity</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="connectivity" value="{{old('connectivity')}}" name="connectivity">
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
                      <select name="shop_id[]" id="shop_id" class="form-control select2" multiple="multiple" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select Shop');">
                        <option value="">Select Shop</option>
                      </select>
                      @error('shop_id')
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
                    <label for="brand_name" class="col-sm-12 col-form-label">Brand Name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="brand_name" value="{{old('brand_name')}}" name="brand_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Product Original Price<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" id="product_price" value="{{old('product_price')}}" name="product_price" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter price');">
                      @error('product_price')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Gender</label>
                    <div class="col-sm-8">
                      <input type="radio" value="Male" name="gender"> Male
                      <input type="radio"  value="Female" name="gender"> Female
                      <input type="radio"  value="Child" name="gender"> Child
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="color" class="col-sm-12 col-form-label">Color</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="color" value="{{old('color')}}" name="color">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="weight" class="col-sm-12 col-form-label">Weight</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="weight" value="{{old('weight')}}" name="weight">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="device_os" class="col-sm-12 col-form-label">OS</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="device_os" value="{{old('device_os')}}" name="device_os">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="storage" class="col-sm-12 col-form-label">Storage</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="storage" value="{{old('storage')}}" name="storage">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="images" class="col-sm-12 col-form-label">Images<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-6">
                    <label for="storage" class="col-sm-12 col-form-label">Key Features</label>
                    </div>
                    <div class="col-md-6">
                      <a class="add_key_feature"><i class="fa fa-plus"></i></a>
                    </div>
                    <div class="key_features">
                      <div class="form-group row">
                        <div class="col-sm">
                          <input type="text" class="form-control"  name="key_feature[]" placeholder="Enter key Features">
                        </div>
                      </div>
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
  $('.add_key_feature').click(function(e){
      var html = "";
      html += '<div class="form-group row">';
      html += '<div class="col-md-10">'
      html += '<div class="col-sm">';
      html += '<input type="text" class="form-control" name="key_feature[]" placeholder="Enter key Features">';
      html += '</div></div>';
      html += '<div class="col-md-2">';
      html += '<a class="remove_key_feature"><i class="fa fa-minus"></i></a>';
      html += '</div></div>';
      $('.key_features').append(html);
  });
  $(document).on("click", ".remove_key_feature", function(e) {
      $(this).closest('.form-group').remove();
  });
</script>
@endpush