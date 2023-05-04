@push('css')
@endpush
<form action="{{ route('admin.investment-return.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
      <input type="hidden" name="investment_id" id="investment_id" value="{{$investment->id}}">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="return_title"><b>Return Title</b><span class="text-danger">*</span></label>
                <input type="text" name="return_title" id="return_title" class="form-control"value="{{ old('return_title') }}" placeholder="Enter Return Title...">
                @if ($errors->has('return_title'))
                    <span class="alert text-danger">
                        {{ $errors->first('return_title') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="investor_id"><b>Select Investor</b><span class="text-danger">*</span></label>
                <input type="text" class="form-control" value="{{ $investment->investor->name }}"
                       readonly>
                <input type="hidden" class="form-control" id="investor_id" name="investor_id"
                       value="{{ $investment->investor->id }}">
                @if ($errors->has('investor_id'))
                    <span class="alert text-danger">
                      {{ $errors->first('investor_id') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="investment_profit_amount"><b>Invested Amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="" class="form-control" readonly
            value="{{ $invested_amount }}">
                @if ($errors->has('investment_profit_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('investment_profit_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="return_profit_amount"><b>Return  Amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="" class="form-control" readonly value="{{ $profit_amount }}">
                @if ($errors->has('return_profit_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('return_profit_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="investment_due_profit_amount"><b>Due  Amount</b><span class="text-danger">*</span></label>
            <input  type="text" name="investment_due_profit_amount" id="investment_due_profit_amount" class="form-control" readonly value="{{ $due }}">
                @if ($errors->has('investment_due_profit_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('investment_due_profit_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_purpose"><b>Return Type</b><span class="text-danger">*</span></label>
            <select name="transaction_purpose" id="transaction_purpose" class="form-control" readonly  >
                <option selected value="11">Profit</option>
            </select>
                @if ($errors->has('transaction_purpose'))
                    <span class="alert text-danger">
                      {{ $errors->first('transaction_purpose') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
            <select name="transaction_way" id="profit_transaction_way" class="form-control"
            onchange="profitTransactionWay()">
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
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 profit_cash" style="display: none">
            <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="profit_cash-balance"
                style="display: none"></span></label>
            <select name="cash_account_id" id="profit_cash_account_id"class="form-select " onchange="getProfitReturn()">
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
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 profit-bank-way  "
        style="display: none">
            <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="bank_balance"
                style="display: none"></span></label>
            <select name="account_id" id="profit_account_id" class="form-control" onchange="getProfitReturn()">
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
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 profit-bank-way "
                style="display: none">
            <label for="cheque_number"><b>Cheque Number / Transaction</b><span class="text-danger">*</span></label>
            <input type="text" name="cheque_number" id="cheque_number"
                    class="form-control" value="{{ old('cheque_number') }}" placeholder=" ...">
            @if ($errors->has('cheque_number'))
                <span class="alert text-danger">
                    {{ $errors->first('cheque_number') }}
                </span>
            @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{ old('transaction_date') }}">
                @if ($errors->has('transaction_date'))
                    <span class="alert text-danger">
                        {{ $errors->first('transaction_date') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="profit_amount_balance"><b>Return  Amount</b><span class="text-danger">*</span><span class="text-info" id="profit_balance" style="display: none"></span></label>
            <input type="hidden" value="" id="profit_amount_balance">
            <input type="number" name="amount" id="profit_amount" class="form-control"value="{{ old('profit_amount') }}" placeholder="1200 ..." onkeyup="checkprofit_amount(this)">
            @if ($errors->has('profit_amount'))
                <span class="alert text-danger">
                    {{ $errors->first('profit_amount') }}
                </span>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="note"><b>Note</b></label>
            <textarea name="note" id="note" rows="3" class="form-control " value="{{ old('note') }}"placeholder="Enter Note..."></textarea>
                @if ($errors->has('note'))
                    <span class="alert text-danger">
                       {{ $errors->first('note') }}
                    </span>
                @endif
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
        </div>
    </div>

</form>



@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js" integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace('note', {
            height:'100px',
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
            removeButtons: 'Image,Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

    $('#profit_transaction_way').on('change', function () {
            $('#profit_amount').val(0);
    })
    function profitTransactionWay() {
            var transaction_way = $("#profit_transaction_way").val();
            if (transaction_way == 2) {
                $('.profit-bank-way').show();
                $('#profit_cash_account_id').val('');
                $('.profit_cash').hide();
            } else if(transaction_way == 1) {
                 $('.profit-bank-way').hide();
                 $('#profit_account_id').val('');
                 $('.profit_cash').show();
            }
             else{
                $('.profit-bank-way').hide();
                $('.profit_cash').hide();
                $('#profit_account_id').val('');
             }
    };
        function getProfitReturn() {
                var totalBalance = $('#total_balance').val();
                var transactionWay = $('#profit_transaction_way').val();
              if (transactionWay == 1) {
                var accountId = $('#profit_cash_account_id').val();
              }
              else{
                var accountId = $('#profit_account_id').val();
              }
                    var url = '{{ route('admin.account.bank.account.balance', ':id') }}';
                    $.ajax({
                        type: "GET",
                        url: url.replace(':id', accountId),
                        success: function (resp) {
                            console.log(resp);
                            //checkAmount(resp);
                            if (transactionWay == 1){
                                $('#profit_cash-balance').show();
                            document.getElementById('profit_cash-balance').innerHTML = '( ' + resp + ' )';
                            }
                            else if (transactionWay == 2){
                                $('#bank_balance').show();
                            document.getElementById('bank_balance').innerHTML = '( ' + resp + ' )';
                            }
                            else{
                                $('#profit_balance').show();
                            document.getElementById('profit_balance').innerHTML = '( ' + resp + ' )';
                            }
                            $('#profit_amount_balance').val(resp);
                            document.getElementById('amount').max = resp;
                        }, // success end
                        error: function (error) {
                            console.log(error);
                            //location.reload();
                        } // Error
                    })
        }
    $('#profit_amount').on('keyup', function () {
            if ($('#profit_transaction_way').val()) {
                if($('#profit_transaction_way').val() == 1){
                    checkprofit_amount($('#profit_amount').val());
                }else{
                    if($('#profit_account_id').val()){
                        checkprofit_amount($('#profit_amount').val());
                    }else{
                        $('#profit_amount').val(0);
                        alert('please select account');
                    }
                }
            } else {
                $('#profit_amount').val(0);
                alert('please select transaction type first');
            }
    })
    function checkprofit_amount(amount) {
                    var amount = amount.value;

                    var amountBalance = $('#profit_amount_balance').val();
                   // alert(amountBalance);
                    if (parseFloat(amountBalance) < parseFloat(amount)) {
                        swal({
                            title: `Alert?`,
                            text: "You don't have enough balance.",
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $('#profit_amount').val(0);
                            }
                        });
                    }
        }
    </script>
@endpush
