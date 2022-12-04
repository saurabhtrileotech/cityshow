@extends('layouts.main')
@section('title','Shops')
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
          <h1>{{ __('Shops')}}</h1>
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
              <a href="{{route('shops')}}"><i class="fa fa-list mr-2"></i>Shop List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('shop.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create Shop</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('shop.store')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Shop Keeper <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="shop_keeper_id" class="form-control select2" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select ShopKeeper');">
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
                    <label for="shop_name" class="col-sm-12 col-form-label">Shop Name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="shop_name" value="{{old('shop_name')}}" name="shop_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="address" class="col-sm-12 col-form-label">Address<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="address" value="{{old('address')}}" name="address">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="banner_image" class="col-sm-12 col-form-label">Banner Imnage <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control @error('banner_image') is-invalid @enderror" id="password" name="banner_image" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Banner Image');">
                      @error('banner_image')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="banner_video" class="col-sm-12 col-form-label">Video</label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="banner_video" name="banner_video">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="images" class="col-sm-12 col-form-label">Images</label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="notes" class="col-sm-12 col-form-label">Notes</label>
                    <div class="col-sm-8">
                      <textarea class="form-control" id="notes" name="notes">{{old('notes')}}</textarea>
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
</script>
<!-- <script src="http://maps.googleapis.com/maps/api/js?libraries=places" type="text/javascript"></script>

<script type="text/javascript">
    function initialize() {
        var input = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('city2').value = place.name;
            document.getElementById('cityLat').value = place.geometry.location.lat();
            document.getElementById('cityLng').value = place.geometry.location.lng();
            //alert("This function is working!");
            //alert(place.name);
           // alert(place.address_components[0].long_name);

        });
    }
    google.maps.event.addDomListener(window, 'load', initialize); 
</script> -->
@endpush