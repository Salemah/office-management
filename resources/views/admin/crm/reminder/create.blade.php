@extends('layouts.dashboard.app')

@section('title', 'Add Reminder')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single{
           padding:6px;
           height: 37px;
           width: 100%;
           font-size: 1.2em;
           position: relative;
       }
   </style>
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
    </script>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Reminder</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.crm.client.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')
    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <form class="add-client-document" enctype="multipart/form-data"
                    action="{{ route('admin.crm.client-reminder.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="client_id"><b>Client Name</b><span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id"class="form-select select2">
                                <option >--Select Client--</option>
                                    @foreach ($clients as $client)
                                        <option value="{{$client->id}}" >{{$client->name}}</option>
                                    @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="date"><b>Date</b> <span class="text-danger">*</span></label>
                            <input type="date" id="date" class="form-control " name="date">
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="time"><b>Time</b> <span class="text-danger">*</span></label>
                            <input type="time" id="time" class="form-control " name="time">
                            @error('time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="document"><b> File</b><span style="color: gray"> (if any)</span></label>
                            <input type="file" id="document" class="form-control " name="document">
                            @error('document')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                            <label for="reminder_note"> <b>Reminder Note </b></label>
                            <textarea name="reminder_note" id="reminder_note" rows="10" cols="40"class="form-control "
                                value="{{ old('reminder_note') }}"placeholder="Enter Reminder Note..."></textarea>
                            @error('reminder_note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <input type="hidden" id="list" class="form-control " name="list" value="from-list">
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        CKEDITOR.replace('reminder_note', {
            height: '120px',
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
            removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

        ref('client_id');
    function ref(params){
            $('#'+params).select2({
                ajax: {
                    url: '{{route('admin.crm.client.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        console.log();
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    value: item.id,
                                    id: item.id,
                                }
                            })
                        };
                    }
                }
            });
    }
    </script>
@endpush
