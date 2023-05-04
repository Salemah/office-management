@extends('layouts.dashboard.app')

@section('title', 'Accounts')
@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Project</span>
            </li>
            <li class="breadcrumb-item">
                <span>Budget</span>
            </li>
            <li class="breadcrumb-item">
                <span>Edit</span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$project->project_title}}</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.account-budget.update',$projectBudget->id) }}"
                method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                            <label for="amount"> <b>Project Amount </b><span class="text-danger">*</span></label>
                            <input type="number"  id="amount" value="{{$projectBudget->amount, old('amount')}}" class="form-control " name="amount" placeholder="Enter Amount" >
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 mb-2">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" id="description" rows="10" cols="40" class="form-control description" placeholder="Description...">{!!$projectBudget->description!!}
                            </textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group my-3">
                            <button type="submit" class="btn btn-sm btn-primary mb-3">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace(description,{
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
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })
    </script>
@endpush
