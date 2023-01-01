@extends('layouts.main')
@section('title')
'City-Show | Shopkeeper-View '
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
                                <label for="role">User Name</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$user->username?$user->username:'-'}}</span>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="role">Status</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$user->status == 'active'?'Active':'Inactive'}}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="col-sm-3">
                                <label for="role">Email</label>
                            </div>
                            <div class="col-sm-3">
                                <span>{{$user->email?$user->email:'-'}}</span>
                            </div>
                            <div class="col-sm-3">
                                <label for="role">Profile Image</label>
                            </div>
                            <div class="col-sm-3">
                            <img src="{{$user->profile_pic}}" alt="Profile Image">
                            </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection