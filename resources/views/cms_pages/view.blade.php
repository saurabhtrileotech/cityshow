<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('/fontawesome-free/css/all.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('/dist/css/adminlte.min.css')}}">
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">


        <!-- Content Wrapper. Contains page content -->
        <div class="">


            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if($data->type == 'privacy_policy')
                            Privacy Policy
                            @endif
                            @if($data->type == 'terms_conditions')
                            Terms and conditions
                            @endif
                            @if($data->type == 'faq')
                            Frequently Asked Questions
                            @endif
                            @if($data->type == 'about')
                            About Us
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        {!!$data->description?$data->description:""!!}

                    </div>
                </div>
                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->


    </div>
    <!-- ./wrapper -->

    <style>
        .card-body * {
            white-space: initial !important;
            word-break: break-all;
        }

        .card-body .lead {
            max-width: 100%;
            flex: initial;
        }
    </style>
    <!-- jQuery -->
    <script src="{{asset('/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js') }} "></script>
    <!-- AdminLTE App -->
    <script src="{{asset('/dist/js/adminlte.min.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('/dist/js/demo.js')}}"></script>
</body>

</html>
