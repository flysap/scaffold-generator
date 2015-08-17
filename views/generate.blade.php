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

                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                                <input type="text" class="form-control input-lg" name="vendor" placeholder="Vendor">
                                            </div>
                                        </div>

                                        <div class="col-xs-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                <input type="text" class="form-control input-lg" name="name" placeholder="Name">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="table" style="margin-bottom: 5px">
                                    <div class="box-body">

                                        <h3><span onclick="remove_table(this)" style="cursor: pointer;"><i class="fa fa-remove"></i></span> Table #1</h4>

                                        <div class="row">

                                            <div class="col-xs-2">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-table"></i></span>
                                                    <input type="text" class="form-control input-sm" name="tables[0][name]" placeholder="users">
                                                </div>
                                            </div>

                                            <div class="col-xs-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-columns"></i></span>
                                                    <input type="text" class="form-control input-sm" name="tables[0][fields]" placeholder="id:int(11)|unsigned|nullable|index, phone_id:int(11)|unsigned">
                                                </div>
                                            </div>


                                            <div class="col-xs-7" style="margin-top: 14px;">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-chain"></i></span>
                                                    <input type="text" class="form-control input-sm" name="tables[0][relations]" placeholder="phone_id:id|phones|cascade|cascade">
                                                </div>
                                            </div>

                                            <div class="col-xs-7 checkbox">
                                                @if($packages)
                                                    <div class="col-xs-9">
                                                        @foreach($packages as $key => $package)
                                                            <label>
                                                                <input type="checkbox" name="tables[0][packages][{{$key}}]" value="0">
                                                                {{$key}}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

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
        function remove_table(span) {
            if( $('.table').length > 1 )
                $(span).closest('.table').remove();
        }

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
