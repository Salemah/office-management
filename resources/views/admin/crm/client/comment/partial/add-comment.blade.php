
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.crm.client-comment.store') }}"
    method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="comment"> <b>Comment </b> <span class="text-danger">*</span></label>
            <textarea name="comment" id="comment" rows="15" cols="40"class="form-control " value="{{ old('comment') }}"placeholder="Enter Comment..."></textarea>
            @error('comment')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="document"><b> File</b><span style="color: gray"> (if any)</span></label>
            <input type="file"  id="document" class="form-control " name="document">
            @error('document')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>

@push('script')
    <script>
        CKEDITOR.replace('comment', {
            height  : '100px',
            toolbarGroups: [
                {"name": "styles","groups": ["styles"]},
                {"name": "basicstyles","groups": ["basicstyles"]},
                {"name": "paragraph","groups": ["list", "blocks"]},
                {"name": "document","groups": ["mode"]},
                {"name": "links","groups": ["links"]},
                {"name": "insert","groups": ["insert"]},
                {"name": "undo","groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

    </script>
@endpush
