
<div class="card-body">
    <input type="hidden" name="id" value="{{ $id }}">
    <div class="form-group col-12">
        <textarea class="summernote" name="content" >{{ $content ?? null}}</textarea>
    </div>

</div>