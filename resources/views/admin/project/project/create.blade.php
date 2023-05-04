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
                <a href="{{ route('admin.projects.index') }}">Project</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.create') }}">Create</a>
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_code"><b>Project Code</b><span class="text-danger">*</span></label>
                                <input type="text" name="project_code" id="project_code"class="form-control @error('project_code') is-invalid @enderror"value="WTL-{{Carbon\Carbon::parse(date('m/d'))->format('M-d').'-P-00'.$projects}}" placeholder="Enter Project Code" readonly>
                                @error('project_code')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="project_title"><b>Project Title</b></label>
                                <input type="text" name="project_title" id="project_title"class="form-control @error('project_title') is-invalid @enderror"value="{{ old('project_title') }}" placeholder="Enter Project Title">
                                @error('project_title')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            {{-- <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="reporting_person"><b>Select Reporting Person</b><span class="text-danger">*</span></label>
                                    <select name="reporting_person" id="reporting_person"class="form-control select2" style="min-height:30px" >
                                        <option>--Select Reporting Person--</option>
                                    </select>
                                    @error('reporting_person')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> --}}
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
                                <select name="client" id="client" class="form-select @error('client') is-invalid @enderror" >
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
                                    @foreach ($priorities as $key => $priority )
                                        <option value="{{$priority->id}}">{{$priority->name}}</option>
                                    @endforeach
                                </select>
                                @error('project_priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="project_date"><b>Project Date</b><span class="text-danger">*</span></label>
                                    <input type="date" name="project_date" id="project_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" placeholder="Invoice Date" required>
                                    @error('project_date')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="amount"> <b>Project Amount </b><span class="text-danger">*</span></label>
                                <input type="number"  id="amount" step="any" value="{{ old('amount')}}" class="form-control @error('amount') is-invalid @enderror" name="amount" placeholder="Enter Amount" >
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                 </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="amount_type"> <b> Amount Type</b></label>
                                <select name="amount_type" id="amount_type" class="form-select" onchange="expenseTransactionWay()">
                                    <option value=""  selected>--Select Amount Type--</option>
                                    <option value="1" >Full</option>
                                    <option value="2" >Partial</option>
                                </select>
                                @error('amount_type')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="receipt-amount"> <b>Receipt Amount </b><span class="text-danger">*</span></label>
                                <input type="number"  id="receipt-amount" value="{{ old('receipt-amount')}}" class="form-control " name="receipt_amount" placeholder="Enter Receipt Amount" onkeyup="checkAmount()" >
                                @error('receipt_amount')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                 </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="revenue_invoice_no"><b>Recive By</b><span class="text-danger">*</span></label>
                                <select name="receive_by_id" id="receive_by_id"class="form-control select2" >
                                    <option>--Select Employee--</option>
                                </select>
                                @error('receive_by_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 transaction" >
                                <label for="transaction_way"><b>Transaction Type</b></label>
                                <select name="transaction_way" id="transaction_way" class="form-control"
                                        onchange="expenseTransactionWay()">
                                    <option value="" selected>-- Select --</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Bank</option>
                                </select>
                                @if ($errors->has('transaction_way'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('transaction_way') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash" style="display: none">
                                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                                    style="display: none"></span></label>
                                <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                    <option  value=""  selected>--Select Cash Account--</option>
                                    @foreach ($cash_account as $account)
                                         <option value="{{$account->id}}" >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('cash_account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way  "
                                 style="display: none">
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="balance"
                                    style="display: none"></span></label>
                                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                            | {{ $bankAccount->account_number }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('account_id'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('account_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way "
                                 style="display: none">
                                <label for="cheque_number"><b>Cheque Number/Transaction</b></label>
                                <input type="text" name="cheque_number" id="cheque_number"
                                       class="form-control" value="{{ old('cheque_number') }}" placeholder=" ...">
                                @if ($errors->has('cheque_number'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('cheque_number') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                                    <option>--Select Status--</option>
                                    <option value="1" selected>Up Coming</option>
                                    <option value="2">On Going</option>
                                    <option value="3">Complete</option>
                                    <option value="4">Cancel</option>
                                    <option value="5">On Hold</option>
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
        // $('#reporting_person').select2({
        //             height:30,
        //             ajax: {
        //                 url: '{{route('admin.expense.employee.search')}}',
        //                 dataType: 'json',
        //                 type: "POST",
        //                 data: function (params) {
        //                     var query = {
        //                         search: params.term,
        //                         type: 'public'
        //                     }
        //                     return query;
        //                 },
        //                 processResults: function (data) {
        //                     console.log();
        //                     // Transforms the top-level key of the response object from 'items' to 'results'
        //                     return {
        //                         results: $.map(data, function (item) {
        //                             return {
        //                                 text: item.name,
        //                                 value: item.id,
        //                                 id: item.id,
        //                             }
        //                         })
        //                     };
        //                 }
        //             }
        // });
        $('#receive_by_id').select2({
                ajax: {
                    url: '{{route('admin.expense.employee.search')}}',
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
         //document append
         $(document).ready(function () {
            var wrapper = $(".documentRow");
            var x = 0;
            $("#add_document").click(function () {
                    x++;
                    $(wrapper).append('<div class="row mt-2 document-table-tr" id="document-table-tr-' + x + '">' +
                                            '<div class="col-sm-6 mb-2 document">'+
                                                ' <div class="form-group">'+
                                                        '<input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >'+
                                                        '@error("document_title")'+
                                                            '<span class="text-danger" role="alert">'+
                                                            ' <p>{{ $message }}</p>'+
                                                            '</span>'+
                                                    ' @enderror'+
                                                    '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-5 mb-2">'+
                                                '<div class="form-group">'+
                                                    '<input type="file" id="document" data-height="25"data-default-file=""class="dropify form-control " name="documents[]">'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-1 ">' +
                                                '<button type="button"  class=" btn btn-sm btn-danger " onclick="documentRemove(' + x + ')">' +
                                                'X' +
                                                '</button>' +
                                            '</div>'+
                                        '</div>' );
                                        $('.dropify').dropify();

            });
        });
        function expenseTransactionWay() {
            var transaction_way = $("#transaction_way").val();
            if (transaction_way == 2) {
                $('.bank-way').show();
                $('#cash_account_id').val('');
                $('.cash').hide();
            } else if(transaction_way == 1) {
                 $('.bank-way').hide();
                 $('#account_id').val('');
                 $('.cash').show();
            }
             else{
                $('.bank-way').hide();
                $('.cash').hide();
                $('#account_id').val('');
             }
        };
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
                    url: '{{route('admin.crm.client.search.all')}}',
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
