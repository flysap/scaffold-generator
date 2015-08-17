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

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab">Builder</a></li>

                        @if($isGenerated)
                            <li><a href="#tab_2" data-toggle="tab" class="editor">Editor</a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">

                            <!-- form start -->
                            <form role="form" method="post" action="">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">

                                <div class="table">
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
                                                <input type="text" name="tables[0][name]" class="form-control"
                                                       placeholder="User">
                                            </div>
                                            <div class="col-xs-7">
                                                <label>{{_('Fields')}}</label>
                                                <input type="text" name="tables[0][fields]" class="form-control"
                                                       placeholder="id:int(11)|unsigned|nullable|index, phone_id:int(11)|unsigned">
                                            </div>

                                            <div class="col-xs-9">
                                                <label>{{_('Relations')}}</label>
                                                <input type="text" name="tables[0][relations]" class="form-control"
                                                       placeholder="phone_id:id|phones|cascade|cascade">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            @if($packages)
                                                <div class="col-xs-9">
                                                    @foreach($packages as $key => $package)
                                                        <lable>{{$key}}</lable>
                                                        <input type="checkbox" name="tables[0][packages][{{$key}}]" value="0">
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>



                                </div>

                                <a href="#" class="js-add-table">{{_('Add new table')}}</a>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">{{_('Generate')}}</button>
                                </div>

                            </form>


                            <form method="post" action="{{route('flush-modules')}}">
                                <button type="submit" class="btn btn-primary">{{_('Flush all')}}</button>
                            </form>

                            @if(isset($vendor) && isset($name))
                                <form method="post" action="{{route('flush-module')}}">
                                    <input type="hidden" name="module" value="{{$vendor . DIRECTORY_SEPARATOR . $name}}">
                                    <button type="submit" class="btn btn-primary">{{_('Flush current')}}</button>
                                </form>

                                <form method="post" action="{{route('export-module')}}">
                                    <input type="hidden" name="module" value="{{$vendor . DIRECTORY_SEPARATOR . $name}}">
                                    <button type="submit" class="btn btn-primary">{{_('Export current')}}</button>
                                </form>
                            @endif

                        </div>

                        @if($isGenerated)
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_2">
                                @if($pathModule)
                                    @include('scaffold-generator::editor')
                                @endif
                            </div>
                            @endif
                            <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->

            </div>
            <!--/.col (left) -->
        </div>
        <!-- /.row -->
    </section><!-- /.content -->


    <script>
        $(function() {
            $(".js-add-table").on("click", function() {
                var table = $(this).prev('div.table').clone();
                console.log(table);

                $(this).before(table);

                return false;

            });

            $("input[type=checkbox]").on("click", function() {
                $(this).val($(this).is(':checked') ? 1 : 0)
            })
        })
    </script>
@endsection
