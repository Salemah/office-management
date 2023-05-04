@extends('layouts.dashboard.app')

@section('title', 'Project')
@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Project</span>
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
           <div class="card-header">
                @include('admin.project.project.show.project-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('module-create')" id="v-pills-module-create-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-module-create" type="button" role="tab" aria-controls="v-pills-module-create" aria-selected="false">Module</a>
                        <a class="nav-link" onclick="setTab('module-list')" id="v-pills-module-list-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-module-list" type="button" role="tab" aria-controls="v-pills-module-list" aria-selected="false">Module List</a>
                    </div>
                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-module-create" role="tabpanel"aria-labelledby="v-pills-module-create-tab" tabindex="0">
                            @include('admin.project.project.show.duration.module.create')
                        </div>
                        <div class="tab-pane fade " id="v-pills-module-list" role="tabpanel"aria-labelledby="v-pills-module-list-tab" tabindex="0">
                            @include('admin.project.project.show.duration.module.module-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function(){
        var activeTab = localStorage.getItem('activeTabClient');
        if(activeTab){
            $('#v-pills-'+activeTab+'-tab').addClass('active');
            $('#v-pills-'+activeTab).addClass('show active');
        }else{
            $('#v-pills-duration-tab').addClass('active');
            $('#v-pills-duration').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTabClient', params);
    }
    </script>
@endpush
