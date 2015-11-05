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

        var Field = function (name, type, size, default_value, is_primary, is_unique, is_unsigned) {

            this.name = name;
            this.type = type;
            this.size = size;
            this.default_value = default_value;
            this.is_primary = is_primary;
            this.is_unique = is_unique;
            this.is_unsigned = is_unsigned;

            /** Check if field is primary  */
            this.isPrimary = function () {
                return (this.is_primary === true);
            }

            /** Check if field is unique  */
            this.isUnique = function () {
                return (this.is_unique === true);
            }

            /** Check if field is unsigned  */
            this.isUnsigned = function () {
                return (this.is_unsigned === true);
            }

        };

        var Package = function (name) {

            this.name = name;
        };

        var Table = function (name, fields, packages, x, y) {

            this.name = name;
            this.fields = fields ? fields : {};
            this.packages = packages ? packages : {};
            this.x = x ? x : 0;
            this.y = y ? y : 0;

            this.addFields = function (fields) {
                var self = this;

                fields.map(function (val) {
                    self.addField(val);
                });
            }

            this.isFieldExists = function(field) {
                return field in this.fields;
            }

            this.addField = function(field) {
                var self = this;

                if( field instanceof Field) {
                    if( this.isFieldExists(field.name) )
                        tableDesigner.debugg('Field already exists. Choose another one!');

                    self.fields[field.name] = field;
                } else {
                    if( field instanceof String) {
                        if( this.isFieldExists(field) )
                            tableDesigner.debugg('Field already exists. Choose another one!');

                        self.fields.field = new Field(field);
                    }
                }
            }

            /**
             * Get field from table .
             *
             * */
            this.getField = function(field) {
                if( this.isFieldExists(field) )
                    return this.fields[field];
            }

            /**
             *  Remove field by key .
             *
             * */
            this.removeField = function(field) {
                if( this.isFieldExists(field) )
                    delete this.fields[field];
            }

            this.addPackages = function (packages) {
                var self = this;

                packages.map(function (val) {
                    var packageObj = new Package(val);

                    self.packages.val = packageObj;
                });
            }

            /**
             * Render current table .
             *
             * */
            this.render = function (container) {

                var self = this;

                var html = '<div id="' + this.name + '" class="panel panel-primary panel_table" style="left: '+this.x+'px; top: '+this.y+'px"><div class="panel-heading row-fluid"><span class="pull-right glyphicon glyphicon-remove tbl-remove" style="margin-left: 4px"></span><span class="pull-right glyphicon glyphicon-edit tbl-edit"></span>' + this.name + '</div><div class="panel-body"></div>';

                html += '<table class="table">';

                var fieldKeys = Object.keys(this.fields);

                fieldKeys.map(function (field) {
                    html += '<tr>';
                    html += '<td>' + self.fields[field].name + '</td>';
                    html += '</tr>';
                });

                var packageKeys = Object.keys(this.packages);

                packageKeys.map(function (package) {
                    html += '<tr>';
                    html += '<td>' + self.packages[package].name + '</td>';
                    html += '</tr>';
                });

                html += '</table>';

                html += '</div>';

                if (container)
                    container.append(html);

                jsPlumb.draggable(this.name, {
                    containment: true,
                    grid:[50,50],
                    drag:function(e){
                        jsPlumb.repaint($(this));
                    },
                    stop: function(e) {
                        self.x = e.pos[0];
                        self.y = e.pos[1];

                        tableDesigner.updateTable(self);
                    }
                });

                return html;
            }

            /**
             * Repaint table .
             *
             * */
            this.flush = function() {
                if( element = $('#' + this.name) ) {
                    element.remove();

                    this.render($("#diagram"));
                }
            }

        };

        var TablePanel = function(table) {

            this.table = tableDesigner.getTable(table);

            /**
             * Load edit table panel .
             *
             * */
            this.loadPanel = function() {
                var self = this;

                var fieldKeys = Object.keys(self.table.fields);

                $('#tableModal .table-fields').html('');
                fieldKeys.map(function(field) {
                    self.insertField(self.table.fields[field]);
                });

                $('#tableModal').attr('data-table', this.table.name);

                /** Open the modal .. */
                $('#tableModal').modal('show')
            }

            this.closePanel = function() {
                $('#tableModal .table-fields').html('');

                $('#tableModal').removeAttr('data-table');

                /** Open the modal .. */
                $('#tableModal').modal('hide');

                /** Save state for current table . */
                this.saveState();
            }


            this.insertField = function(field) {
                var html = '<tr>';

                html += '<td>'+field.name+'</td>';
                html += '<td>'+field.type+'</td>';
                html += '<td>'+field.size+'</td>';
                html += '<td>'+field.name+'</td>';
                html += '<td>delete</td>';

                html += '</tr>';

                $('#tableModal .table-fields').append(html);
            }

            /**
             * Add field for current table .
             *
             * */
            this.addField = function(field, type, size, defaultVal, isPrimary, isUnique, isUnsigned) {
                /**
                 * When user try to add a field i have to:
                 *
                 *  a. have to check if field with the same name already exists in current table
                 *  b. check if field name is valid (i mean it have no spaces, numbers no -, it must use cameCase style and use _ instead of spaces)
                 *  c. check if field name is no too long ..
                 *  d. check the combination of params entered by user , primary not null etc ..
                 *  e. i have to get the current table and insert that field to current table.
                 *  f. i have to use template and insert current field to the current panel
                 *  g. i have to save table current state.
                 *  m. i have to repaint current table to the diagram.
                 *  n. i have to clear panel form fields.
                 *
                 * */

                if( this.table.getField(field) )
                    throw new Error('Field with the same name already exists!')

                if( ! field.match(/^([a-zA-Z_]){2,20}$/gi) )
                    throw new Error('Invalid field name. Please choose another name!');

                //#@todo check the combination

                var fieldObj = new Field(field, type, size, defaultVal, isPrimary, isUnique, isUnsigned);

                this.table.addField(
                    fieldObj
                );

                this.insertField(fieldObj);

                this.saveState();

                this.table.flush();
            }

            /**
             * Remove field from current table .
             *
             * */
            this.removeField = function(field) {

                /**
                 *  When user try to delete field from current table i have to:
                 *
                 *      a. i have to check if that field persist in current table
                 *      b. i have to get all connections between fields and check if current field have no connections with other fields.
                 *      c. i have to delete that field from current table
                 *      d. i have to save current state to the source database.
                 *      e. i have to delete html from current panel
                 *      f. i have to repaint current table from diagram .
                 * */


                this.saveState();

                this.table.flush();
            }

            /**
             * Save state for current table .
             *
             * */
            this.saveState = function() {
                tableDesigner.updateTable(
                    this.table
                );

                tableDesigner.debugg('Save state for table ' + this.table.name);
            }
        }


        var tableDesigner = {

            DEBUGG: true,

            tables: {},

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
                if (! this.is_supports_html5_storage())
                    throw new Error('Browser do not support local storage.!');

                if (! tables) {
                    var tables = Lockr.get('scaffold-tables');

                    if (tables)
                        tables = JSON.parse(tables);
                    else
                        tables = {};
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
             * Update table .
             *
             * */
            updateTable: function(table) {
                this.removeTable(table.name);
                this.addTable(table.name, table.fields, table.packages, table.x, table.y);
            },

            /**
             *  Add new table to the stack .
             * */
            addTable: function (table, fields, packages, x, y) {
                if( this.isTableExists(table) )
                    throw new Error('Table already exists. Choose another name!');

                var tableObj = new Table(table, fields, packages, x, y);

                this.tables[table] = tableObj;

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

                if (tables !== undefined) {
                    var keys = Object.keys(tables);

                    keys.map(function (val) {
                        self.addTable(tables[val].name, tables[val].fields, tables[val].packages, tables[val].x, tables[val].y);
                    });
                }

                return this;
            },


            /**
             * Check if table exists .
             *
             * */
            isTableExists: function(table) {
                return table in this.tables;
            },

            getTable: function(table) {
                if(! this.isTableExists(table))
                    throw new Error('Table not exists!');

                return this.tables[table];
            },


            /**
             * Remove table .
             *
             * */
            removeTable: function (table) {
                if( ! this.isTableExists(table) )
                    throw new Error('Table are not exists. Cannot be deleted!');

                this.debugg('removed table "' + table + '" from database source');

                delete this.tables[table];

                this.saveCanvasState();

                return true;
            },

            /**
             * Remove tables .
             *
             * */
            removeTables: function () {
                this.tables = {};

                this.saveCanvasState();
            },


            /**
             * Render all the tables .
             *
             * */
            render: function (container) {
                var tables = this.tables;

                this.debugg('rendering tables from database source');

                var keys = Object.keys(tables);

                keys.map(function (val) {
                    tables[val].render(container)
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

                    var tablePanelObj = new TablePanel(
                        div.attr('id')
                    );

                    tablePanelObj.loadPanel();

                });

                $('.add-field').on('click', function() {
                    var div = $(this).closest('#tableModal');

                    var tablePanelObj = new TablePanel(
                        div.attr('data-table')
                    );

                    var form = div.find('.form-fields');

                    tablePanelObj.addField(
                       form.find('.field-name').val(),
                       form.find('.field-type').val(),
                       form.find('.field-size').val(),
                       form.find('.field-default').val()
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
