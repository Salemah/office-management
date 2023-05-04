@push('css')
@endpush
<form action="{{ route('admin.loan-authority.update',$authority->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
            @php
                $auth = Auth::user();
                 $user_role = $auth->roles->first();
            @endphp
            @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror" >
                    <option value="" selected >--Select warehouse_id--</option>
                    @forelse ($warehouses as $item)
                        <option value="{{ $item->id }}" {{$authority->warehouse_id == $item->id ? 'selected' : ''}}>
                            {{ $item->name }}
                        </option>
                    @empty
                        <option value="">No Warehouse</option>
                    @endforelse
                </select>
            @else
                <input type="text" name="warehouse_id_name"  class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
            @endif
                @error('warehouse_id')
                    <span class="alert text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="deposit_source"><b>Name</b><span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"value="{{$authority->name}}" placeholder="Enter Name...">
                @if ($errors->has('name'))
                    <span class="alert text-danger">
                         {{ $errors->first('name') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="deposit_source"><b>Email</b><span class="text-danger">*</span></label>
            <input type="text" name="email" id="email" class="form-control"value="{{$authority->email}}" placeholder="Enter email...">
                @if ($errors->has('email'))
                    <span class="alert text-danger">
                        {{ $errors->first('email') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone"><b>Phone</b><span class="text-danger">*</span></label>
            <input type="number" name="phone" id="phone" class="form-control"value="{{$authority->phone}}" placeholder="Enter phone...">
                @if ($errors->has('phone'))
                    <span class="alert text-danger">
                       {{ $errors->first('phone') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="note"><b>Note</b><span class="text-danger">*</span></label>
            <textarea name="note" id="note" rows="3" class="form-control " placeholder="Enter Note...">{{$authority->note}}</textarea>
                @if ($errors->has('note'))
                    <span class="help-block">
                        {{ $errors->first('note') }}
                    </span>
                @endif
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
        </div>
    </div>
</form>



@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        CKEDITOR.replace('note', {
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
            removeButtons: 'Image,Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>
@endpush