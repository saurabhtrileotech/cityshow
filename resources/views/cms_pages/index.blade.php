@extends('layouts.main')
@section('title','Nailzy Saloon | CMS Page')
@section('content')
@push('style')
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
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
  <div class="row">
        @include('include.message')
    <div class="col-md-12">
      <div class="card p-3">
        <div class="card-body">
    <a href="{{route('cmsPages.create')}}"><button type="button" class="btn btn-primary mb-2">Create Page</button></a>

          <table id="cms_pages_table" class="table">
            <thead>
              <tr>
                <th>{{ __('Name')}}</th>
                <th>{{ __('Action')}}</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@push('script')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    
<script src="{{asset('/dist/js/pages/cms_pages.js')}}"></script>
@endpush



@endsection
