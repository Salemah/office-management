@extends('layouts.dashboard.app')

@section('title', 'Trsansaction Show')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.account.transaction.index') }}">Trsansaction Show</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.account.transaction.index') }}">Show</a>
            </li>
        </ol>
        <a href="{{ route('admin.account.transaction.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.account.transaction.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_date"><b>Transaction Date</b><span class="text-danger">*</span></label>
                                <input type="text" name="transaction_date" id="transaction_date"class="form-control "value="{{$transaction->transaction_date}}" readonly>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_title"><b>Transaction Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="transaction_title" id="transaction_title"class="form-control "value="{{$transaction->transaction_title}}" readonly>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_way"><b>Transaction Way</b><span class="text-danger">*</span></label>
                                <input type="text" name="transaction_way" id="transaction_way"class="form-control "value="{{$transaction_way}}" readonly>
                            </div>
                            @if ($transaction->bankAccount)
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="bank"><b>Bank </b><span class="text-danger">*</span></label>
                                    <input type="text" name="bank" id="bank"class="form-control "value="{{$transaction->bankAccount->bank->bank_name}}" readonly>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="bank_account"><b>Bank Account  Name</b><span class="text-danger">*</span></label>
                                    <input type="text" name="bank_account" id="bank_account"class="form-control "value="{{$transaction->bankAccount->name}}" readonly>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="account_no"><b>Account No</b><span class="text-danger">*</span></label>
                                    <input type="text" name="account_no" id="account_no"class="form-control "value="{{$transaction->bankAccount->account_number}}" readonly>
                                </div>
                            @endif
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_purpose"><b>Transaction Purpose</b><span class="text-danger">*</span></label>

                                <input type="text" name="transaction_purpose" id="transaction_purpose"class="form-control "value="{{$transaction_purpose}}" readonly>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="amount"><b>Amount</b><span class="text-danger">*</span></label>
                                <input type="text" name="amount" id="amount"class="form-control "value="{{$transaction->amount}}" readonly>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="created_by"><b>Added By</b><span class="text-danger">*</span></label>
                                <input type="text" name="created_by" id="created_by"class="form-control "value="{{$transaction->createdByUser->name}}" readonly>
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
