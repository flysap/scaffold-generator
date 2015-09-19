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
                        <li class="active"><a href="#tab_1" data-toggle="tab">{{_('Build your module')}}</a></li>

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                {{_('Actions')}} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('flush-modules')}}">{{_('Flush modules')}}</a></li>

                                @if($isGenerated)
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('flush-module')}}?module={{$vendor . DIRECTORY_SEPARATOR . $name}}">{{_('Flush module')}}</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('export-module')}}?module={{$vendor . DIRECTORY_SEPARATOR . $name}}">{{_('Export module')}}</a></li>
                                @endif

                            </ul>
                        </li>

                        @if($isGenerated)
                            <li><a href="#tab_2" data-toggle="tab" class="editor">{{_('Editor')}}</a></li>
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
                                                <input type="text" class="form-control input-lg" name="vendor" placeholder="Vendor" value="{{Input::get('vendor')}}">
                                            </div>
                                        </div>

                                        <div class="col-xs-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                <input type="text" class="form-control input-lg" name="name" placeholder="Name" value="{{Input::get('name')}}">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <?php
                                    $tables = Input::has('tables') ? Input::get('tables') : [0];
                                ?>

                                <?php $key = 0; ?>
                                @foreach($tables as $table)
                                    <div class="table" style="margin-bottom: 5px">
                                        <div class="box-body">

                                            <h3><span onclick="remove_table(this)" style="cursor: pointer;"><i class="fa fa-remove"></i></span> <span class="js-table-number">Table #{{$key + 1}}</span></h3>

                                                <div class="row">

                                                    <div class="col-xs-2">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-table"></i></span>
                                                            <input type="text" class="form-control input-sm" name="tables[{{$key}}][name]" placeholder="users" value="{{$table['name']}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-4">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-columns"></i></span>
                                                            <input type="text" class="form-control input-sm" name="tables[{{$key}}][fields]" placeholder="id:int(11)|unsigned|nullable|index, phone_id:int(11)|unsigned" value="{{$table['fields']}}">
                                                        </div>
                                                    </div>


                                                    <div class="col-xs-7" style="margin-top: 14px;">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-chain"></i></span>
                                                            <input type="text" class="form-control input-sm" name="tables[{{$key}}][relations]" placeholder="phone_id:id|phones|cascade|cascade" value="{{$table['relations']}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-9 checkbox">
                                                        @if($packages)
                                                            <div class="col-xs-9">
                                                                @foreach($packages as $package_key => $package)
                                                                    <label>
                                                                        <input type="checkbox" name="tables[{{$key}}][packages][{{$package_key}}]" value="0" {{isset($package['is_default']) && $package['is_default'] == true ? 'checked' : ''}} onclick="<?php if(isset($package['attributes'])) { ?>showPackageAttributes(this); <?php } ?>">
                                                                        <span title="{{isset($package['description']) ? $package['description'] : ''}}">{{$package_key}}</span>

                                                                        @if(isset($package['attributes']))
                                                                            <textarea disabled name="tables[{{$key}}][packages][{{$package_key}}][attributes]" hidden rows="10" cols="30">{{isset($package['attributes']) ? $package['attributes'] : ''}}</textarea>
                                                                        @endif
                                                                    </label>



                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                        </div>
                                    </div>
                                    <?php $key++; ?>
                                @endforeach


                                <a href="#" class="js-add-table">
                                    <span style="cursor: pointer;"><i class="fa fa-plus-circle"></i></span>
                                </a>

                                <div class="box-footer" style="border-top: 0">
                                    <button type="submit" class="btn bg-olive margin">{{_('Generate')}}</button>
                                </div>

                            </form>
                        </div>

                        @if($isGenerated)
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_2">
                                @if($pathModule)
                                    @include('scaffold-generator::editor')
                                @endif
                            </div>
                            <!-- /.tab-pane -->
                        @endif

                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->

            </div>
            <!--/.col (left) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    <script type="text/javascript">
        /**
         * Show package attributes if selected ..
         *
         * @param object
         */
        function showPackageAttributes(object) {
            var textarea = $(object).closest('label').find('textarea');

            if( textarea ) {
                textarea.toggle(function() {
                    $(object).prop('checked') ? textarea.removeAttr('disabled').show() : textarea.attr('disabled', true).attr('hidden', true);
                });
            }
        }

        /**
         * Remove table .
         *
         * @param span
         */
        function remove_table(span) {
            if( $('.table').length > 1 )
                $(span).closest('.table').remove();
        }

        $(function() {
            $(".js-add-table").on("click", function() {
                var table = $(this).prev('div.table').clone();

                table.find('input[name*=tables]').each(function(key, value) {
                   $(value).attr('name', $(value).attr('name').replace(new RegExp("(\\d+)", "g"), $('.table').length))
                });

                table.find('.js-table-number').html(
                    table.find('.js-table-number').text().replace(new RegExp("(\\d+)", "g"), $('.table').length + 1)
                );

                $(this).before(table);

                return false;

            });

            $("input[type=checkbox]").on("click", function() {
                $(this).val($(this).is(':checked') ? 1 : 0)
            })
        })
    </script>
@endsection
