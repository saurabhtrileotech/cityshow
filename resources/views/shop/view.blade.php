@extends('layouts.main')
@section('title')
'City-Show | Shop-View '
@endsection
@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-slider/css/bootstrap-slider.min.css') }}">
@endpush
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">View</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6 ">
                            <div class="col-sm-3">
                                <label for="role">Shop Name</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$shop->shop_name?$shop->shop_name:'-'}}</span>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="role">Status</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$shop->status == 1?'Active':'Inactive'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Is Verified By Admin</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$shop->status == 1?'Yes':'No'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Banner</label>
                            </div>
                            <div class="col-sm-3">
                            <img src="{{$shop->banner}}" alt="Banner image">
                            </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="col-sm-3">
                                <label for="role">Address</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$shop->address?$shop->address:'-'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Notes</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$shop->notes?$shop->notes:'-'}}</span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">Video</label>
                            </div>

                            <div class="col-sm-3">
                                <video style="height: 300px;
    width: 222px;" controls>
  <source src="{{$shop->video}}" type="video/mp4">
</video>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">images</label>
                            </div>

                           <div class="col-sm-3">
                            @if(!empty($shop->images))
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($shop->images as $image)
                                        <div class="carousel-item active">
                                            <img src="{{$image}}" class="d-block w-100">
                                        </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                    </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@push('script')
<script src="{{ asset('plugins/bootstrap-slider/bootstrap-slider.min.js') }}"></script>
@endpush