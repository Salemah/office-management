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
                <a href="{{ route('admin.employee.index') }}">Project</a>
            </li>
        </ol>

        <h4 style="color: #0d6efd">{{$project->project_title}}</h4>

        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection


@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.project.project.show.project-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('basic')" id="v-pills-basic-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-basic" type="button" role="tab" aria-controls="v-pills-basic" aria-selected="true">Basic</a>
                        <a class="nav-link" onclick="setTab('documents')" id="v-pills-documents-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-documents" type="button" role="tab" aria-controls="v-pills-documents" aria-selected="false">Documents</a>
                        <a class="nav-link" onclick="setTab('identity')" id="v-pills-identity-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-identity" type="button" role="tab" aria-controls="v-pills-identity" aria-selected="false">Requirement</a>
                        <a class="nav-link" onclick="setTab('link')" id="v-pills-link-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-link" type="button" role="tab" aria-controls="v-pills-link" aria-selected="false">Link</a>
                    </div>

                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-basic" role="tabpanel" aria-labelledby="v-pills-basic-tab" tabindex="0">
                            @include('admin.project.project.show.partial.basic')
                        </div>
                        <div class="tab-pane fade " id="v-pills-documents" role="tabpanel"aria-labelledby="v-pills-documents-tab" tabindex="0">
                            @include('admin.project.project.show.partial.documents')
                        </div>
                        <div class="tab-pane fade " id="v-pills-link" role="tabpanel"aria-labelledby="v-pills-link-tab" tabindex="0">
                            @include('admin.project.project.show.partial.link')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>

    $(document).ready(function(){
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#v-pills-'+activeTab+'-tab').addClass('active');
            $('#v-pills-'+activeTab).addClass('show active');
        }else{
            $('#v-pills-basic-tab').addClass('active');
            $('#v-pills-basic').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTab', params);
    }

    function ckEditor(id){
        CKEDITOR.replace( id,{
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
    }

</script>

@endpush
