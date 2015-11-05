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
                        <select class="form-control" name="field-type">
                            @foreach($fields as $field)
                                <option value="{{$field}}">{{ucFirst($field)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="number" class="form-control field-size" placeholder="Size" aria-describedby="basic-addon1">
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control field-default" placeholder="Default"aria-describedby="basic-addon1">
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox">
                                Primary
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox">
                                Unique
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control {
        border-radius: 5px;
    }
</style>