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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                        <a class="nav-link" onclick="setTab('add-task')" id="v-pills-add-task-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-add-task" type="button" role="tab" aria-controls="v-pills-add-task" aria-selected="false">Add</a>
                        <a class="nav-link" onclick="setTab('task-list')" id="v-pills-task-list-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-task-list" type="button" role="tab" aria-controls="v-pills-task-list" aria-selected="false">Task List</a>
                    </div>
                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-add-task" role="tabpanel"aria-labelledby="v-pills-add-task-tab" tabindex="0">
                            @include('admin.project.project.show.task.create')
                        </div>
                        <div class="tab-pane fade " id="v-pills-task-list" role="tabpanel"aria-labelledby="v-pills-task-list-tab" tabindex="0">
                            @include('admin.project.project.show.task.list')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
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
            $('#v-pills-add-task-tab').addClass('active');
            $('#v-pills-add-task').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTabClient', params);
    }
    </script>
@endpush
