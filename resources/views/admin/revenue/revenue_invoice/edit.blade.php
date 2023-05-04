@extends('layouts.dashboard.app')

@section('title', 'Edit Revenue')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
    integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .text-danger strong{
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title{
            width: 50%;
            font-weight: 600;
            display: none;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
        .dropify-wrapper .dropify-message p {
            font-size: initial;

        }
        .dropify-wrapper {
            border-radius: 6px;
       }
        @media (min-width:200px ) and (max-width:1130px ) {
            .responsive-table{
                width: 100%;
            }

            .responsive-table th{
                display: none;
            }

            .responsive-table .responsive-table-tr{
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .responsive-table tr td{
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title{
                display: block;
            }
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
                <a href="{{ route('admin.revenue.index') }}">Revenue</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.revenue.index') }}">Edit</a>
            </li>
        </ol>
        <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('admin.revenue.update',$revenue->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="revenue_invoice_no"><b>Revenue Invoice No</b><span class="text-danger">*</span></label>
                                    <input type="text" name="revenue_invoice_no" id="revenue_invoice_no" class="form-control" value="{{$revenue->revenue_invoice_no}}" placeholder="Invoice No" readonly required>
                                    @error('revenue_invoice_no')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="invoice_date"><b>Revenue Invoice Date</b><span class="text-danger">*</span></label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{$revenue->revenue_invoice_date}}" placeholder="Invoice Date" required>
                                    @error('invoice_date')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                                @php
                                    $auth = Auth::user();
                                     $user_role = $auth->roles->first();
                                @endphp
                                @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                                    <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror" >
                                        <option value="" selected >--Select warehouse_id--</option>
                                        @forelse ($warehouses as $item)
                                            <option value="{{ $item->id }}" {{$transaction->warehouse_id == $item->id ? 'selected' : ''}}>
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Warehouse</option>
                                        @endforelse
                                    </select>

                                @else
                                    <input type="text" name="warehouse_id_name"  class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                                    <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
                                @endif
                                @error('warehouse_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="revenue_invoice_no"><b>Revenue By</b></label>
                                    <select class="form-control expense_by " id="revenue_by_id" name="revenue_by_id" required>
                                        <option>Select Option</option>
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}" {{$revenue->revenue_by == $employee->id ? 'selected' : ''}}>{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('revenue_by_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="form-group col-sm-6 my-2 client">
                                <label for="client"><b>Client</b></label>
                                <select name="client" id="client" class="form-select @error('client') is-invalid @enderror" >
                                    <option value="">--Select Client--</option>
                                    @if (isset($client))
                                        <option value="{{$client->id}}" selected>{{$client->name}}</option>
                                    @endif
                                </select>
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}
                        </div>
                        @foreach ($revenue_details as $key => $revenuedetails)
                        <input type="hidden" name="revenue_details_id[]" value="{{$revenuedetails->id}}">
                            <div class="row mt-2" id="responsive-table-tr-{{$key}}">
                                <div class="col-sm-6 mb-2">
                                    <div class="form-group">
                                            <label for="revenue_categorie_id"><b>Category</b><span class="text-danger">*</span></label>
                                            <select class="form-control revenue_categorie_id "id="revenue_categorie_id" name="revenue_categorie_id[]">
                                                <option value="" selected>--Select Catagory-- </option>
                                                @foreach($revenueCategorys as $revenueCategory)
                                                    <option value="{{$revenueCategory->id}}"{{$revenuedetails->revenue_category == $revenueCategory->id ? 'selected' : '' }}>{{$revenueCategory->name}}</option>
                                                @endforeach
                                                        </select>
                                                @error('revenue_categorie_id')
                                                    <span class="text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                    </div>
                                </div>
                                <div class="col-sm-5 mb-2">
                                    <div class="form-group">
                                            <label for="revenue_categorie_id"><b>Date</b><span class="text-danger">*</span></label>
                                            <input type="date" name="revenue_date[]" id="revenue_date" class="form-control" value="{{$revenuedetails->revenue_date}}" required>
                                            @error('revenue_date')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                @if ($key == 0 )
                                    <div class="col-sm-1 ">
                                        <button style="margin-top:22px" class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white"
                                            type="button" disabled>&times;
                                            </button>
                                    </div>
                                @else
                                    <div class="col-sm-1 ">
                                        <button style="margin-top:22px" class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white"
                                            type="button" onclick="expensesRemove({{ $key}})"> &times;
                                            </button>
                                    </div>
                                @endif
                                <div class="col-sm-8 ">
                                    <div class="form-group">
                                            <label for="description"><b>Description</b></label>
                                            <input class="form-control description " value="{{$revenuedetails->description}}" id="description" type="text" placeholder="Description" name="description[]" >
                                            @error('description')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3 ">
                                    <div class="form-group">
                                            <label for="amount"><b>Amount</b><span class="text-danger">*</span></label>
                                            <input class="form-control amount" value="{{$revenuedetails->amount}}" id="amount" type="number" placeholder="0.00" name="amount[]" onkeyup="getTotalAmount()"required>
                                            @error('amount')
                                                <span class="text-danger" role="alert">
                                                    <p>{{ $message }}</p>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- append data --}}
                        <div class="expense-body" >

                        </div>
                        {{-- <div class="col-sm-4 ">
                            <button type="button" style="margin-top:22px" id="addRow" class="btn btn-sm btn-success text-white">
                                + Add Revenue
                            </button>
                        </div> --}}
                        {{--  --}}
                        <div class="row mt-4">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Vat</b></label>
                                    <input class="form-check-input " type="checkbox" id="addVat-btn" value="" {{$revenue->vat_rate ? 'checked' : ''}} >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-6 mb-2"  >
                                        <div class="form-group add-vat" @if ($revenue->vat_rate == null ) style="display: none" @endif >
                                            <label><b>Vat Type</b></label>
                                            <select class="form-control" id="add_vat_type" name="vat_type" >
                                                <option value="" selected>--Select Vat Type--</option>
                                                <option value="1" {{$revenue->vat_type == 1 ? 'selected' : ''}}>Including</option>
                                                <option value="2" {{$revenue->vat_type == 2 ? 'selected' : ''}}>Excluding</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2"  >
                                        <div class="form-group add-vat"  @if ($revenue->vat_rate == null ) style="display: none" @endif>
                                            <label><b>Vat Rate </b> <span class="text-danger">%</span></label>
                                            <input type="number" name="vat_rate" id="vat_rate"class="form-control " value="{{$revenue->vat_rate}}" placeholder="0 %" onkeyup="adjustmentBalanceCount()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="total"><b>Total</b><span class="text-red">*</span></label>
                                    <input type="number" name="total" id="total" class="form-control"
                                           value="{{$total}}" placeholder="Total" readonly required>
                                    <div class="help-block with-errors"></div>
                                    @error('total')
                                    <span class="text-red" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Adjustment</b></label>
                                    <input class="form-check-input " type="checkbox" id="adjustment-btn" value="" {{$revenue->adjustment_type ? 'checked' : ''}} >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group adjustment" @if ($revenue->adjustment_type == null ) style="display: none" @endif>
                                    <label><b>Adjustment Type</b></label>
                                    <select class="form-control" id="adjustment_type" name="adjustment_type"
                                            onchange="adjustmentBalanceCount()">
                                        <option value=""  selected>--Select--</option>
                                        <option value="1" {{$revenue->adjustment_type == 1 ? 'selected' : ''}}>Addition</option>
                                        <option value="2" {{$revenue->adjustment_type == 2 ? 'selected' : ''}}>Subtraction</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group adjustment" @if ($revenue->adjustment_type == null ) style="display: none" @endif>
                                    <label><b>Adjustment Balance</b></label>
                                    <input type="number" name="adjustment_balance" id="adjustment_balance"
                                           class="form-control" value="{{$revenue->adjustment_balance}}" placeholder="{{$revenue->adjustment_balance ? $revenue->adjustment_balance : '.....' }}"
                                           onkeyup="adjustmentBalanceCount()">
                                    @error('adjustment_balance')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><b>Final Balance </b><span class="text-danger">*</span></label>
                                    <input type="hidden" value="" id="amount_balance">
                                    <input type="number" name="total_balance" id="total_balance" class="form-control "
                                           value="{{$revenue->total}}" placeholder="0.00" readonly required>
                                    @error('total_balance')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            {{-- <div class="col-12 my-2">
                                <div class="form-group">
                                    <label><b>Add Transaction</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-transaction" value="" {{$revenue->transaction_way ? 'checked' : ''}} >
                                </div>
                            </div> --}}
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 transaction"  @if ($revenue->transaction_way == null ) style="display: none" @endif>
                                <label for="transaction_way"><b>Transaction Type</b><span
                                        class="text-danger">*</span></label>
                                        <select name="transaction_way" id="transaction_way" class="form-control"onchange="expenseTransactionWay()">
                                            <option value="" selected>-- Select --</option>
                                            <option value="1" {{ $revenue->transaction_way == 1 ? 'selected' : '' }}>Cash</option>
                                            <option value="2" {{ $revenue->transaction_way == 2 ? 'selected' : '' }}>Bank</option>
                                        </select>
                                @if ($errors->has('transaction_way'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('transaction_way') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way transaction"  @if ($revenue->transaction_way != 2) style="display: none" @endif >
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" @if ($transaction){{$transaction->account_id ==  $bankAccount->id ? 'selected' : ''}}@endif >{{ $bankAccount->name }}
                                            | {{ $bankAccount->account_number }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('account_id'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('account_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash transaction" @if($transaction->transaction_account_type == 2) style="display: none" @endif>
                                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span></label>
                                <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                    <option  value=""  selected>--Select Cash Account--</option>
                                    @foreach ($cash_account as $account)
                                         <option value="{{$account->id}}" {{$transaction->account_id == $account->id ? 'selected' : '' }} >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way transaction" @if ($revenue->transaction_way != 2) style="display: none" @endif>
                                <label for="cheque_number"><b>Cheque Number / Transaction</b></label>
                                <input type="text" name="cheque_number" id="cheque_number"
                                    class="form-control"@if ($transaction) value="{{$transaction->cheque_number}}"@endif placeholder=" ...">
                                @if ($errors->has('cheque_number'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('cheque_number') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><b>Add Document</b></label>
                                    <input class="form-check-input " type="checkbox" id="add-document-check-btn" value="" {{count($documents) > 0   ? 'checked' : ''}}>
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
                            <div class="col-sm-4 document " @if ($documents->count() == 0 ) style="display: none" @endif >
                                <button type="button" style="margin-top:0px" id="add_document"
                                        class="btn btn-sm btn-success text-white">
                                    + Add Document
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note"><b>Note</b><span class="text-red"></span></label>
                                    <textarea name="note" id="note" class="form-control" cols="30" rows="5"
                                              placeholder="Add a description">{{$revenue->description}}</textarea>
                                    <div class="help-block with-errors"></div>
                                    @error('note')
                                    <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Update Invoice</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script>
        $(document).ready(function() {
                $('.dropify').dropify();
            });
        CKEDITOR.replace('note', {
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"]},
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links", "groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo", "groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
            // $('#client').select2({
            //     placeholder:'Select Client',
            //         ajax: {
            //             url: '{{route('admin.crm.client.search.all')}}',
            //             dataType: 'json',
            //             type: "POST",
            //             data: function (params) {
            //                 var query = {
            //                     search: params.term,
            //                     type: 'public'
            //                 }
            //                 return query;
            //             },
            //             processResults: function (data) {
            //                 console.log();
            //                 // Transforms the top-level key of the response object from 'items' to 'results'
            //                 return {
            //                     results: $.map(data, function (item) {
            //                         return {
            //                             text: item.name,
            //                             value: item.id,
            //                             id: item.id,
            //                         }
            //                     })
            //                 };
            //             }
            //         }
            // });

        // Row Append
        $(document).ready(function () {
            // $('#revenue_by_id').select2();
            var max_field = 5;
            var wrapper = $(".expense-body");
            var x = 0;
            $("#addRow").click(function () {
                if (x < max_field) {
                    x++;
                    $(wrapper).append('<div class="row mt-2 responsive-table-tr" id="responsive-table-tr-'+x+'">'+
                            '<div class="col-sm-6 mb-2">'+
                                '<div class="form-group">'+
                                        '<label for="revenue_categorie_id"><b>Category</b></label>'+
                                        '<select class="form-control revenue_categorie_id "id="revenue_categorie_id" name="revenue_categorie_id[]">'+
                                            '<option value="" selected>--Select Catagory-- </option>'+
                                            '@foreach($revenueCategorys as $revenueCategory)'+
                                                '<option value="{{$revenueCategory->id}}">{{$revenueCategory->name}}</option>'+
                                            '@endforeach'+
                                        '</select>'+
                                        '@error("revenue_categorie_id")'+
                                            '<span class="text-danger" role="alert">'+
                                               ' <p>{{ $message }}</p>'+
                                            '</span>'+
                                        '@enderror'+
                               ' </div>'+
                            '</div>'+
                            '<div class="col-sm-5 mb-2">'+
                                '<div class="form-group">'+
                                        '<label for="revenue_categorie_id"><b>Date</b><span class="text-danger">*</span></label>'+
                                        '<input type="date" name="revenue_date[]" id="revenue_date" class="form-control" value="" required>'+
                                        '@error("revenue_date")'+
                                            '<span class="text-danger" role="alert">'+
                                               ' <p>{{ $message }}</p>'+
                                            '</span>'+
                                        '@enderror'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-1 ">'+
                                    '<button type="button" style="margin-top:22px"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="expensesRemove('+x+')">'+
                                        '&times;'+
                                    '</button>'+
                            '</div>'+
                            '<div class="col-sm-8 ">'+
                                '<div class="form-group">'+
                                        '<label for="description"><b>Description</b><span class="text-danger">*</span></label>'+
                                        '<input class="form-control description " id="description" type="text" placeholder="Description" name="description[]" >'+
                                        '@error("description")'+
                                            '<span class="text-danger" role="alert">'+
                                                '<p>{{ $message }}</p>'+
                                           ' </span>'+
                                        '@enderror'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-3">'+
                                '<div class="form-group">'+
                                       ' <label for="amount"><b>Amount</b><span class="text-danger">*</span></label>'+
                                        '<input class="form-control amount" id="amount" type="number" placeholder="0.00" name="amount[]" onkeyup="getTotalAmount()"required>'+
                                        '@error("amount")'+
                                            '<span class="text-danger" role="alert">'+
                                                '<p>{{ $message }}</p>'+
                                            '</span>'+
                                        '@enderror'+
                                '</div>'+
                            '</div>'+
                        '</div>');
                } else {
                    alert('you can not add more than 5 field');
                }
            });
        });
        $('#revenue_by_id').select2({
                    height:30,
                    ajax: {
                        url: '{{route('admin.investment.employee.search')}}',
                        dataType: 'json',
                        type: "POST",
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public',
                                warehouse_id: $('#warehouse_id').val(),
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
        $('#account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                        transaction_way : $("#transaction_way").val()
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
        $('#cash_account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                       transaction_way : $("#transaction_way").val()
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

        $(document).on("click","#adjustment-btn",function() {
            if($('#adjustment-btn').is(":checked"))
            $(".adjustment").show();
           else
             $(".adjustment").hide();
             $('#adjustment_balance').val(0);
             $('#adjustment_type').val('');
             adjustmentBalanceCount();
        });

        $(document).on("click","#add-transaction",function() {
            if($('#add-transaction').is(":checked"))
            $(".transaction").show();
           else
             $(".transaction").hide();
             $(".bank-way").hide();
             $("#transaction_way").val('');
             $("#balance").hide();

        });
        $(document).on("click","#addVat-btn",function() {
            if($('#addVat-btn').is(":checked"))
            $(".add-vat").show();
           else
             $(".add-vat").hide();
             $('#vat_rate').val(0);
             $('#add_vat_type').val('');
             adjustmentBalanceCount();
        });
        $(document).on("click", "#add-document-check-btn", function () {
            if ($('#add-document-check-btn').is(":checked"))
                $(".document").show();
            else
                $(".document").hide();
                $('#remove_row_document').remove();
        });
        // Row Remove
        function expensesRemove(id) {
            var expenseRowTotal = $('#responsive-table-tr-'+id+' #amount').val();

            var total = $('#total').val();
            var total_balance = $('#total_balance').val();

            var totalResult = parseFloat(total) - parseFloat(expenseRowTotal);
            var totalBalanceResult = parseFloat(total_balance) - parseFloat(expenseRowTotal);


            $('#total').val(totalResult);
            $('#total_balance').val(totalBalanceResult);

            $('#responsive-table-tr-'+id).remove();
        }
        function documentRemove(id) {
            $('#document-table-tr-' + id).remove();
        };

        $(document).on('click', '.btn_remove_document', function () {
            $(this).parents('#remove_row_document').remove();
        });

        // Total Value sum
        function getTotalAmount() {
            var sumdata = 0;
            $('.amount').each(function () {
                if ($(this).val() != '') {
                    sumdata += parseFloat($(this).val());
                }
            });
            $("#total").val(sumdata);
            $("#total_balance").val(sumdata);
            $('#adjustment_balance').val(0);
            adjustmentBalanceCount();
        }

        // Adjustment Balance Count
        function adjustmentBalanceCount() {
            var adjustmentType = document.getElementById('adjustment_type').value;
            var vatRate= document.getElementById('vat_rate').value;
            var totalAmount = document.getElementById('total').value;
            var adjustmentBalance = document.getElementById('adjustment_balance').value;

            var vatBlance = (vatRate/100)*parseFloat(totalAmount);
            var vatfinalBalance = parseFloat(totalAmount) + parseFloat(vatBlance);

            $("#total_balance").val(vatfinalBalance);

            var vatBalance = document.getElementById('total_balance').value;

            if (adjustmentType == 1) {
                if (adjustmentBalance)
                {
                    var finalBalance = parseFloat(vatBalance) + parseFloat(adjustmentBalance);
                $("#total_balance").val(finalBalance);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentBalance)
                {
                    var finalBalance = vatBalance - adjustmentBalance;
                    $("#total_balance").val(finalBalance);
                }
            }
        }
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

    </script>
@endpush

