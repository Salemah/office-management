@extends('layouts.dashboard.app')

@section('title', 'Edit Imported CLient')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
                <span>Customer </span>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.customers.customer.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.customers.customer.update', $customer->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <p class="m-0">Create Client </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="name">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ $customer->name }}" placeholder="Enter Name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="email">Email<span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{$customer->email}}"
                                    placeholder="Enter Email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="customer_type">Client Type<span class="text-danger">*</span></label>
                                <select name="customer_type" id="customer_type"
                                    class="form-select @error('customer_type') is-invalid @enderror">
                                    <option selected>--Select Customer Type--</option>
                                    @foreach ($ClientTypes as $clienttype )
                                    <option value="{{$clienttype->id}}" {{ $clienttype->id == $customer->customer_type ? 'selected' : ' '}}>{{$clienttype->name}}</option>
                                    @endforeach
                                </select>
                                @error('customer_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="customer_type_priority">Cyustomer Priority<span class="text-danger">*</span></label>
                                <select name="customer_type_priority" id="customer_type_priority"
                                    class="form-select ">
                                    <option >--Select Priority--</option>
                                    <option value="1" {{ $customer->customer_type_priority == 1 ? 'selected' : ' '}} >First</option>
                                    <option value="2" {{ $customer->customer_type_priority == 2 ? 'selected' : ' '}} >Second</option>
                                    <option value="3" {{ $customer->customer_type_priority == 3 ? 'selected' : ' '}} >Third</option>
                                </select>
                                @error('customer_type_priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="interested_on">Interested On<span class="text-danger">*</span></label>
                                <select name="interested_on" id="interested_on"
                                    class="form-select @error('interested_on') is-invalid @enderror">
                                    <option selected>--Select Contact Through--</option>
                                    @foreach ($InterestedsOn as $InterestedOn )
                                    <option value="{{$InterestedOn->id}}" {{ $InterestedOn->id == $customer->interested_on ? 'selected' : ' '}} >{{$InterestedOn->name}}</option>
                                    @endforeach
                                </select>
                                @error('interested_on')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status">Status<span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option>--Select Status--</option>
                                    <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="10" cols="40"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Description...">{{ $customer->description }}</textarea>
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
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>

@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        CKEDITOR.replace('description', {
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
            removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
        $("#add_contact_person").click(function () {
            $("#contact_person_section").append('' +
                '<div class="row" id="remove_row_contact_person" >' +
                    '<div class="form-group col-12 col-sm-12 col-md-11 mb-2">'+
                        '<input type="text" name="contact_person[]" id="contact_person"class="form-control" value="{{ old('contact_person') }}"placeholder="Enter Contact Person">'+
                            '@error('contact_person')'+
                                '<span class="invalid-feedback" role="alert">'+
                                    '<strong>{{ $message }}</strong>'+
                                '</span>'+
                          ' @enderror'+
                    '</div>'+
                    '<div class="col-sm-1">' +
                        '<div class="form-group">' +
                            '<button id="btn_remove_contact_person" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                        '</div>' +
                    '</div>'+
                '</div>'
            );
        });

        $(document).on('click', '#btn_remove_contact_person', function () {
            $(this).parents('#remove_row_contact_person').remove();
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
