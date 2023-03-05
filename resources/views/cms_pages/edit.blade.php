@extends('layouts.main')
@section('title','Nailzy Saloon | Update')

@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/plugins/summernote/summernote-bs4.min.css') }}">

<style>
  .select2-selection__rendered {
    line-height: 31px !important;
  }

  .select2-container .select2-selection--single {
    height: 38px !important;
  }

  .select2-selection__arrow {
    height: 34px !important;
  }
</style>

@endpush
<div class="container-fluid">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>{{ __('CMS Page')}}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('CMS Page')}}</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <div class="row clearfix">
    <!-- start message area-->
    @include('include.message')
    <!-- end message area-->
    <!-- only those have manage_role permission will get access -->

    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3>{{ __('Update CMS Page')}}</h3>
        </div>
        <div class="card-body">
          <form class="forms-sample" method="POST" enctype="multipart/form-data" action="{{ route('cmsPages.update',['id'=>$data->id]) }}">
            @csrf
            <div class="row">
              <div class="form-group col-md-6">
                <label class="col-md-2 control-label">Page Type</label>
                <select id='page_type' name="type" class="form-control select2" style="width: 200px">
                  <option value="">Select type</option>
                  @foreach($static_keys as $val)
                  <option value="{{$val}}" {{$data->type == $val ?'selected="selected"':''}}>{{$val}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12  ">
                <div class="card card-outline card-info">
                  <div class="card-header">
                    <h3 class="card-title">
                      Description
                    </h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <textarea id="summernote" name="description">
                      {{$data->description}}
                                              </textarea>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-rounded">{{ __('Update')}}</button>
                <a href="{{route('cmsPages.index')}}">
                  <div class="btn btn-danger">Cancel</div>
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- push external js -->
@push('script')
<script>
  $(function () {
    $('#summernote').summernote()
  })
</script>
<script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('/plugins/summernote/summernote-bs4.min.js') }}"></script>
@endpush

@endsection
