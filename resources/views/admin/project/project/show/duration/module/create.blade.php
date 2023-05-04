<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.module.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  >
            <label for="module_name"><b>Module Name</b><span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('module_name') is-invalid @enderror" name="module_name" id="module_name" placeholder="Enter Module Name" >
            @error('module_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <label for="status"><b>Status</b><span class="text-danger">*</span></label>
            <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                <option value="">--Select Status--</option>
                <option value="1" selected>Up Coming</option>
                <option value="2">On Going</option>
                <option value="3">Complete</option>
                <option value="4">Cancel</option>
                <option value="5">On Hold</option>
            </select>
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="3"
                      class="form-control @error('description') is-invalid @enderror"
                       placeholder="Description..."></textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group my-3">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
        CKEDITOR.replace(description,{
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"] },
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links","groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo","groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })

</script>
@endpush
