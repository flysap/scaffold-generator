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

                    <div style="clear:both; overflow: hidden; margin-bottom: 10px">
                        <div style="width:50px; height:400px; background:#000; float:left;">
                            <button type="button" class="btn btn-primary btn-small add_table" data-toggle="modal">
                                Add table
                            </button>

                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#tableModal">
                                Launch demo modal
                            </button>
                        </div>

                        <div id="diagram" style="width: calc(100% - 50px); float:left;"></div>
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
            height: 400px;
            background-image: url("http://freedevelopertutorials.azurewebsites.net/wp-content/uploads/2015/06/grid.png");
            background-repeat: repeat;
            position: relative;
        }

        .panel-heading {
            cursor: pointer;
        }

        .panel_table {
            width: 200px;
            position: absolute;
            margin: 0;
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

        Array.prototype.remove = function (from, to) {
            var rest = this.slice((to || from) + 1 || this.length);
            this.length = from < 0 ? this.length + from : from;
            return this.push.apply(this, rest);
        };

        var Field = function (name, type, size, default_value, is_primary, is_unique) {

            this.name = name;
            this.type = type;
            this.size = size;
            this.default_value = default_value;
            this.is_primary = is_primary;
            this.is_unique = is_unique;

            /** Check if field is primary  */
            this.isPrimary = function () {
                return (this.is_primary === true);
            }

            /** Check if field is unique  */
            this.isUnique = function () {
                return (this.is_unique === true);
            }

        };

        var Package = function (name) {

            this.name = name;
        };

        var Table = function (name) {

            this.name = name;
            this.fields = [];
            this.packages = [];

            this.addFields = function (fields) {
                var self = this;

                fields.map(function (val) {
                    self.addField(val);
                });
            }

            this.addField = function(field) {
                if( ! self.fields[field] )
                    self.fields[field] = new Field(field);
            }

            /**
             *  Remove field by key .
             *
             * */
            this.removeField = function(field) {
                if( this.fields[field] !== undefined )
                    this.fields[field].remove(field);
            }

            this.addPackages = function (packages) {
                var self = this;

                packages.map(function (val) {
                    var packageObj = new Package(val);

                    self.packages[val] = packageObj;
                });
            }

            /**
             * Render current table .
             *
             * */
            this.render = function (container) {

                var html = '<div id="' + this.name + '" class="panel panel-primary panel_table"><div class="panel-heading row-fluid"><span class="pull-right glyphicon glyphicon-remove tbl-remove" style="margin-left: 4px"></span><span class="pull-right glyphicon glyphicon-edit tbl-edit"></span>' + this.name + '</div><div class="panel-body"></div>';

                html += '<table class="table">';

                this.fields.map(function (field) {
                    html += '<tr>';
                    html += '<td>' + field.name + '</td>';
                    html += '</tr>';
                });

                this.packages.map(function (package) {
                    html += '<tr>';
                    html += '<td>' + package.name + '</td>';
                    html += '</tr>';
                });

                html += '</table>';

                html += '</div>';

                if (container) {
                    container.append(html);

                    jsPlumb.draggable(this.name, {
                        containment: true
                    });
                }

                return html;
            }

            /**
             * Repaint table .
             *
             * */
            this.flush = function() {
                if( element = window.getElementById(this.name) ) {
                    element.removeChild();

                    this.render();
                }
            }

        };

        var tableDesigner = {

            DEBUGG: true,

            tables: [],

            /**
             * Debugg message .
             *
             * */
            debugg: function (message) {
                if (window.location.href.indexOf('127.0.0.1:') >= 0 || this.DEBUGG === true)
                    console.log(message);
            },

            
            /**
             * Load from storage current canvas state .
             * */
            loadCanvasState: function (tables, block) {
                if (!this.is_supports_html5_storage())
                    throw new Error('Browser do not support local storage.!');

                if (! tables) {
                    var tables = Lockr.get('scaffold-tables');

                    if (tables)
                        tables = JSON.parse(tables);
                    else
                        tables = [];
                }

                this.addTables(tables);

                if(! block)
                    block = $("#diagram");

                jsPlumb.setContainer(block);
                jsPlumb.empty(block);

                this.render(block);
            },

            /**
             * That function have to load current tables state and save it to local storage
             *  Save canvas state will be triggered for each modify ..
             *
             * */
            saveCanvasState: function () {
                this.debugg('save tables current status to database source.');

                Lockr.flush();
                Lockr.set('scaffold-tables', JSON.stringify(this.tables));
            },


            /**
             * Load edit table panel .
             *
             * */
            loadPanel: function(table) {
                if( table ) {
                    if(! this.isTableExists(table))
                        throw new Error('Invalid table!');

                    var tableObj = this.getTable(table);

                    var html = '';

                    tableObj.fields.map(function(field) {
                        html += '<tr>';

                        html += '<td>'+field.name+'</td>';
                        html += '<td>'+field.type+'</td>';
                        html += '<td>'+field.size+'</td>';
                        html += '<td>'+field.name+'</td>';
                        html += '<td>delete</td>';

                        html += '</tr>';
                    });

                    $('#tableModal .table-fields').html(html);
                }

                /** Open the modal .. */
                $('#tableModal').modal('show')
            },

            closePanel: function() {
                $('#tableModal .table-fields').html('');

                /** Open the modal .. */
                $('#tableModal').modal('hide')
            },


            /**
             *  Add new table to the stack .
             * */
            addTable: function (table, fields, relations, packages) {
                if( this.isTableExists(table) )
                    throw new Error('Table already exists. Choose another name!');

                var tableObj = new Table(table);

                /**
                 * Add fields if they exists .
                 * */
                if (fields !== undefined)
                    tableObj.addFields(fields);

                /**
                 * Add packages if they exists .
                 * */
                if (packages !== undefined)
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
            addTables: function (tables) {
                var self = this;

                if (tables !== undefined)
                    tables.map(function (table) {
                        self.addTable(table.name, table.fields, table.packages);
                    });

                return this;
            },


            /**
             * Check if table exists .
             *
             * */
            isTableExists: function(table) {
                var is_table_exists = false;

                this.tables.map(function(val) {
                   if( val.name == table ) {
                       is_table_exists = true;
                       return false;
                   }
                });

                return is_table_exists;
            },

            getTable: function(table) {
                if(! this.isTableExists(table))
                    throw new Error('Table not exists!');

                var tableObj = null;
                this.tables.map(function(val) {
                     if( val.name == table ) {
                         tableObj = val;
                         return false;
                     }
                });

                return tableObj;
            },


            /**
             * Remove table .
             *
             * */
            removeTable: function (table) {
                var self = this;

                this.debugg('removed table "' + table + '" from database source');

                this.tables.map(function (val, i) {
                    if (val.name == table)
                        self.tables.remove(i);
                });

                this.saveCanvasState();

                return true;
            },

            /**
             * Remove tables .
             *
             * */
            removeTables: function () {
                var self = this;

                this.tables.map(function (table) {
                    self.removeTable(table.name);
                });
            },


            /**
             * Render all the tables .
             *
             * */
            render: function (container) {
                var tables = this.tables;

                this.debugg('rendering tables from database source');

                tables.map(function (val) {
                    val.render(container)
                });
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

        jsPlumb.ready(function () {
            try {
                tableDesigner.loadCanvasState(null, $("#diagram"));

                $('.add_table').on('click', function () {
                    var table = prompt('Please enter table name!');

                    if (table && table !== undefined)
                        if (tableObj = tableDesigner.addTable(table))
                            tableObj.render($("#diagram"))
                });

                $('.tbl-remove').on('click', function () {

                    /**
                     * If user want delete table i have to:
                     *
                     *  a. remove the table.
                     *  b. remove all the connections for the fields
                     *  c. remove endPoints from jsPlumb instance .
                     *  d. save the state to the database source
                     *  e. remove from html dom obj .
                     *
                     */

                    var div = $(this).closest('.panel_table');

                    if (confirm('You really want delete that table?')) {
                        if (tableDesigner.removeTable(div.attr('id')))
                            div.remove()
                    }
                })

                $('.tbl-edit').on('click', function() {
                    /**
                     *
                     * If user click on edit table i have to:
                     *
                     *      a. get the table from current tables.
                     *      b. fill current data from table to modal
                     *      c. open modal
                     */


                    var div = $(this).closest('.panel_table');

                    tableDesigner.loadPanel(
                        div.attr('id')
                    );
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
