@extends('layouts.dashboard.app')

@section('title', 'Project')

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
                <a href="{{ route('admin.projects.index') }}">Expense Category</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.create') }}">Create</a>
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_code"><b>Project Code</b><span class="text-danger">*</span></label>
                                <input type="text" name="project_code" id="project_code"class="form-control @error('project_code') is-invalid @enderror"value="{{'P-'.$projects}}" placeholder="Enter Project Code" readonly>
                                @error('project_code')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_title"><b>Project Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="project_title" id="project_title"class="form-control @error('project_title') is-invalid @enderror"value="{{ old('project_title') }}" placeholder="Enter Project Title">
                                @error('project_title')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_category"><b>Project Category</b><span class="text-danger">*</span></label>
                                <select name="project_category" id="project_category"class="form-select @error('project_category') is-invalid @enderror">
                                    <option value="" selected>--Select Project Category--</option>
                                </select>
                                @error('project_category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_type"><b>Project Type</b><span class="text-danger">*</span></label>
                                <select name="project_type" id="project_type"class="form-select @error('project_type') is-invalid @enderror" onclick="projectType()">
                                    <option value="" selected>--Select Project Type--</option>
                                    <option value="1" >Own Project </option>
                                    <option value="2" >Client Project</option>
                                    <option value="3" >Public Project </option>
                                </select>
                                @error('project_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 client" style="display: none">
                                <label for="client"><b>Client</b></label>
                                <select name="client" id="client" class="form-select @error('client') is-invalid @enderror">
                                    <option value="" selected>--Select Client--</option>
                                </select>
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_priority"><b>Project Priority</b><span class="text-danger">*</span></label>
                                <select name="project_priority" id="project_priority" class="form-select @error('project_priority') is-invalid @enderror">
                                    <option value="" selected>--Select Project Priority--</option>
                                    <option value="1" >First</option>
                                    <option value="2" >Second</option>
                                    <option value="3" >Third</option>
                                </select>
                                @error('project_priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                                    <option>--Select Status--</option>
                                    <option value="1" selected>Not Start</option>
                                    <option value="2">On Going</option>
                                    <option value="3">Complete</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="department_id"><b>Department</b><span class="text-danger">*</span></label>
                                    <select name="department_id[]" id="department_id"class="form-control department_id" multiple="multiple">
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
                                <label for="employee_id"><b>Assign To</b><span class="text-danger">*</span></label>
                                <select name="employee_id[]" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" multiple="multiple">
                                    <option value="" >--Select Employee--</option>
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="start_date"><b>Start Date</b></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date" value="{{ old('start_date')}}">
                                @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="end_date"><b>End Date</b></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" id="end_date" value="{{ old('end_date')}}">
                                @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="total_day"><b>Estimated Day</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="total_day" value="{{ old('total_day')}}" id="total_day" placeholder="Enter Total Day" onkeyup="getTotalHour()" min="1">
                                @error('total_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="total_hour"><b>Estimated Hour</b><span class="text-danger">*</span></label>
                                    <input type="num" class="form-control @error('total_hour') is-invalid @enderror" name="total_hour" id="total_hour" placeholder="0.0" readonly>
                                    @error('total_hour')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            {{-- adjustment DAY --}}
                            <div class="row my-2">
                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label><b>Adjustment</b></label>
                                        <input class="form-check-input " type="checkbox" id="adjustment-btn" value="">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group adjustment" style="display: none">
                                        <label><b>Adjustment Type</b></label>
                                        <select class="form-control" id="adjustment_type" name="adjustment_type"
                                                onchange="adjustmentHourCount()">
                                            <option value="" selected>--Select--</option>
                                            <option value="1">Addition</option>
                                            <option value="2">Subtraction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group adjustment" style="display: none">
                                        <label><b>Adjustment hour</b></label>
                                        <input type="number" name="adjustment_hour" id="adjustment_hour"
                                               class="form-control " value="0" placeholder="0"
                                               onkeyup="adjustmentHourCount()">
                                        @error('adjustment_hour')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="final_hour"><b>Final Hour</b><span class="text-danger">*</span></label>
                                        <input type="num" class="form-control @error('final_hour') is-invalid @enderror" name="final_hour" id="final_hour" placeholder="0.0" readonly>
                                        @error('final_hour')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          value="{{ old('description') }}" placeholder="Description..."></textarea>
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
         $('#project_category').select2({
            ajax: {
                url: '{{route('admin.projects.category.search')}}',
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

        $('#client').select2({
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

        $('#department_id').select2({
            placeholder: 'Select Department',
        });
        $('#employee_id').select2({
            placeholder: 'Select Employee',
        });
        // dept
        $('#department_id').on('change', function() {
            changeEmployee();
            let value = $(this).val();
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
            if(value.includes("0")){
                $(this).empty();
                if($('#department_id').val() == 0)
                {$(this).append('<option selected value="0">All Employee</option>');}
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
                    if($('#department_id').val() == 0)
                    {$("#employee_id").append('<option value="0">All Employee</option>');}
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

        $(document).on("click", "#adjustment-btn", function () {
            if ($('#adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
                $('#adjustment_hour').val(0);
                getTotalHour() ;
        });
        function projectType() {
            var projectType = $("#project_type").val();
            if (projectType == 2) {
                $('.client').show();
            } else {
                $('.client').hide();
            }
        };
        function getTotalHour() {
            var totalDay = $("#total_day").val();
            $('#total_hour').val(totalDay*8);
            $('#final_hour').val(totalDay*8);
            $('#adjustment_hour').val(0);
            adjustmentHourCount();
        };
        function adjustmentHourCount() {
            var adjustmentType = $('#adjustment_type').val();
            var adjustmentHour = $('#adjustment_hour').val();
            var totaltHour = $("#total_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour)*8) + parseFloat(adjustmentHour);
                    $("#final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour)*8) - parseFloat(adjustmentHour)
                    $("#final_hour").val(finalBalance);
                }
            }
        }
        CKEDITOR.replace('description', {
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"] },
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links","groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo", "groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

    </script>
@endpush
