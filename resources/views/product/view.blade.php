@extends("layouts.main")
@section("title")
"City-Show | Product-View "
@endsection
@section("content")
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
                    <li class="breadcrumb-item"><a href="{{url("dashboard")}}">Dashboard</a></li>
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
                                <label for="role">Shop Keeper</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{isset($product->Shopkeeper)?$product->Shopkeeper->username:"-"}}</span>
                            </div>

                            <div class="col-sm-3">
                                <label for="role">Category</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{isset($product->Category)?$product->Category->name:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Product Name</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->name ?$product->name:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Model Name</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->model_name?$product->model_name:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Product Selling Price</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->selling_price?$product->selling_price:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Size</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->size?$product->size:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Warranty</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->warranty== 0?"No":"Yes"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Material</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->material?$product->material:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Gold</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->is_gold == 0?"No":"Yes"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Ram</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->ram?$product->ram:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Connectivity</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->connectivity?$product->connectivity:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Description</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->description?$product->description:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Images</label>
                            </div>
                            <div class="col-sm-3">
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($product->Product_Image as $image)
                                        <div class="carousel-item active">
                                            <img src="{{$image['image']}}" class="d-block w-100">
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
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-3">
                                <label for="role">Shop</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->shop_name?$product->shop_name:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Sub Category</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{isset($product->Sub_Category)?$product->Sub_Category->name:"-"}}</span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">Brand Name</label>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                    <span>{{$product->brand_name?$product->brand_name:"-"}}</span>
                                </span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">Product Original Price</label>
                            </div>

                            <div class="col-sm-3">
                                <span>{{$product->price?$product->price:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Gender</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->gender?$product->gender:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Color</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->color?$product->color:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Weight</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->wight?$product->wight:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">OS</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->device_os?$product->device_os:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Storage</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->storage?$product->storage:"-"}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Key Features</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$product->key_featurees?$product->key_featurees:"-"}}</span>
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
