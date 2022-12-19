@extends('layouts.main')
@section('title','Shop Keeper')
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
          <h1>{{ __('Shop Keeper')}}</h1>
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
              <a href="{{route('shopkeepers')}}"><i class="fa fa-list mr-2"></i>Shop List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('shopkeeper.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create ShopKeeper</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('shopkeeper.store')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="first_name" class="col-sm-12 col-form-label">First Name</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="first_name" value="{{old('first_name')}}" name="first_name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="email" class="col-sm-12 col-form-label">Email<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="email" value="{{old('email')}}" name="email" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter email address');">
                    </div>
                    @error('email')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row">
                    <label for="password" class="col-sm-12 col-form-label">Password<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="password" class="form-control" id="password" name="password" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter password');">
                    </div>
                    @error('password')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row">
                    <label for="address" class="col-sm-12 col-form-label">Address</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="address" value="{{old('address')}}" name="address">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                 <div class="form-group row">
                    <label for="shop_name" class="col-sm-12 col-form-label">Last Name</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="last_name" value="{{old('last_name')}}" name="last_name">
                    </div>
                 </div>
                 <div class="form-group row">
                    <label for="username" class="col-sm-12 col-form-label">Username<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="email" value="{{old('username')}}" name="username" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter Username');">
                    </div>
                    @error('username')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                 </div>
                 <div class="form-group row">
                    <label for="password_confirmation" class="col-sm-12 col-form-label">Confirm Password<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter confirm password');">
                    </div>
                    @error('password_confirmation')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                 </div>
                 <div class="form-group row">
                    <label for="profile_picture" class="col-sm-12 col-form-label">Profile picture</label>
                    <div class="col-sm-8">
                      <input type="file" class="form-control" id="profile_picture" name="profile_picture">
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