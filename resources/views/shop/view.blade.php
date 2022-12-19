@extends('layouts.main')
@section('title')
'City-Show | Shop-View '
@endsection
@section('content')
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
                                <span>{{$shop->Address?$shop->Address:'-'}}</span>
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
                                <span>
                                        <video src="{{$shop->video}}" alt="Video"> </video>
                                </span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">Image</label>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                    @if(!empty($shop->image))
                                      @foreach($shop->image as $image)
                                        <img src="{{$image}}" alt="shop image">
                                      @endforeach
                                    @endif
                                </span>
                            </div>
                    </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
@endsection