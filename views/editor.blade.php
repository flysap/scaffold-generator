<div style="float: left; width: 82%">
    {!! Flysap\FileManager\editFile($pathModule . '/module.json', ['on_click' => '.editor', 'editor_var' => 'coreEditor']) !!}
    <br /><input class="btn btn-flat js-update-file" type="button" value="{{ _('Update file') }}">
</div>

<div style="margin-left: 83%">
    <div class="form-group">
        <label>{{_("Select file")}}</label>
        {!! Flysap\FileManager\listFiles($pathModule, 'select', ['active' => 'module.json', 'class' => 'form-control']) !!}
    </div>
    <input type="button" class="btn btn-flat js-load-file" value="{{ _('Load file') }}">
</div>

<div class="clearfix"></div>

<script type="text/javascript">

    /**
     * Update file .
     */
    $(".js-update-file").on("click", function() {
        var file_active = '{{$pathModule}}/' + $("select.form-control option:selected").text();

        $.post('{{route('update-file')}}', {
            content: window.coreEditor.getDoc().getValue(),
            file: file_active
        });

        return false;
    });

    /**
     * Get the file .
     */
    $(".js-load-file").on("click", function() {
        var file_active = '{{$pathModule}}/' + $("select.form-control option:selected").text();

        $.get('{{route('load-file')}}', {
            file: file_active
        }, function(content) {
            window.coreEditor.getDoc().setValue(content)
        });

        return false;
    })
</script>