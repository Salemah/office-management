@extends('layouts.dashboard.app')

@section('title', 'Client ')
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
                <span>Client </span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$Client->name}}</h4>
        <a href="{{ route('admin.crm.client.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.crm.client.client-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('comment')" id="v-pills-comment-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-comment" type="button" role="tab" aria-controls="v-pills-comment" aria-selected="true">Comment</a>
                        <a class="nav-link" onclick="setTab('addcomment')" id="v-pills-addcomment-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-addcomment" type="button" role="tab" aria-controls="v-pills-addcomment" aria-selected="false">Add Comment</a>
                    </div>
                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-comment" role="tabpanel" aria-labelledby="v-pills-comment-tab" tabindex="0">
                            @include('admin.crm.client.comment.partial.comments')
                        </div>
                        <div class="tab-pane fade " id="v-pills-addcomment" role="tabpanel" aria-labelledby="v-pills-addcomment-tab" tabindex="0">
                            @include('admin.crm.client.comment.partial.add-comment')
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
            $('#v-pills-comment-tab').addClass('active');
            $('#v-pills-comment').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTabClient', params);
    }
    </script>
@endpush
