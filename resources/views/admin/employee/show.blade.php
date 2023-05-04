@extends('layouts.dashboard.app')

@section('title', 'Employee')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
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
                <a href="{{ route('admin.employee.index') }}">Employee</a>
            </li>
        </ol>

        <h4 style="color: #0d6efd">{{$employee->name}}</h4>

        <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection


@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.employee.employee-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('basic')" id="v-pills-basic-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-basic" type="button" role="tab" aria-controls="v-pills-basic" aria-selected="true">Basic</a>
                        <a class="nav-link" onclick="setTab('documents')" id="v-pills-documents-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-documents" type="button" role="tab" aria-controls="v-pills-documents" aria-selected="false">Documents</a>
                        <a class="nav-link" onclick="setTab('identity')" id="v-pills-identity-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-identity" type="button" role="tab" aria-controls="v-pills-identity" aria-selected="false">Identity</a>
                        <a class="nav-link" onclick="setTab('address')" id="v-pills-address-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-address" type="button" role="tab" aria-controls="v-pills-address" aria-selected="false">Address</a>
                        <a class="nav-link" onclick="setTab('qualifications')" id="v-pills-qualifications-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-qualifications" type="button" role="tab" aria-controls="v-pills-qualifications" aria-selected="false">Qualifications</a>
                        <a class="nav-link" onclick="setTab('work-experience')" id="v-pills-work-experience-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-work-experience" type="button" role="tab" aria-controls="v-pills-work-experience" aria-selected="false">Work Experience</a>
                        <a class="nav-link" onclick="setTab('certification')" id="v-pills-certification-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-certification" type="button" role="tab" aria-controls="v-pills-certification" aria-selected="false">Certification</a>
                        <a class="nav-link" onclick="setTab('reference')" id="v-pills-reference-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-reference" type="button" role="tab" aria-controls="v-pills-reference" aria-selected="false">Reference</a>
                        <a class="nav-link" onclick="setTab('employee-bank')" id="v-pills-employee-bank-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-employee-bank" type="button" role="tab" aria-controls="v-pills-employee-bank" aria-selected="false">Bank accounts</a>
                    </div>

                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-basic" role="tabpanel" aria-labelledby="v-pills-basic-tab" tabindex="0">
                            @include('admin.employee.partial.basic')
                        </div>
                        <div class="tab-pane fade" id="v-pills-documents" role="tabpanel" aria-labelledby="v-pills-documents-tab" tabindex="0">
                            @include('admin.employee.partial.documents')
                        </div>
                        <div class="tab-pane fade" id="v-pills-identity" role="tabpanel" aria-labelledby="v-pills-identity-tab" tabindex="0">
                            @include('admin.employee.partial.identity')
                        </div>
                        <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab" tabindex="0">
                            @include('admin.employee.partial.address')
                        </div>
                        <div class="tab-pane fade" id="v-pills-qualifications" role="tabpanel" aria-labelledby="v-pills-qualifications-tab" tabindex="0">
                            @include('admin.employee.partial.qualifications')
                        </div>
                        <div class="tab-pane fade" id="v-pills-work-experience" role="tabpanel" aria-labelledby="v-pills-work-experience-tab" tabindex="0">
                            @include('admin.employee.partial.experience')
                        </div>
                        <div class="tab-pane fade" id="v-pills-certification" role="tabpanel" aria-labelledby="v-pills-certification-tab" tabindex="0">
                            @include('admin.employee.partial.certificates')

                        </div>
                        <div class="tab-pane fade" id="v-pills-reference" role="tabpanel" aria-labelledby="v-pills-reference-tab" tabindex="0">
                            @include('admin.employee.partial.reference')
                        </div>
                        <div class="tab-pane fade" id="v-pills-employee-bank" role="tabpanel" aria-labelledby="v-pills-employee-bank-tab" tabindex="0">
                            @include('admin.employee.partial.bank-account')
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
            removeButtons: 'Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })
    }

</script>

@endpush
