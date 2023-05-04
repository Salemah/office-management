@extends('layouts.dashboard.app')

@section('title', 'Accounts')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
    integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <style>
        .text-danger strong{
            font-size: 11px;
        }
        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }
        .dropify-wrapper {
            border-radius: 6px;
        }
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
                <span>Project</span>
            </li>
            <li class="breadcrumb-item">
                <span>Receive</span>
            </li>
            <li class="breadcrumb-item">
                <span>Edit</span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$project->project_title}}</h4>
        <a href="{{route('admin.project.account-budget.view',$project->id)}}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <span class="badge rounded-pill bg-success fs-6"><span class="">Total Budget :</span> {{number_format($total_budget,2) }}</span>
                <span class="badge rounded-pill bg-success fs-6"><span class="">Due Amount:</span> {{number_format($total_due,2)}}</span>
                <form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.budget-receipt.update',$projectReceive->id) }}"
                     method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <input type="hidden" name="total_budget" id="total_budget" value="{{$total_budget}}">

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="amount-type"> <b> Amount Type</b><span class="text-danger">*</span></label>
                            <select name="amount_type" id="amount-type" class="form-select" onchange="expenseTransactionWay()">
                                <option value=""  selected>--Select Amount Type--</option>
                                <option value="1" {{$projectReceive->amount_type == 1 ? 'selected' : ''}} >Full</option>
                                <option value="2" {{$projectReceive->amount_type == 2 ? 'selected' : ''}}>Partial</option>
                            </select>
                            @error('amount_type')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="receipt_date"><b>Receipt Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="receipt_date" id="receipt_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" placeholder="Invoice Date" required>
                            @error('receipt_date')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="receipt-amount"> <b>Receive Amount </b><span class="text-danger">*</span></label>
                            <input type="number" step="any"  id="receipt-amount" value="{{$projectReceive->amount, old('receipt-amount')}}" class="form-control " name="receipt_amount" placeholder="Enter Receipt Amount" onkeyup="checkAmountt()" >
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
                                <option value="{{ $employee->id}}"@if( $employee->id ==  $projectReceive->received_by)  selected @endif>
                                    {{ $employee->name}}
                                </option>
                            </select>
                            @error('receive_by_id')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2  transaction"  >
                            <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
                                <select name="transaction_way" id="transaction_way" class="form-control"onchange="expenseTransactionWay()">
                                    <option value="" selected>-- Select --</option>
                                    <option value="1" {{ $projectReceive->transaction_account_type == 1 ? 'selected' : '' }}>Cash</option>
                                    <option value="2" {{ $projectReceive->transaction_account_type == 2 ? 'selected' : '' }}>Bank</option>
                                </select>
                            @if ($errors->has('transaction_way'))
                                <span class="alert text-danger">
                                    {{ $errors->first('transaction_way') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash" @if($projectReceive->transaction_account_type == 2) style="display: none" @endif>
                            <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                                @if($projectReceive->transaction_account_type == 2) style="display: none" @endif ></span></label>
                            <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                <option  value=""  selected>--Select Cash Account--</option>
                                @foreach ($cash_account as $account)
                                     <option value="{{$account->id}}"{{$projectReceive->account_id == $account->id ? 'selected' : '' }} >{{$account->name}}</option>
                                @endforeach
                            </select>
                                @error('cash_account_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way transaction"  @if ($projectReceive->transaction_account_type	 == 1) style="display: none" @endif >
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info" id="balance" ></span></label>
                                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" @if ($projectReceive){{$projectReceive->account_id ==  $bankAccount->id ? 'selected' : ''}}@endif >{{ $bankAccount->name }}
                                            | {{ $bankAccount->account_number }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('account_id'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('account_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way transaction" @if ($projectReceive->transaction_account_type == 1) style="display: none" @endif>
                                <label for="cheque_number"><b>Cheque Number / Transaction</b><span class="text-danger">*</span></label>
                                <input type="text" name="cheque_number" id="cheque_number"
                                    class="form-control"@if ($projectReceive) value="{{$projectReceive->cheque_number}}"@endif placeholder=" ...">
                                @if ($errors->has('cheque_number'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('cheque_number') }}
                                    </span>
                                @endif
                            </div>
                        <div class="">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Document</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-document-check-btn" value="" @if (count($documents)> 0 ) checked @endif >
                                </div>
                            </div>
                            @if(isset($documents) && count($documents) > 0)
                                @foreach($documents as $key => $document)
                                    <div class="row" id="remove_row_document" class="remove_document">
                                        <input type="hidden" name="revenue_document_id[]" class="revenue_document_id" value="{{$document->id}}">
                                        <div class="col-sm-6 mb-2 document">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="document_title"><b>Document Title</b></label>
                                                @endif
                                                <input class="form-control document_title" type="text" placeholder="Document Title.."  name="document_title[]" value="{{$document->document_name}}" >
                                                @error('document_title')
                                                    <span class="text-danger" role="alert">
                                                        <p>{{ $message }}</p>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-5 mb-2 document">
                                            <div class="form-group">
                                                @if($key == 0)
                                                    <label for="document"><b>Document</b></label>
                                                @endif
                                                <input  data-height="25"class="dropify form-control  document" type="file" placeholder="Document" name="documents[]"   data-default-file="{{asset('/img/revenue/documents'.$document->document_name)}}">
                                                    @error('documents')
                                                        <span class="text-danger" role="alert">
                                                            <p>{{ $message }}</p>
                                                        </span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-1 document ">
                                            <button type="button" @if($key == 0) style="margin-top:24px" @endif  class=" btn btn-sm btn-danger  btn_remove_document" >
                                                X
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row">
                                    <div class="col-sm-6 mb-2 document " style="display:none">
                                        <div class="form-group">
                                            <label for="document_title"><b>Document Title</b></label>
                                            <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" value="" >
                                            @error('document_title')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-5 mb-2 document" style="display:none">
                                        <div class="form-group">
                                            <label for="document"><b>Document</b></label>
                                            <input data-height="25"class="dropify form-control " type="file" placeholder="Document" name="documents[]" value="">
                                            @error('documents')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-1 document" style="display:none">
                                        <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                                            X
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="documentRow document" >

                            </div>
                            <div class="col-sm-4 document " @if (count($documents) == 0 ) style="display: none" @endif >
                                <button type="button" style="margin-top:0px" id="add_document"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Document
                                </button>
                            </div>
                        </div>
                        <div class="form-group col-12 mb-2">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" id="receiptdescription" rows="10" cols="40" class="form-control description" value="{{ old('description') }}" placeholder="Description...">{{$projectReceive->description}}</textarea>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
                $('.dropify').dropify();
        });
        CKEDITOR.replace(receiptdescription,{
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"] },
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links","groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo","groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
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
                                                    '<input type="file" id="document" data-height="25"class="dropify form-control " name="documents[]" value="">'+
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
        $(document).on("click", "#add-document-check-btn", function () {
            if ($('#add-document-check-btn').is(":checked"))
                $(".document").show();
            else
                $(".document").hide();
                $('#remove_row_document').remove();
        });
        function documentRemove(id) {
            $('#document-table-tr-' + id).remove();
        };

        $(document).on('click', '.btn_remove_document', function () {
            $(this).parents('#remove_row_document').remove();
        })
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
        function checkAmountt(){
            var totalAmount = $('#total_budget').val();
            var receiptAmount = $('#receipt-amount').val();
            if(parseFloat(totalAmount) < parseFloat(receiptAmount)){
                $('#receipt-amount').val(0);
                swal({
                        title: `Please Enter Correct Amount`,
                        text: receiptAmount + "  Greater Than " + totalAmount,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#receipt-amount').val(0);
                        }
                    });
            }
        }
    </script>
@endpush
