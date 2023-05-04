@extends('layouts.dashboard.app')

@section('title', 'Create Notice')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .text-danger strong {
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title {
            width: 50%;
            font-weight: 600;
            display: none;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }


        @media (min-width: 200px ) and (max-width: 1130px ) {
            .responsive-table {
                width: 100%;
            }
            .responsive-table th {
                display: none;
            }
            .responsive-table .responsive-table-tr {
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            .responsive-table tr td {
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title {
                display: block;
            }
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
                <span>HRM</span>
            </li>
            <li class="breadcrumb-item">
                <span>Notice</span>
            </li>
            <li class="breadcrumb-item">
                <span>Create</span>
            </li>
        </ol>
        <a href="{{ route('admin.hrm.notice.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hrm.notice.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="notice_title"><b>Notice Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="notice_title" id="notice_title" class="form-control"
                                    value="{{ old('notice_title') }}">
                                @if ($errors->has('notice_title'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_title') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="notice_date"><b>Notice Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="notice_date" id="notice_date"class="form-control"
                                    value="{{ old('notice_date') }}">
                                @if ($errors->has('notice_date'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_date') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option >--Select Status--</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="department_id"><b>Department</b><span class="text-danger">*</span></label>
                                    <select name="department_id[]" id="department_id"class="form-control department_id"
                                        style="min-height:30px; width: 100%;" multiple="multiple">
                                        <option value="0" id="all-department">All Department</option>
                                        @forelse ($departments as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @empty
                                            <option>No Department</option>
                                        @endforelse
                                    </select>
                                    @error('department_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="employee_id"><b>Employee</b><span class="text-danger">*</span></label>
                                    <select name="expense_by_id[]" id="employee_id" class="form-control select2" style="min-height:30px; width: 100%;" multiple="multiple">
                                    </select>
                                    @error('expense_by_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Meeting</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-meeting-check-btn" value="">
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 meeting" style="display:none">
                                <label for="meeting_date"><b>Meeting Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="meeting_date[]" id="meeting_date" class="form-control"
                                    value="{{ old('meeting_date') }}">
                                @if ($errors->has('meeting_date'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_title') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 meeting" style="display:none">
                                <label for="meeting_time"><b>Meeting Time</b><span class="text-danger">*</span></label>
                                <input type="time" name="meeting_time[]" id="meeting_time"class="form-control"
                                    value="{{ old('meeting_time') }}">
                                @if ($errors->has('meeting_time'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_date') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 meeting" style="display:none">
                                <label for="meeting_purpose"><b>Meeting Purpose</b><span class="text-danger">*</span></label>
                                <input type="text" name="meeting_purpose[]" id="meeting_purpose"class="form-control"
                                    placeholder="Enter Meeting Purpose"
                                    value="{{ old('meeting_purpose') }}">
                                @if ($errors->has('meeting_purpose'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_date') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-5 mb-2 meeting" style="display:none">
                                <label for="meeting_link"><b>Meeting Link</b><span class="text-danger">*</span></label>
                                <input type="text" name="meeting_link[]" id="meeting_link"class="form-control"
                                    placeholder="Enter Meeting Link"
                                    value="{{ old('meeting_link') }}">
                                @if ($errors->has('meeting_link'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_date') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-sm-1 meeting" style="display:none">
                                <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                                    X
                                </button>
                            </div>
                            <div class="meetingRow meeting" style="display:none">

                            </div>
                            <div class="col-sm-4 meeting " style="display:none">
                                <button type="button" style="margin-top:0px" id="add_meeting"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Meeting
                                </button>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Document</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-document-check-btn" value="">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-2 document" style="display:none">
                                <div class="form-group">
                                    <label for="document_title"><b>Document Title</b></label>
                                    <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >
                                    @error('document_title')
                                        <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-5 mb-2 document" style="display:none">
                                <div class="form-group">
                                    <label for="document_file"><b>Document</b></label>
                                    <input class="form-control " type="file" placeholder="Document" name="document_file[]" value="">
                                    @error('document_file')
                                        <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-1 document" style="display:none">
                                <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                                    X
                                </button>
                            </div>
                            <div class="documentRow document" style="display:none">

                            </div>
                            <div class="col-sm-4 document " style="display:none">
                                <button type="button" style="margin-top:0px" id="add_document"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Document
                                </button>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description </b></label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                    placeholder="Description..."></textarea>
                                @error('description')
                                    <span class="alert text-danger" role="alert">
                                        {{ $message }}
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        //Document Start
        $(document).ready(function () {
            var wrapper = $(".documentRow");
            var x = 0;
            $("#add_document").click(function () {
                x++;
                $(wrapper).append('<div class="row mt-2 document-table-tr" id="document-table-tr-' + x + '">' +
                                '<div class="col-sm-6 mb-2 document">'+
                                        ' <div class="form-group">'+
                                            '<input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >'+
                                            '@error("document_title")'+
                                                '<span class="text-danger" role="alert">'+
                                                    ' <p>{{ $message }}</p>'+
                                                '</span>'+
                                            ' @enderror'+
                                        '</div>'+
                                    ' </div>'+
                                    '<div class="col-sm-5 mb-2">'+
                                            '<div class="form-group">'+
                                                '<input class="form-control" type="file" placeholder="Document" name="document_file[]" value="">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-sm-1 ">' +
                                            '<button type="button"  class=" btn btn-sm btn-danger " onclick="documentRemove(' + x + ')">' +
                                            'X' +
                                            '</button>' +
                                        '</div>'+
                                    '</div>' );

            });
        });

        $(document).on("click", "#add-document-check-btn", function () {
            if ($('#add-document-check-btn').is(":checked"))
                $(".document").show();
            else
                $(".document").hide();
        });

        function documentRemove(id) {
            $('#document-table-tr-' + id).remove();
        }
        // Document End


        // Meeting Start
        $(document).ready(function () {
            var wrapper2 = $(".meetingRow");
            var y = 0;
            $("#add_meeting").click(function () {
                y++;
                $(wrapper2).append('<div class="row mt-2 meeting-table-tr" id="meeting-table-tr-' + y + '">' +
                    '<div class="form-group col-12 col-sm-12 col-md-6 mb-2"> '+
                        '<label for="meeting_date"><b>Meeting Date</b><span class="text-danger">*</span></label> '+
                        '<input type="date" name="meeting_date[]" id="meeting_date" class="form-control" '+
                            ' value="{{ old("meeting_date") }}"> '+
                            '@if ($errors->has("meeting_date")) '+
                                '<span class="alert text-danger"> '+
                                    '{{ $errors->first("meeting_date") }} '+
                                '</span> '+
                            '@endif '+
                    '</div> '+

                    '<div class="form-group col-12 col-sm-12 col-md-6 mb-2"> '+
                        '<label for="meeting_time"><b>Meeting Time</b><span class="text-danger">*</span></label> '+
                        '<input type="time" name="meeting_time[]" id="meeting_time" class="form-control" '+
                            ' value="{{ old("meeting_time") }}"> '+
                            '@if ($errors->has("meeting_time")) '+
                                '<span class="alert text-danger"> '+
                                    '{{ $errors->first("meeting_time") }} '+
                                '</span> '+
                            '@endif '+
                    '</div> '+

                    '<div class="form-group col-12 col-sm-12 col-md-6 mb-2"> '+
                        '<label for="meeting_purpose"><b>Meeting Purpose</b><span class="text-danger">*</span></label> '+
                        '<input type="text" name="meeting_purpose[]" id="meeting_purpose" class="form-control" '+
                            ' value="{{ old("meeting_purpose") }}"> '+
                            '@if ($errors->has("meeting_purpose")) '+
                                '<span class="alert text-danger"> '+
                                    '{{ $errors->first("meeting_purpose") }} '+
                                '</span> '+
                            '@endif '+
                    '</div> '+

                    '<div class="form-group col-12 col-sm-12 col-md-5 mb-2"> '+
                        '<label for="meeting_link"><b>Meeting Link</b><span class="text-danger">*</span></label> '+
                        '<input type="text" name="meeting_link[]" id="meeting_link" class="form-control" '+
                            ' value="{{ old("meeting_link") }}"> '+
                            '@if ($errors->has("meeting_link")) '+
                                '<span class="alert text-danger"> '+
                                    '{{ $errors->first("meeting_link") }} '+
                                '</span> '+
                            '@endif '+
                    '</div> '+
                    '<div class="col-sm-1 col-md-1">' +
                        '<button type="button"  class=" btn btn-sm btn-danger " onclick="meetingRemove(' + y + ')">' +
                        'X' +
                        '</button>' +
                    '</div>'+
                '</div>' );

            });
        });

        $(document).on("click", "#add-meeting-check-btn", function () {
            if ($('#add-meeting-check-btn').is(":checked"))
                $(".meeting").show();
            else
                $(".meeting").hide();
        });

        function meetingRemove(id) {
            $('#meeting-table-tr-' + id).remove();
        }
        // Metting End

        CKEDITOR.replace('description', {
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"]},
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links", "groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo", "groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });


        $('#department_id').select2();
        $('#employee_id').select2();
        // dept
        $('#department_id').on('change', function() {
            changeEmployee();
            let value = $(this).val();
            console.log(value.includes("0"))
            if(value.includes("0")){
                $(this).empty();
                $(this).append('<option selected value="0">All Department</option>');
            }
            if(value == ''){
                changeDepartment();
            }

        });

        //employee
        $("#employee_id").on('change', function (){
            let value = $(this).val();
            console.log(value.includes("0"))
            if(value.includes("0")){
                $(this).empty();
                $(this).append('<option selected value="0">All Employee</option>');
            }
           if(value == ''){
               changeEmployee();
           }
        })

        function changeEmployee(){
            let department_id = $('#department_id').val();
            $("#employee_id").empty();
            $.ajax({
                url: "{{ route('admin.hrm.getDepartmentWiseEmployee') }}",
                type: 'post',
                data: { departmentId: department_id},
                success: function(response) {
                    $("#employee_id").append('<option value="0">All Employee</option>');
                    $.each(response, function(key, value) {
                        console.log(value.id)
                        $("#employee_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }

        function changeDepartment(){
            let department_id = $('#department_id').val();
            $("#employee_id").empty();
            $.ajax({
                url: "{{ route('admin.hrm.getDepartment') }}",
                type: 'post',
                data: { departmentId: department_id},
                success: function(response) {
                    $.each(response, function(key, value) {
                        console.log(value.id)
                        $("#department_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }
    </script>
@endpush
