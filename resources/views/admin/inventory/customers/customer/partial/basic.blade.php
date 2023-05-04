@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }
        .dropify-wrapper {
            height: 180px;
            width: 180px;
       }
    </style>
@endpush

<form action="{{ route('admin.inventory.customers.customer.update', $customer->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
        @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <input type="file" id="image" class="dropify form-control @error('image') is-invalid @enderror"
                   @if ($customer->image) data-default-file="{{ asset('img/customer/' . $customer->image) }}"
                   @else
                   data-default-file="{{asset('img/no-image/noman.jpg')}}"
                    @endif name="image">
            @error('image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="name"> <b>Name</b><span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ $customer->name }}"
                placeholder="Enter Name">
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="customer_type"><b>Customer Type</b><span class="text-danger">*</span></label>
            <select name="customer_type" id="customer_type"class="form-select @error('customer_type') is-invalid @enderror">
                <option selected>--Select Cuatomer Type--</option>
                @foreach ($ClientTypes as $clienttype )
                    <option value="{{$clienttype->id}}" {{$customer->customer_type == $clienttype->id ? 'selected' : ''}}>{{$clienttype->name}}</option>
                @endforeach
            </select>
            @error('customer_type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="customer_type_priority"><b>Customer Priority</b><span class="text-danger">*</span></label>
            <select name="customer_type_priority" id="customer_type_priority"class="form-select ">
                <option >--Select Priority--</option>
                <option value="1" {{$customer->customer_type_priority == 1 ? 'selected' : ''}}>First</option>
                <option value="2" {{$customer->customer_type_priority == 2 ? 'selected' : ''}}>Second</option>
                <option value="3" {{$customer->customer_type_priority == 3 ? 'selected' : ''}}>Third</option>
            </select>
            @error('customer_type_priority')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="email"><b>Email</b><span class="text-danger">*</span></label>
            <input type="text" name="email" id="email"class="form-control @error('email') is-invalid @enderror" value="{{$customer->email }}"placeholder="Enter Email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone"><b>Customer Phone</b><span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ $customer->phone }}" placeholder="Enter Phone Number">
            @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="10" cols="40"class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"placeholder="Description...">{{ $customer->description }}</textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
        </div>
    </div>
</form>
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function () {
            $('.dropify').dropify();
        });
    CKEDITOR.replace('description', {
        toolbarGroups: [
            { "name": "styles","groups": ["styles"] },
            {"name": "basicstyles","groups": ["basicstyles"] },
            {"name": "paragraph","groups": ["list", "blocks"] },
            {"name": "document","groups": ["mode"] },
            {"name": "links","groups": ["links"] },
            { "name": "insert","groups": ["insert"] },
            { "name": "undo", "groups": ["undo"] },
        ],
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
    });
            // client_typeData
        $(document).on('change', '#client_type', function () {
                var id = $("#client_type").val();
                var url = '{{ route('admin.crm.client.type.priority', ':id') }}';
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', id),
                    success: function(resp) {
                        $('#client_type_priority').val(resp.priority);
                    },
                    error: function() {
                        location.reload();
                    }
                });
        });
</script>
@endpush
