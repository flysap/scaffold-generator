@extends('themes::layouts.default')

@section('content')

    <script src="{{ asset("/bower_components/lockr/lockr.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("/bower_components/jsPlumb/dist/js/jsPlumb-2.0.4-min.js") }}" type="text/javascript"></script>
    <link href="{{ asset("/bower_components/jsPlumb/dist/css/jsPlumbToolkit-defaults.css") }}" rel="stylesheet"
          type="text/css"/>

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
            <div class="col-md-12">
                <div class="box">


                    <!-- Button trigger modal -->
                    {{--<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                        Launch demo modal
                    </button>--}}

                    <button type="button" class="btn btn-primary btn-small add_table" data-toggle="modal">
                        Add table
                    </button>



                    <div id="diagram">

                    </div>


                    @include('scaffold-generator::addtable')
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->


    <style>
        #diagram {
            padding: 20px;
            width: 80%;
            height: 400px;
        }

        .item {
            position: absolute;
            height: 80px;
            width: 80px;
            border: 1px solid blue;
        }

    </style>

    <?php
    /**
     * So by default we need to have add dialog modal which will include all the logic in the separate view blade script .
     *  when the page is loaded is need to check for local storage if there is some data to render..
     *
     *   1. if there persist data for loading we have to
     *      a. load the data from local storage
     *      b. need a function which will render data from local storage .
     *      c. need a function addTable(data) and addTables(data)
     *      d. need a function removeTable(table) and removeTables
     *      e. need a function addConnection(source, target, params), removeConnection(source) or target, removeConnections()
     *      f. each drawn table must have hidden inputs with declared data, meaning fields, relations, packages etc ...
     *
     *
     *   2. if there is clicked to add new table i have to prompt user to add new table name, if user entered table name i have to:
     *          a. add new table to the tables
     *          b. and render that table on the page .
     */
    ?>

    <script type="text/javascript">

        var Field = function(name, type, size, default_value, is_primary, is_unique) {

            this.name = name;
            this.size = size;
            this.default_value = default_value;
            this.is_primary = is_primary;
            this.is_unique = is_unique;

            /** Check if field is primary  */
            this.isPrimary = function() {
                return (this.is_primary === true);
            }

            /** Check if field is unique  */
            this.isUnique = function() {
                return (this.is_unique === true);
            }

        };

        var Package = function(name) {

            this.name = name;
        };

        var Table = function(name) {

            this.name = name;
            this.fields = [];
            this.packages = [];

            this.addFields = function(fields) {
                var self = this;

                fields.map(function(val) {

                    //#@todo ..
                    var fieldObj = new Field(val);

                    self.fields[val] = fieldObj;
                });
            }

            this.addPackages = function(packages) {
                var self = this;

                packages.map(function(val) {
                    var packageObj = new Package(val);

                    self.packages[val] = packageObj;
                });
            }

            /**
             * Render current table .
             *
             * */
            this.render = function() {

                var html = '<div id="'+this.name+'" class="panel panel-primary"><div class="panel-heading">'+ this.name +'</div><div class="panel-body"></div>';

                html += '<table class="table">';

                this.fields.map(function(field) {
                    html += '<tr>';
                    html += '<td>'+field.name+'</td>';
                    html += '</tr>';
                });

                this.packages.map(function(package) {
                    html += '<tr>';
                    html += '<td>'+package.name+'</td>';
                    html += '</tr>';
                });


                html += '</table>';

                html += '</div>';

                return html;
            }

        };

        var tableDesigner = {

            DEBUGG: true,

            tables: [],

            /**
             * Debugg message .
             *
             * */
            debugg: function(message) {
                if (window.location.href.indexOf('127.0.0.1:') >= 0 || this.DEBUGG === true)
                    console.log(message);
            },

            
            /**
             * Load from storage current canvas state .
             * */
            loadCanvasState: function () {
                if (! this.is_supports_html5_storage())
                    throw new Error('Browser do not support local storage.!');

                var tables = Lockr.get('scaffold-tables');

                if( tables )
                    tables = JSON.parse(tables);
                 else
                    tables = [];

                this.addTables(tables);

                this.render(
                    $('#diagram')
                );
            },

            /**
             * That function have to load current tables state and save it to local storage
             *  Save canvas state will be triggered for each modify ..
             *
             * */
            saveCanvasState: function() {
                this.debugg('save tables current status to database source.');

                Lockr.flush();
                Lockr.set('scaffold-tables', JSON.stringify(this.tables));
            },


            /**
             *  Add new table to the stack .
             * */
            addTable: function(table, fields, relations, packages) {
                var tableObj = new Table(table);

                /**
                 * Add fields if they exists .
                 * */
                if( fields !== undefined )
                    tableObj.addFields(fields);

                /**
                 * Add packages if they exists .
                 * */
                if( packages !== undefined )
                    tableObj.addPackages(packages);

                this.tables.push(tableObj);

                this.debugg('adding table "' + table + '" to database source');

                /** Save current state to the data source .. */
                this.saveCanvasState();

                return tableObj;
            },

            /**
             * Add new tables to the stack
             *
             * */
            addTables: function(tables) {
                var self = this;

                if( tables !== undefined )
                    tables.map(function(table) {
                       self.addTable(table.name, table.fields, table.packages);
                    });

                return this;
            },


            /**
             * Remove table .
             *
             * */
            removeTable: function(table) {

                this.debugg('removed table "' + table + '" from database source');

                this.saveCanvasState();
            },

            /**
             * Remove tables .
             *
             * */
            removeTables: function() {

                this.saveCanvasState();
            },


            /**
             * Render all the tables .
             *
             * */
            render: function(container) {
                var tables = this.tables;

                this.debugg('rendering tables from database source');

                var html = '';

                tables.map(function(val) {
                    html += val.render();
                });

                container.append(html);
            },

            /**
             * Check if it support local storage ..
             *
             * @returns {boolean}
             */
            is_supports_html5_storage: function () {
                return 'localStorage' in window && window['localStorage'] !== null;
            }
        };

        $(function () {

            try {
                tableDesigner.loadCanvasState();

                $('.add_table').on('click', function() {
                    var table = prompt('Please enter table name!');

                    if( table && table !== undefined ) {
                        var tableObj = {};

                        if( tableObj = tableDesigner.addTable(table) )
                            $("#diagram").append(
                                tableObj.render()
                            );
                    }
                });

            } catch (e) {
                alert('Error ' + e.name + ":" + e.message + "\n" + e.stack);
            }

        });
    </script>


    <script>
        /*jsPlumb.ready(function () {

            var source = jsPlumb.makeSource('a');
            var target = jsPlumb.makeTarget('b');

            *//*var e1 = jsPlumb.addEndpoint("a", {
             isSource:true,
             parameters:{
             "p1":34,
             "p2":new Date(),
             "p3":function() { console.log("i am p3"); }
             }
             });

             var e2 = jsPlumb.addEndpoint("b", {
             isTarget:true,
             parameters:{
             "p5":343,
             "p3":function() { console.log("FOO FOO FOO"); }
             }
             });*//*

            jsPlumb.connect({source: source, target: target});
        });*/
    </script>

@endsection