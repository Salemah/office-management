@extends('layouts.dashboard.app')

@section('title', 'Edit Notice')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .text-danger strong{
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title{
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

        @media (min-width:200px ) and (max-width:1130px ) {
            .responsive-table{
                width: 100%;
            }

            .responsive-table th{
                display: none;
            }

            .responsive-table .responsive-table-tr{
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .responsive-table tr td{
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title{
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
                <a href="{{route('admin.hrm.notice.index')}}">Noticce</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.hrm.notice.edit',$notice->id)}}">Edit</a>
            </li>
        </ol>
        <a href="{{route('admin.hrm.notice.index')}}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('admin.hrm.notice.update', $notice->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="notice_title"><b>Notice Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="notice_title" id="notice_title" class="form-control"
                                    value="{{ $notice->notice_title }}">
                                @if ($errors->has('notice_title'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('notice_title') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="notice_date"><b>Notice Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="notice_date" id="notice_date"class="form-control"
                                    value="{{ $notice->notice_date }}">
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
                                    <option value="1" {{ $notice->status== 1 ? 'selected': '' }}>Active</option>
                                    <option value="0" {{ $notice->status== 0 ? 'selected': '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <span class="alert text-danger" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-sm-4 col-md-6">
                                <div class="form-group">
                                    <label for="expense_invoice_no"><b>Select Department</b><span class="text-danger">*</span></label>
                                    @php
                                        $departmentIds  =  json_decode($notice->department_id);
                                    @endphp
                                    <select class="form-control expense_by" id="department_id" name="department_id[]" multiple="multiple"    required>
                                        <option value="0" @if (in_array("0", $departmentIds)) selected @endif>
                                            All Department
                                        </option>
                                        @foreach($departments as $department)
                                            <option value="{{$department->id}}"
                                                @if(in_array($department->id, $departmentIds))
                                                    {{ "selected" }}
                                                @endif
                                            >
                                                {{$department->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('expense_by_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-4 col-md-6">
                                <div class="form-group">
                                    <label for="expense_invoice_no"><b>Select Employee</b><span class="text-danger">*</span></label>
                                    @php
                                        $employeeIds  =  json_decode($notice->employee_id);
                                    @endphp
                                    <select class="form-control expense_by" id="employee_id" name="expense_by_id[]" multiple="multiple" required>
                                        <option>Select Option</option>
                                        <option value="0" @if (in_array("0", $employeeIds)) selected @endif>
                                            All Employee
                                        </option>
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}"
                                                @if(in_array($employee->id, $employeeIds))
                                                    {{ "selected" }}
                                                @endif
                                            >
                                                {{$employee->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('expense_by_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                        </div>

                        {{-- Notice --}}
                        <div class="">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Meeting</b></label>
                                   {{-- <input class="form-check-input" type="checkbox" id="add-meeting-check-btn" value="" {{ $meetings ? 'checked' : '' }}> --}}
                                </div>
                            </div>
                            @if(isset($meetings) && count($meetings) > 0)
                                @foreach ($meetings['date'] as $key => $meeting)
                                    <div class="row" id="remove_row_meeting">
                                        <div class="col-sm-6 mb-2 col-md-6 meeting">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="meeting_date"><b>Meeting Date</b></label>
                                                @endif
                                                <input class="form-control " type="date" placeholder="Meeting Title.."
                                                    name="meeting_date[]" value="{{ $meetings['date'][$key] }}">
                                                @error('meeting_date')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 mb-2 col-md-6 meeting">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="meeting_time"><b>Meeting Time</b></label>
                                                @endif
                                                <input class="form-control " type="time" placeholder="Meeting Title.."
                                                    name="meeting_time[]" value="{{ $meetings['time'][$key] }}">
                                                @error('meeting_time')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 mb-2 col-md-6 meeting">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="meeting_purpose"><b>Meeting Purpose</b></label>
                                                @endif
                                                <input class="form-control " type="text" placeholder="Meeting Title.."
                                                    name="meeting_purpose[]" value="{{ $meetings['purpose'][$key] }}">
                                                @error('meeting_purpose')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 mb-2 col-md-5 meeting">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="meeting_link"><b>Meeting Link</b></label>
                                                @endif
                                                <input class="form-control " type="text" placeholder="Meeting Title.."
                                                    name="meeting_link[]" value="{{ $meetings['link'][$key] }}">
                                                @error('meeting_link')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-1 col-md-1 meeting ">
                                            <button type="button" @if($key == 0) style="margin-top:24px" @endif  class=" btn btn-sm btn-danger  btn_remove_meeting" >
                                                X
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row">
                                    <div class="col-sm-6 mb-2 col-md-6 meeting" style="display:none">
                                        <div class="form-group">
                                            <label for="meeting_date"><b>Meeting Date</b></label>
                                            <input class="form-control" type="date" placeholder="Meeting Date.." name="meeting_date[]" value="" >
                                            @error('meeting_date')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 mb-2 col-md-6 meeting" style="display:none">
                                        <div class="form-group">
                                            <label for="meeting_time"><b>Meeting Time</b></label>
                                            <input class="form-control" type="time" placeholder="Meeting Time.." name="meeting_time[]" value="" >
                                            @error('meeting_time')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 mb-2 col-md-6 meeting" style="display:none">
                                        <div class="form-group">
                                            <label for="meeting_purpose"><b>Meeting Purpose</b></label>
                                            <input class="form-control" type="text" placeholder="Meeting Time.." name="meeting_purpose[]" value="" >
                                            @error('meeting_purpose')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 mb-2 col-md-6 meeting" style="display:none">
                                        <div class="form-group">
                                            <label for="meeting_link"><b>Meeting Link</b></label>
                                            <input class="form-control" type="text" placeholder="Meeting Time.." name="meeting_link[]" value="" >
                                            @error('meeting_link')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-1 col-md-1 meeting" style="display:none">
                                        <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                                            X
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="meetingRow meeting" >

                            </div>
                            <div class="col-sm-4 meeting " @if ($meetings == null ) style="display: none" @endif >
                                <button type="button" style="margin-top:0px" id="add_meeting"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Meeting
                                </button>
                            </div>
                        </div>

                        {{-- Document --}}
                        <div class="">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Document</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-document-check-btn" value="" {{$documents ? 'checked' : ''}}>
                                </div>
                            </div>
                            @if(isset($documents) && count($documents) > 0)
                                @foreach($documents as $key => $document)
                                    <div class="row" id="remove_row_document">
                                        <div class="col-sm-6 mb-2 document">
                                            <div class="form-group">
                                                @if($key == 0)
                                                     <label for="document_title"><b>Document Title</b></label>
                                                @endif
                                                <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" value="{{$documents_title[$key]}}" >
                                                @error('document_title')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-5 mb-2 document">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="document"><b>Document</b></label>
                                                @endif
                                                <input class="form-control" type="file" placeholder="Document" name="documents[]" value="">
                                                    @error('documents')
                                                        <span class="text-danger" role="alert">
                                                            <p>{{ $message }}</p>
                                                        </span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-1 document ">
                                            <button type="button" @if($key == 0) style="margin-top:24px" @endif  class=" btn btn-sm btn-danger  btn_remove_document" >
                                                X
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                            <div class="row">
                                <div class="col-sm-6 mb-2 document " style="display:none">
                                    <div class="form-group">
                                        <label for="document_title"><b>Document Title</b></label>
                                        <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" value="" >
                                        @error('document_title')
                                            <span class="text-danger" role="alert">
                                                <p>{{ $message }}</p>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-5 mb-2 document" style="display:none">
                                    <div class="form-group">
                                        <label for="document"><b>Document</b></label>
                                        <input class="form-control " type="file" placeholder="Document" name="documents[]" value="">
                                        @error('documents')
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
                           </div>
                            @endif
                            <div class=" documentRow document" >

                            </div>
                            <div class="col-sm-4 document " @if ($documents == null ) style="display: none" @endif >
                                <button type="button" style="margin-top:0px" id="add_document"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Document
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description"><b>description</b><span class="text-red"></span></label>
                                    <textarea name="description" id="description" class="form-control" cols="30" rows="5"
                                              placeholder="Add a description">{{ $notice->description }}</textarea>
                                    <div class="help-block with-errors"></div>
                                    @error('description')
                                    <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Update Notice</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

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

        

        //document append
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
                                        '</div>'+
                                            '<div class="col-sm-5 mb-2">'+
                                                '<div class="form-group">'+
                                                    '<input class="form-control" type="file" placeholder="Document" name="documents[]" value="">'+
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
        };

        $(document).on('click', '.btn_remove_document', function () {
            $(this).parents('#remove_row_document').remove();
        });


        //meeting append
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

        $(document).on('click', '.btn_remove_meeting', function () {
            $(this).parents('#remove_row_meeting').remove();
        });

        function meetingRemove(id) {
            $('#meeting-table-tr-' + id).remove();
        }

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

