
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.crm.client-reminder.store') }}"
    method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="date"><b>Date</b> <span class="text-danger">*</span></label>
            <input type="date"  id="date" class="form-control " name="date">
            @error('date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="time"><b>Time</b> <span class="text-danger">*</span></label>
            <input type="time"  id="time" class="form-control " name="time">
            @error('time')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="document"><b> File</b><span style="color: gray"> (if any)</span></label>
            <input type="file"  id="document" class="form-control " name="document">
            @error('document')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="reminder_note"> <b>Reminder Note </b></label>
                <textarea name="reminder_note" id="reminder_note" rows="10" cols="40"class="form-control " value="{{ old('reminder_note') }}"placeholder="Enter Reminder Note..."></textarea>
            @error('reminder_note')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
        <input type="hidden" name="show" id="show" value="show">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>

@push('script')
    <script>
        CKEDITOR.replace('reminder_note', {
            height  : '180px',
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
