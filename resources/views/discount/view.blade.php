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
                    <li class="breadcrumb-item"><a href="{{url('discounts')}}">Discount</a></li>
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
                                <label for="role">Coupon Nname</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->coupon_name?$discount->coupon_name:'-'}}</span>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="role">Coupon Code</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$discount->coupon_code?$discount->coupon_code:'-'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Is is_price</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->is_price == 1?'Percentage':'Price'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Price</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->price?$discount->price:'-'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Shop Name</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->shop_keeper->username?$discount->shop_keeper->username:'-'}}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="col-sm-3">
                                <label for="role">Percentage</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->percentage?$discount->percentage:'-'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">start_date</label>
                            </div>
                            <div class="col-sm-3">
                            <span>{{$discount->start_date?date('m/d/y', strtotime($discount->start_date)):'-'}}</span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">end_date</label>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                <span>{{$discount->end_date?date('m-d-y', strtotime($discount->end_date)):'-'}}</span>
                                </span>
                            </div>
                            <div class="col-sm-3 pt-2">
                                <label for="role">Shop</label>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                    @if(!empty($shop_name))
                                      @foreach($shop_name as $name)
                                      <span>{{$name->shop_name}},</span>
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