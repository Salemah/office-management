@extends('layouts.dashboard.app')

@section('title', 'Dashboard Setting')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
        integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;

        }
    </style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
                <span>Dashboard Setting </span>
            </li>
            <li class="breadcrumb-item">
                <span>Create/Update </span>
            </li>
        </ol>

    </nav>
@endsection

@section('content')
    <!-- Alert -->
    {{-- @include('layouts.dashboard.partials.alert') --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.dashboard.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="system_title">System Title<span class="text-danger">*</span></label>
                                <input type="text" name="system_title" id="system_title"
                                    class="form-control @error('system_title') is-invalid @enderror"
                                    value="{{ isset($DashboardSetting) ? $DashboardSetting->system_title : old('system_title') }} "
                                    placeholder="Enter System Title">
                                @error('system_title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <input type="hidden" name="id"
                                value="{{ isset($DashboardSetting) ? $DashboardSetting->id : ' ' }}">

                            <div class="form-group col-6 col-sm-6 col-md-6 mb-2">
                                <label for="logo">Upload Logo<span class="text-danger">*</span></label>
                                <input type="file" id="logo" data-height="290"
                                    @if ($DashboardSetting) data-default-file="{{ asset('img/' . $DashboardSetting->logo) }}" @endif
                                    class="dropify form-control @error('logo') is-invalid @enderror" name="logo">
                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-6 col-sm-6 col-md-6 mb-2">
                                <label for="favicon">Upload Favicon<span class="text-danger">*</span></label>
                                <input type="file" id="favicon" data-height="290"
                                    @if ($DashboardSetting) data-default-file="{{ asset('img/' . $DashboardSetting->favicon) }}" @endif
                                    class="dropify form-control @error('favicon') is-invalid @enderror" name="favicon">
                                @error('favicon')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group col-12 mb-2">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="10" cols="40"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Description...">{{ isset($DashboardSetting) ? $DashboardSetting->description : old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <button type="submit"
                                    class="btn btn-sm btn-primary">{{ isset($DashboardSetting) ? 'Update' : 'Create' }}</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
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
            removeButtons: 'Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>
    <script></script>
@endpush
