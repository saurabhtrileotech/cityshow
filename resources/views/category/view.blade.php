@extends('layouts.main')
@section('title')
'City-Show | Category-View '
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
                                <label for="role">Category</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$category->name?$category->name:'-'}}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-3 pt-2">
                                <label for="role">Image</label>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                    @if(!empty($category->image))
                                      <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                    @foreach($category->image as $image)
                                        <div class="carousel-item active">
                                        <img src="{{$image}}" class="d-block w-100" alt="Category image">

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