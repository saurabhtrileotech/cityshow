@extends('layouts.main')
@section('title','Discount')
@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
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
          <h1>{{ __('Discount')}}</h1>
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
              <a href="{{route('discounts')}}"><i class="fa fa-list mr-2"></i>Discount List</a>
            </li>
            <li class="nav-item active" id="create_nav" style="padding-left: 10px;">
              <a href="{{route('discount.create')}}" class="active show"><i class="fa fa-list mr-2"></i>Create Discount</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form action="{{route('discount.update',['id'=>$discount->id])}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Shopkeeper <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="shop_keeper_id" id="shop_keeper_id" class="form-control select2" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select ShopKeeper');">
                        <option></option>  
                        @foreach ($shop_keepers as $shop_keeper)
                        <option  value="{{ $shop_keeper->id }}">{{ $shop_keeper->username }} </option>
                        @endforeach
                      </select>
                      @error('shop_keeper_id')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="coupon_name" class="col-sm-12 col-form-label">Coupon name<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="coupon_name" value="{{$discount->coupon_name}}" name="coupon_name" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter coupon name');">
                    </div>
                    @error('coupon_name')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row">
                    <label for="product_price" class="col-sm-12 col-form-label">Is Price?</label>
                    <div class="col-sm-8">
                      <input type="radio" value="0" {{$discount->is_price == 0?'checked':''}} name="is_price" checked> Yes
                      <input type="radio" value="1" {{$discount->is_price == 1?'checked':''}} name="is_price"> No
                    </div>
                  </div>
                  <div class="form-group row">
                        <label class="col-sm-12 col-form-label">Start Date <span class="text-danger">*</span> </label>
                        <div class="col-sm-8" data-target-input="nearest">
                            <div class="input-group date" id="startdate" data-target-input="nearest">
                                <input name="startdate" oninput="setCustomValidity('');" required value="{{ date('mm/dd/yyyy', strtotime($discount->start_date)) }}" oninvalid="this.setCustomValidity('Please choose start date');" id="startdate" type="text" class="form-control datetimepicker-input" data-target="#startdate" />
                                <div class="input-group-append" data-target="#startdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="" id="startdate">
                                @error('startdate')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                   </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group row">
                    <label for="shop_keeper" class="col-sm-12 col-form-label">Shop <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <select name="shop_id[]" id="shop_id" class="form-control select2" multiple="multiple" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please select Shop');">
                        <option></option>  
                      </select>
                      @error('shop_id')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="coupon_code" class="col-sm-12 col-form-label">Coupon code<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="coupon_code" value="{{$discount->coupon_code}}" name="coupon_code" oninput="setCustomValidity('');" required oninvalid="this.setCustomValidity('Please enter coupon code');">
                    </div>
                    @error('coupon_code')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row price">
                    <label for="price" class="col-sm-12 col-form-label">Price<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="price" value="{{$discount->price}}" name="price">
                    </div>
                    @error('price')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row percentage" style="display:none">
                    <label for="percentage" class="col-sm-12 col-form-label">Percentage<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="percentage" value="{{$discount->percentage}}" name="percentage">
                    </div>
                    @error('percentage')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group row">
                        <label class="col-sm-12 col-form-label">End Date <span class="text-danger">*</span> </label>
                        <div class="col-sm-8" data-target-input="nearest">
                            <div class="input-group date" id="enddate" data-target-input="nearest">
                                <input name="enddate" oninput="setCustomValidity('');" required value="{{ date('mm/dd/yyyy', strtotime($discount->end_date)) }}" oninvalid="this.setCustomValidity('Please choose end date');" id="enddate" type="text" class="form-control datetimepicker-input" data-target="#enddate" />
                                <div class="input-group-append" data-target="#enddate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="" id="enddate">
                                @error('enddate')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
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
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
  $('.select2').select2({
    placeholder : 'Please select'
  });

  $( document ).ready(function() {
    $("#shop_keeper_id").val("{{$discount->shop_keeper_id}}").trigger('change');
   
    setTimeout(function() { 
    $("#shop_id").val(<?php echo json_encode($shopDiscount); ?>).change();
    }, 1000);

});
  var date = new Date();
    date.setDate(date.getDate());

    $('#startdate').datetimepicker({
        format: 'L',
        minDate: date,
        defaultDate: date,
    });
    $('#enddate').datetimepicker({
        format: 'L',
        minDate: date,
        defaultDate: date,
    });

    $("#startdate").on("change.datetimepicker", function(e) {
        var start_date = $('#startdate').data('date');
        var end_date = $('#enddate').data('date');

        $('#enddate').datetimepicker('minDate', e.date);
    });

    $("#enddate").on("change.datetimepicker", function(e) {
        // Get camp manager which are not between this time period
        var start_date = $('#startdate').data('date');
        var end_date = $('#enddate').data('date');

        // Make start date below than end date
        $('#startdate').datetimepicker('maxDate', e.date);
    });

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
              $("#shop_id").val('');
          }
        });
    });

  $("input[type='radio'][name='is_price']").click(function() {
    var value = $(this).val();
    if(value == 0){
       $('.price').show();
       $('.percentage').hide();       
    }else{
       $('.price').hide();
       $('.percentage').show(); 
    }
   });
</script>
@endpush