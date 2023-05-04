@extends('layouts.dashboard.app')

@section('title', 'Withdraw')
@push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            padding: 6px;
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
                <a href="{{route('admin.account.withdraw.index')}}">Withdraw</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.account.withdraw.create')}}">Edit</a>
            </li>
        </ol>
        <a href="{{ route('admin.account.withdraw.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.account.withdraw.update',$transaction->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                                @php
                                    $auth = Auth::user();
                                     $user_role = $auth->roles->first();
                                @endphp
                                @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                                    <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror">
                                        <option value="" selected >--Select warehouse_id--</option>
                                        @forelse ($warehouses as $item)
                                            <option value="{{ $item->id }}" {{$item->id == $transaction->warehouse_id ? 'selected' : ''}} >
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Warehouse</option>
                                        @endforelse
                                    </select>

                                @else
                                <input type="text" name="warehouse_id_name" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                                <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
                                @endif
                                @error('warehouse_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_way"> <b>Transaction Type</b><span class="text-danger">*</span></label>
                                <select name="transaction_way" id="transaction_way"class="form-select" onchange="expenseTransactionWay()">
                                    <option value="" >--Select Transaction Type--</option>
                                    <option value="1"  {{$transaction->transaction_account_type == 1  ? 'selected' : ''}}>Cash</option>
                                    <option value="2" {{$transaction->transaction_account_type == 2 ? 'selected' : ''}}>Bank</option>
                                </select>
                                    @error('transaction_way')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_title"><b>Transaction Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="transaction_title" id="transaction_title"class="form-control " value="{{$transaction->transaction_title }}"placeholder="Enter Transaction Title">
                                    @error('transaction_title')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_date"> <b> Date </b><span class="text-danger">*</span></label>
                                    <div class="form-group{{ $errors->has('transaction_date') ? ' has-error' : '' }} has-feedback">
                                        <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{$transaction->transaction_date}}">
                                            @if ($errors->has('transaction_date'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('transaction_date') }}</strong>
                                                </span>
                                            @endif
                                    </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" @if($transaction->transaction_account_type == 1) style="display: none" @endif>
                                <label for="account_id"> <b>Bank Account</b><span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id"class="form-select @error('account_id') is-invalid @enderror" onchange="getBalance()">
                                    <option >--Select Bank Account--</option>
                                    @foreach ($bank_accounts as $account)
                                    <option value="{{$account->id}}" {{$transaction->account_id == $account->id ? 'selected' : '' }} >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash"  @if($transaction->transaction_account_type == 2) style="display: none" @endif>
                                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span></label>
                                <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                    <option  value=""  selected>--Select Cash Account--</option>
                                    @foreach ($cash_account as $account)
                                         <option value="{{$account->id}}" {{$transaction->account_id == $account->id ? 'selected' : '' }}>{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('cash_account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" @if($transaction->transaction_account_type == 1) style="display: none" @endif>
                                <label for="cheque_number"> <b>Cheque Number</b> </label>
                                <div class="form-group{{ $errors->has('cheque_number') ? ' has-error' : '' }} has-feedback">
                                    <input type="text" name="cheque_number" id="cheque_number" class="form-control"
                                        value="{{$transaction->cheque_number}}" placeholder=" ...">
                                    @error('cheque_number')
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cheque_number') }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="deposit_source"> <b> Amount </b><span class="text-danger">*</span><span class="text-info" id="balance" >{{$accountBalance}}</span></label>
                                <input type="hidden"  id="amount_balance" value="{{$accountBalance}}">
                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} has-feedback">
                                    <input type="number" min="0" name="amount" id="amount"class="form-control"value="{{$transaction->amount}}" placeholder="1200 ..." onkeyup="checkAmount(this)">
                                         @error('amount')
                                            <span class="help-block">
                                                <strong>{{ $errors->first('amount') }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                    placeholder="Description...">{{$transaction->description}}</textarea>
                                @error('description')
                                    <span class="text-danger" role="alert">
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
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace('description', {
            toolbarGroups: [
                {"name": "styles","groups": ["styles"]},
                {"name": "basicstyles","groups": ["basicstyles"]},
                {"name": "paragraph","groups": ["list", "blocks"]},
                {"name": "document","groups": ["mode"]},
                {"name": "links","groups": ["links"]},
                {"name": "insert","groups": ["insert"]},
                {"name": "undo","groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
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
        function getBalance() {
            var transactionWay = $('#transaction_way').val();
             var accountId = '';
             if (transactionWay == 2   ){
                var accountId =$('#account_id').val();
             }
             else if(transactionWay == 1){
                var accountId =$('#cash_account_id').val();
             }
                if (accountId !== null) {
                        var url = '{{ route("admin.account.bank.account.balance",":id") }}';
                        $.ajax({
                            type: "GET",
                            url: url.replace(':id', accountId),
                            success: function (resp) {
                                $('#balance').show();
                                document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                                $('#amount_balance').val(resp);
                                console.log(resp);
                            }, // success end
                            error: function (error) {
                                location.reload();
                            } // Error
                        })
                }
        }

        function checkAmount(amount) {

            if($('#transaction_way').val()){
                    var amount = amount.value;
                    var amountBalance = $('#amount_balance').val();
                    if (parseFloat(amountBalance) < parseFloat(amount)) {
                        $('#amount').val(0);
                        swal({
                            title: `Alert?`,
                            text: "You don't have enough balance.",
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $('#amount').val(0);
                            }
                        });
                    }
            }
            else{
                $('#amount').val(0);
                alert('please select transaction type first');
            }
        }
    </script>
@endpush
