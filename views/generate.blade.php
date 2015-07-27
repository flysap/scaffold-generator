@extends('themes::layouts.default')

@section('content')
    <section class="content-header">
        <h1>
            General Form Elements
            <small>Preview</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Forms</a></li>
            <li class="active">General Elements</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">


        <div class="row">
            <!-- left column -->
            <div class="col-md-12">


                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{_('Generate modules')}}e</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" method="post" action="">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">

                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-2">
                                    <label>{{_('Vendor')}}</label>
                                    <input type="text" name="vendor" class="form-control" placeholder="Vendor">
                                </div>
                                <div class="col-xs-3">
                                    <label>{{_('Name')}}</label>
                                    <input type="text" name="name" class="form-control" placeholder="Name">
                                </div>

                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-2">
                                    <label>{{_('Table name')}}</label>
                                    <input type="text" name="tables[0][name]" class="form-control" placeholder="User">
                                </div>
                                <div class="col-xs-7">
                                    <label>{{_('Fields')}}</label>
                                    <input type="text" name="tables[0][fields]" class="form-control" placeholder="id:int(11)|unsigned|nullable|index, phone_id:int(11)|unsigned">
                                </div>

                                <div class="col-xs-9">
                                    <label>{{_('Relations')}}</label>
                                    <input type="text" name="tables[0][relations]" class="form-control" placeholder="phone_id:id|phones|cascade|cascade">
                                </div>

                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{_('Generate')}}</button>
                        </div>

                    </form>
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
@endsection