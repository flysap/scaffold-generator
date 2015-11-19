@extends('themes::layouts.default')

@section('content')

    <!-- Include custom components -->
    <link href="/bower_components/database-designer/assets/style.css" rel="stylesheet" type="text/css"/>

    <!-- Include lockr components -->
    <script src="/bower_components/lockr/lockr.min.js" type="text/javascript"></script>

    <!-- Include bootstrap confirmation components -->
    <script src="/bower_components/bootstrap-confirmation/bootstrap-confirmation.js" type="text/javascript"></script>

    <!-- Include jsPlumb components -->
    <script src="/bower_components/jsPlumb/dist/js/jsPlumb-2.0.4-min.js" type="text/javascript"></script>
    <link href="/bower_components/jsPlumb/dist/css/jsPlumbToolkit-defaults.css" rel="stylesheet" type="text/css"/>

    <!-- Include keyboardjs components -->
    <script src="/bower_components/keyboardjs/dist/keyboard.min.js" type="text/javascript"></script>

    <!-- Include notifyjs components -->
    <script src="/bower_components/notifyjs/dist/notify.js" type="text/javascript"></script>

    <!-- Include parse.com components -->
    <!--<script src="bower_components/parse-js-sdk/lib/parse.min.js" type="text/javascript"></script>-->
    <script src="http://www.parsecdn.com/js/parse-1.6.7.min.js"></script>

    <!-- Include mask components -->
    <script src="/bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.js"></script>

    <script src="/bower_components/database-designer/database-designer.js" type="text/javascript"></script>

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
                <div class="box">
                    <div style="clear:both; overflow: auto; width: 100%; height:700px; position: relative" class="main_block">
                        <div style="width:4033px; overflow: hidden; height:1800px">

                            <div style="width:33px; height:100%; background:#ecf0f5; float:left;">
                                <div class="btn-group-vertical">
                                    <button type="button" class="btn btn-link add_table" title="New table"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>

                                    <button type="button" class="btn btn-link refresh_schema" title="Clear schema"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span></button>

                                    <p style="text-align: center; padding: 0; margin: 5px 0; color: #507597;"><span
                                                class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></p>
                                    <button type="button" class="btn btn-link load_templates" title="Load templates"><span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span></button>
                                    <button type="button" class="btn btn-link save_table" title="Push template"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></button>

                                    <p style="text-align: center; padding: 0; margin: 5px 0; color: #507597;"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></p>

                                    <button type="button" class="btn btn-link login_user" title="Login"><span class="glyphicon glyphicon glyphicon-log-in" aria-hidden="true"></span></button>

                                    <button type="button" class="btn btn-link logout_user" title="Logout"><span class="glyphicon glyphicon glyphicon-log-out" aria-hidden="true"></span></button>

                                    <p style="text-align: center; padding: 0; margin: 5px 0; color: #507597;"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></p>

                                    <button type="button" class="btn btn-link export_sql" title="Export SQL"><span class="glyphicon glyphicon glyphicon-save-file" aria-hidden="true"></span></button>
                                </div>
                            </div>

                            <div id="diagram" style=" width:calc(100% - 33px); height:100%"></div>
                        </div><!-- .container -->

                        <div class="minimap"></div>
                        <!--<div class="sidebar-right"></div>-->
                    </div><!-- .wrapper -->

                    <!-- Bootstrap modal -->
                    <div class="modal fade bs-example-modal-lg" id="tableModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                                </div>

                                <div class="modal-body">

                                    <div class="form-inline form-fields" style="margin-bottom: 10px">
                                        <div class="form-group">
                                            <input type="text" class="form-control field-name" placeholder="Field" aria-describedby="basic-addon1">
                                        </div>

                                        <div class="form-group">
                                            <select class="form-control field-type">
                                                <option value="int" selected data-size="11">Int</option>
                                                <option value="enum">Enum</option>
                                                <option value="tinyint" data-size="1">Tinyint</option>
                                                <option value="varchar" data-size="55">Varchar</option>
                                                <option value="longtext">Longtext</option>
                                                <option value="text">Text</option>
                                                <option value="timestamp">Timestamp</option>
                                                <option value="date">Date</option>
                                                <option value="datetime">DateTime</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control field-size" placeholder="Size" value="11"
                                                   aria-describedby="basic-addon1">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control field-default" placeholder="Default"
                                                   aria-describedby="basic-addon1">
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="field-primary" value="0">
                                                    Primary
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="field-unique" value="0">
                                                    Unique
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="field-unsigned" value="0">
                                                    Unsigned
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-inline btn-success btn-flat add-field">Add field</button>
                                    </div>

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Details</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody class="table-fields"></tbody>
                                    </table>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Modal -->
                    <div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Load template</h4>
                                </div>
                                <div class="modal-body">
                                    <select class="form-control"></select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary load_template">Load</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Modal -->
                    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Login</h4>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="form-group">
                                            <label for="email" class="control-label">Email:</label>
                                            <input type="text" class="form-control" id="email">
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="control-label">Password:</label>
                                            <input type="password" class="form-control" id="password">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary js_login">Login</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Modal -->
                    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Hot keys</h4>
                                </div>
                                <div class="modal-body">
                                    <p>New table - <b>CTRL + A</b></p>
                                    <p>Clear dashboard - <b>CTRL + X</b></p>
                                    <p>Load / push template - <b>CTRL + L / P</b></p>
                                    <p>Log in / out - <b>CTRL + I / O</b></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-block btn-success btn-lg" onclick="$('.diagram_form').submit()">Generate</button>
                    </div>
                </div>
            </div>
            <!--/.col (left) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    <script type="text/javascript">
        $(function() {
            $('.field-name').inputmask("Regex", {
                regex: "([a-zA-Z_]{2,})",
                onBeforePaste: function(val) {
                    if( val == ' ') {
                        return '_';
                    }
                }
            });

            $("[type=checkbox]").on('click', function () {
                $(this).val(this.checked ? 1 : 0)
                $(this).prop('checked', this.checked ? 1 : 0)
            })

            $("#tableModal select").on('change', function() {
                if( selected = $(this).find('option:selected') ) {
                    var size = selected.data('size') ? selected.data('size') : null;
                    $('.field-size').val(size)
                }
            })
        });
    </script>
@endsection
