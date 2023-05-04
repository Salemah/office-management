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
                <a href="{{ route('admin.projects.edit',$project->id) }}">Edit</a>
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
                                <select name="client[]" id="client" class="form-select @error('client') is-invalid @enderror" multiple="multiple">
                                    <option value="">--Select Client--</option>
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
            placeholder:'Select Client',
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

        function projectType() {
            var projectType = $("#project_type").val();
            if (projectType == 2) {
                $('.client').show();
            } else {
                $('.client').hide();
            }
        };


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
