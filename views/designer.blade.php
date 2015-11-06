@extends('themes::layouts.default')

@section('content')

    <script src="https://raw.githubusercontent.com/ethaizone/Bootstrap-Confirmation/master/bootstrap-confirmation.js" type="text/javascript"></script>
    <script src="{{ asset("/bower_components/lockr/lockr.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("/bower_components/jsPlumb/dist/js/jsPlumb-2.0.4-min.js") }}" type="text/javascript"></script>
    <link href="{{ asset("/bower_components/jsPlumb/dist/css/jsPlumbToolkit-defaults.css") }}" rel="stylesheet" type="text/css"/>

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
                        <div style="width:33px; height:400px; background:#ecf0f5; float:left;">

                            <div class="btn-group-vertical">
                                <button type="button" class="btn btn-link add_table"><span class="glyphicon glyphicon-list" aria-hidden="true"></span></button>
                            </div>

                            {{--
                            <button type="button" class="btn btn-primary btn-small add_table" data-toggle="modal">
                                Add table
                            </button>--}}
                        </div>

                        <div id="diagram" style="width: calc(100% - 33px); float:left;"></div>
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

        #diagram .panel-heading {
            cursor: pointer;
            padding: 7px 8px;

        }

        #diagram .table>tbody>tr>td {
            padding: 5px 8px;
        }

        .panel_table {
            width: 200px;
            position: absolute;
            margin: 0;
        }

        .btn{
            padding:6px 9px;
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

        function clearForm() {
            $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
            $(':checkbox, :radio').prop('checked', false);
        }

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
            this.is_primary = is_primary ? is_primary : false;
            this.is_unique = is_unique ? is_unique : false;
            this.is_unsigned = is_unsigned ? is_unsigned : false;
            this.ref = null;

            /**
             * Check if field have type number .
             *
             * */
            this.isNumber = function() {
                return this.type == 'int';
            }

            this.haveRelation = function() {
                return this.ref !== null;
            }

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

        var Package = function (name, attributes) {

            this.name = name;
            this.attributes = attributes;
        };

        var Table = function (name, fields, packages, x, y, order) {

            this.name = name;
            this.fields = fields ? fields : {};
            this.packages = packages ? packages : {};
            this.x = x ? x : 0;
            this.y = y ? y : 0;
            this.order = order ? order : 0;

            /** If that option is setup to true i have to render hidden input fields . */
            this.showInputs = true;

            this.renderInputs = function() {
                return this.showInputs;
            }

            this.isFieldExists = function(field) {
                return field in this.fields;
            }

            this.isPackageExists = function(package) {
                return package in this.packages;
            }


            this.addFields = function (fields) {
                var self = this;

                if( fields ) {
                    if( fields instanceof Object ) {
                        var fieldKeys = Object.keys(fields);

                        fieldKeys.map(function(val) {
                            var field = fields[val];

                            self.addField(
                                new Field(field.name, field.type, field.size, field.default_value, field.is_primary, field.is_unique, field.is_unsigned)
                            )
                        })

                    } else {
                        fields.map(function (val) {
                            self.addField(val);
                        });
                    }
                }

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

                //#@todo check if there is no relations .
            }


            /**
             * Adding an package .
             *
             * */
            this.addPackage = function(package) {
                var self = this;

                if( package instanceof Package) {
                    if( this.isPackageExists(package.name) )
                        tableDesigner.debugg('Package already exists. Choose another one!');

                    self.packages[package.name] = package;
                } else {
                    if( package instanceof String) {
                        if( this.isPackageExists(package) )
                            tableDesigner.debugg('Package already exists. Choose another one!');

                        self.packages.package = new Package(package);
                    }
                }
            }

            this.addPackages = function (packages) {
                var self = this;

                if( packages ) {
                    if( packages instanceof Object ) {
                        var packageKeys = Object.keys(packages);

                        packageKeys.map(function(val) {
                            var packageObj = packages[val];

                            self.addPackage(
                                new Package(packageObj.name, packageObj.attributes)
                            )
                        })

                    } else {
                        packages.map(function (val) {
                            var packageObj = new Package(val);

                            self.addPackage(packageObj);
                        });
                    }
                }
            }


            /**
             * Render current table .
             *
             * */
            this.render = function (container) {

                var self = this;

                var html = '<div id="' + this.name + '" class="panel panel-primary panel_table" style="left: '+this.x+'px; top: '+this.y+'px"><div class="panel-heading row-fluid"><span class="pull-right glyphicon glyphicon-remove tbl-remove" style="margin-left: 4px;"></span><span class="pull-right glyphicon glyphicon-edit tbl-edit"></span><strong>' + this.name + '</strong></div>';

                html += '<table class="table">';


                if( self.renderInputs() )
                    html += '<input type="hidden" name="tables['+self.order+'][name]" value="'+self.name+'">';

                var fieldKeys = Object.keys(this.fields);

                var counter = 0;
                fieldKeys.map(function (field) {
                    var fieldObj = self.fields[field];

                    html += '<tr>';

                    if( self.renderInputs() )
                        html += '<input type="hidden" name="tables['+self.order+'][fields]['+counter+'][name]" value="'+fieldObj.name+'">';

                    html += '<td><div id="'+self.name + '_' + fieldObj.name+'">' + fieldObj.name + ' -   <i>' + fieldObj.type + ' (' +  fieldObj.size + ')</i> ' + (fieldObj.isPrimary() ? '<i class="fa fa-fw fa-key" style="color: #c1a203"></i>' : '') + '</div></td>';
                    html += '</tr>';

                    counter++;

                    //@todo add less show more button and make it hidden.
                    if( counter > 5 )
                        return false;
                });

                var packageKeys = Object.keys(this.packages);

                var counter = 0;
                packageKeys.map(function (package) {
                    html += '<tr>';

                    if( self.renderInputs() ) {
                        html += '<input type="hidden" name="tables['+self.order+'][packages]['+counter+'][name]" value="'+self.packages[package].name+'">';
                        html += '<input type="hidden" name="tables['+self.order+'][packages]['+counter+'][attributtes]" value="'+self.packages[package].attributes +'">';
                    }

                    html += '<td>' + self.packages[package].name + '</td>';
                    html += '</tr>';

                    counter++;
                });

                html += '</table>';

                html += '</div>';

                if (container) {
                    container.append(html);

                    fieldKeys.map(function (field) {

                        var fieldObj = self.fields[field];

                        /**
                         * Here we will adding source and target for fields which are numbers and is unsigned ..
                         *
                         *  1. if field is number and is unsigned or is primary key .
                         *  2. check if current have some relations and connect if needed
                         *
                         * */
                        if( fieldObj.isNumber() && ( fieldObj.isPrimary() || fieldObj.isUnsigned() ) ) {

                            /** Use js plumb window instance to add new endpoint .*/
                            jsPlumb.addEndpoint(self.name + '_' + fieldObj.name, {
                                isTarget: true,
                                isSource: (fieldObj.name == 'id') ? false : true
                            });

                            if( fieldObj.haveRelation() ) {
                                var relation = fieldObj.ref;

                                //#@todo connect relation .
                            }
                        }
                    });

                    //@todo repaint .
                    jsPlumb.repaint(this.name);

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
                }


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
                $('#tableModal').modal('toggle');
                $('#tableModal').on('shown.bs.modal', function () {
                    setTimeout(function (){
                        self.focusInput();
                    }, 300);
                })
            }

            this.focusInput = function() {
                $('#tableModal').find('input')
                        .not('input[type=hidden],input[type=button],input[type=submit],input[type=reset],input[type=image],button')
                        .filter(':enabled:visible:first')
                        .focus();
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
                var html = '<tr class="'+field.name+'">';

                html += '<td>'+field.name+'</td>';
                html += '<td>'+field.type+'</td>';
                html += '<td>'+field.size+'</td>';
                html += '<td>'+field.name+'</td>';
                html += '<td><a href="#"><span class="glyphicon glyphicon-trash delete-field" aria-hidden="true" data-field="'+field.name+'"></span></a></td>';

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

                if( ! type)
                    throw new Error('Please select field type');

                if(! size)
                    size = 55;

                var fieldObj = new Field(field, type, size, defaultVal, isPrimary, isUnique, isUnsigned);

                this.table.addField(
                    fieldObj
                );

                this.insertField(fieldObj);

                this.saveState();

                this.table.flush();

                clearForm();

                this.focusInput();
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

                if(! this.table.getField(field) )
                    throw new Error('Field with this name aren\'t exists!')

                this.table.removeField(field);

                $(".table-fields ." + field).remove();

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

            DEBUGG: false,

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

                jsPlumb.importDefaults({
                    ConnectionsDetachable:false
                });

                jsPlumb.setContainer(block);
                jsPlumb.empty(block);

                /**
                 * When new connection is made i have to:
                 *
                 *  1. show popup to let user select the connection type
                 *  2. if user selected connection type need an trigger which will catch that
                 *  3. get source and check if there is no more relation for that field exists
                 *  4. get source and check if that field type permit to have an connection
                 *  5. get target and check if field type permit to have an connection (id|primary)
                 *  6. get source and write to database source new relation and save to database and repaint table
                 *  7. get the target write to database the connection which are in connections: [target1:params, target2:params] and repaint table
                 *  8. when table render it have to check if field some connections and connect them if needed
                 *  9. when user try to delete some of field it have to check if that field aren't into relation
                 *  10. check if source and target cannot belong to the same table than show alert and return false. it can have relation to the same table.
                 *
                 * */
                jsPlumb.bind("connection", function(info) {
                    console.log(info);
                });

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
                this.addTable(table.name, table.fields, table.packages, table.x, table.y, table.order);
            },

            /**
             *  Add new table to the stack .
             * */
            addTable: function (table, fields, packages, x, y, order) {
                if( this.isTableExists(table) )
                    throw new Error('Table already exists. Choose another name!');

                var tableObj = new Table(table, null, null, x, y, order);

                if( fields )
                    tableObj.addFields(fields);

                if( packages )
                    tableObj.addPackages(packages);

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

                    if( tables instanceof  Object ) {
                        var keys = Object.keys(tables);

                        keys.map(function (val) {
                            var tableObj = tables[val];

                            self.addTable(tableObj.name, tableObj.fields, tableObj.packages, tableObj.x, tableObj.y, tableObj.order);
                        });
                    } else {
                        tables.map(function(table) {
                            self.addTable(table);
                        });
                    }
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

                    var order = $('.panel_table').length + 1;

                    if (table && table !== undefined)
                        if (tableObj = tableDesigner.addTable(table, null, null, null, null, order)) {

                            tableObj.addField(new Field(
                                'id', 'int', 11, null, true, false, false
                            ));

                            tableDesigner.saveCanvasState();

                            tableObj.render($("#diagram"));

                            $('#' + table).find('.tbl-edit').trigger('click');
                        }
                });

                $('#diagram').on('click', '.tbl-remove', function () {

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
                });

                $('#diagram').on('click', '.tbl-edit', function() {
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
                       form.find('select option:selected').val(),
                       form.find('.field-size').val(),
                       form.find('.field-default').val(),
                       form.find('.field-primary').val() > 0,
                       form.find('.field-unique').val() > 0,
                       form.find('.field-unsigned').val() > 0
                    );

                });

                $('#tableModal').on('click', '.delete-field', function() {
                    var div = $(this).closest('#tableModal');

                    var tablePanelObj = new TablePanel(
                        div.attr('data-table')
                    );

                    tablePanelObj.removeField(
                        $(this).data('field')
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
