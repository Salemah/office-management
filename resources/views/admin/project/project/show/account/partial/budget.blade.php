<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.account-budget.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
       {{-- <input type="hidden" name="project_end_date_initial" id="project_end_date_initial" @if ($project_duration >0) value="{{$projectDurationEnd->end_date}}" @endif> --}}
       <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="amount"> <b>Project Amount </b><span class="text-danger">*</span></label>
            <input type="number"  id="amount" value="{{ old('amount')}}" class="form-control " name="amount" placeholder="Enter Amount" >
            @error('amount')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="10" cols="40" class="form-control description" value="{{ old('description') }}" placeholder="Description..."></textarea>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace(description,{
            toolbarGroups: [{
                "name": "styles",
                "groups": ["styles"]
            },
                {
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                },

                {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                },
                {
                    "name": "document",
                    "groups": ["mode"]
                },
                {
                    "name": "links",
                    "groups": ["links"]
                },
                {
                    "name": "insert",
                    "groups": ["insert"]
                },

                {
                    "name": "undo",
                    "groups": ["undo"]
                },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })
    </script>
@endpush
