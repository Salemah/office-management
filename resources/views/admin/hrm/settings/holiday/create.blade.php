@extends('layouts.dashboard.app')

@section('title', 'Create Holiday')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
             <a href="{{route('admin.hrm.setting.holiday.index')}}">Holiday</a>
            </li>
        </ol>
        <a href="{{ route('admin.hrm.setting.holiday.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.hrm.setting.holiday.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                     <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="title"><b>Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}" placeholder="Enter Title">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option >--Select Status--</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="type"><b>Type</b><span class="text-danger">*</span></label>
                                <select name="type" id="type"
                                        class="form-select @error('type') is-invalid @enderror">
                                    <option >--Select Type--</option>
                                    <option value="1">Single Day</option>
                                    <option value="2">Multiple Days</option>
                                </select>
                                @error('type')
                                <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="single_date" class="single_date"><b>Single Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="single_date" id="single_date"
                                    class="single_date  form-control @error('single_date') is-invalid @enderror"
                                    value="{{ old('single_date') }}" placeholder="Enter Date">
                                @error('single_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="start_date" class="start_date"><b>Start Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date"
                                    class="start_date form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" placeholder="Enter Start Date">
                                @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="end_date" class="end_date"><b>End Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" 
                                    class="end_date form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" placeholder="Enter End Date">
                                @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                    placeholder="Description..."></textarea>
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

        $('.single_date').hide();
        $('.start_date').hide();
        $('.end_date').hide();

        $('#type').on('change', function() {
            let type = $(this).val();
            if(type == 1){
                $('.start_date').hide();
                $('.end_date').hide();
                $('.single_date').show();
            }

            if(type == 2){
                $('.single_date').hide();
                $('.start_date').show();
                $('.end_date').show();

                var startDate = new Date($('.start_date').val());
                var endDate   = new Date($('.end_date').val());

                if (startDate < endDate){
                    console.log('Start must be less than the end date!!!');
                }
            }

        });


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
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>
@endpush
