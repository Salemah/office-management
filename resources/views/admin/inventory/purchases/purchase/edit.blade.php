@extends('layouts.dashboard.app')

@section('title', 'Edit Purchase')

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
                 <a href="{{route('admin.inventory.purchase.index')}}">Purchase </a>
            </li>
            <li class="breadcrumb-item">
                 Edit
            </li>
        </ol>
        <a href="{{ route('admin.inventory.purchase.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.purchase.update',$purchase->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="date"><b>Date</b><span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('datee') is-invalid @enderror"value="{{$purchase->date}}" name="date" id="datepicker">
                                    @error('date')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
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
                                                <option value="{{ $item->id }}" {{$item->id == $purchase->warehouse_id ? 'selected' : ''}} >
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
                                    <label for="supplier_id"><b>Supplier</b><span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                        <option selected value="{{$purchase->supplier_id}}">
                                            {{$purchase->supplier_id ? $purchase->suppliers->name : ''}}
                                        </option>
                                    </select>
                                        @error('supplier_id')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="status"><b>Select Purchase Status</b> <span class="text-danger">*</span></label>
                                    <select name="status"  class="form-control" >
                                        <option value="1" selected>Recieved</option>
                                        {{-- <option value="2">Partial</option>
                                        <option value="3">Pending</option>
                                        <option value="4">Ordered</option> --}}
                                    </select>
                                        @error('status')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label><b>Attach Document</b></label> <i class="fa-solid fa-circle-question" data-toggle="tooltip" title="Only jpg, jpeg, png, gif, pdf, csv, docx, xlsx and txt file is supported"></i>
                                        <input type="file" name="document" class="form-control" >
                                        @if($errors->has('document'))
                                            <span>
                                               <strong>{{ $errors->first('document') }}</strong>
                                            </span>
                                        @endif
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <div class="">
                                        <label><b> Cash Memo </b><span class="text-danger">*</span></label>
                                        <input type="text" name="cash_memo" id="cash_memo" value="{{$purchase->cash_memo}}" class="form-control" placeholder="Enter Cash Memo" >
                                        @if($errors->has('cash_memo'))
                                            <span>
                                               <strong class="text-danger">{{ $errors->first('cash_memo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <div class="">
                                        <label> <b>Invoice No </b><span class="text-danger">*</span></label>
                                        <input type="text" name="invoice_no" id="invoice_no" value="{{$purchase->invoice_no}}" class="form-control" placeholder="Enter Invoice No" >
                                        @if($errors->has('invoice_no'))
                                            <span>
                                               <strong class="text-danger">{{ $errors->first('invoice_no') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                        <label><b> Select Product </b><span class="text-danger">*</span></label>
                                        <select  id="product"  class="form-control" >
                                            <option value="" selected>Select Product</option>
                                            {{-- @foreach ($variantProducts as $product)
                                              <option value="{{$product->id}}">{{$product->value}} {{$product->varients->name}} | {{$product->products->name}}</option>
                                            @endforeach --}}
                                            @foreach ($products as $key=>$product)
                                                    @if($product->is_variant)
                                                        @foreach ($product->productVarients as $varient )
                                                            <option value="{{$product->id}}, {{$varient->id}}">
                                                                {{$varient->value}} {{$varient->varients->name}} | {{$product->code }} | {{$product->name}}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                    <option value="{{$product->id}}">
                                                        {{$product->name}}
                                                      </option>
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- @if($errors->has('invoice_no'))
                                            <span>
                                               <strong class="text-danger">{{ $errors->first('invoice_no') }}</strong>
                                            </span>
                                        @endif --}}
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5>Order Table *</h5>
                                        <div class="table-responsive mt-3">
                                            <table id="myTable" class="table table-hover order-list">
                                                <thead class="bg-secondary">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Code</th>
                                                        <th>Quantity</th>
                                                        <th class="recieved-product-qty d-none">Recieved</th>
                                                        <th>Batch No</th>
                                                        <th>Expired Date</th>
                                                        <th>Net Unit Cost</th>
                                                        <th>Discount</th>
                                                        <th>Tax</th>
                                                        <th>Subtotal</th>
                                                        <th><i class="fa-solid fa-trash"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody  id="product_data">
                                                    @foreach ($inventories as $key => $inventory )
                                                        <tr id="responsive-table-tr">
                                                            <input type="hidden" class="purchase-unit" name="purchase_unit[]" value="{{$inventory->purchase_unit_id}}">
                                                            <td ><input type="hidden" name="product[]" id="product_id-{{$key+1}}" value="{{$inventory->products->id}}" class="form-control" >@if($inventory->variant){{$inventory->variant->value}} {{$inventory->variant->name}} - @endif{{$inventory->products->name}}</td>
                                                            <td>{{$inventory->products->code}}</td>
                                                            <td><input type="number" id= "qty-{{$key+1}}" oninput="qtyCheck({{$key+1}})" name="qty[]" class="form-control" value="{{$inventory->purchase_qty}}"></td>
                                                            <td class="recieved-product-qty d-none">Recieved</td>

                                                            <td><input type="text" name="batch_no[]" class="form-control"   {{$inventory->products->is_batch ? '' : 'readonly'}}  value="{{$inventory->product_batch_id}}" ></td>
                                                            <td><input type="date" name="expired_date[]" class="form-control"   {{$inventory->products->is_batch ? '' : 'readonly'}} value="{{$inventory->expired_date}}"></td>

                                                            <td><input type="text" name="price[]"  id="price-{{$key+1}}" value="{{$inventory->net_unit_cost}}" class="form-control"   readonly ></td>
                                                            <td><input type="text" name="discount[]" value="" id="discount-{{$key+1}}" class="form-control"   readonly ></td>
                                                            <td><input type="text" name="tax[]" value="" id="tax-{{$key+1}}" class="form-control"   readonly ></td>
                                                            <td><input type="number" name="subtotal[]" id="subtotal-{{$key+1}}" class="form-control" value="{{$inventory->total}}" readonly></td>
                                                            <td><button type="button"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="expensesRemove({{$key+1}})">
                                                            &times;
                                                            </button></td>
                                                            <input type="hidden" name="variant[]"  id="variant-{{$key+1}}" value="{{$inventory->variant_id}}" class="form-control"   readonly >
                                                        </tr>
                                                            @endforeach
                                                            @php
                                                                $totalrow =  count($inventories);
                                                            @endphp
                                                            <input type="hidden"   id="total-row" value="{{$totalrow}}" class="form-control" readonly >
                                                </tbody>
                                                <tfoot class="tfoot active">
                                                    <th colspan="2">Total</th>
                                                    <th id="total-qty"> {{$purchase->total_qty}}</th>
                                                    <th class="recieved-product-qty d-none"></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th id="total-discount">0.00</th>
                                                    <th id="total-tax">0.00</th>
                                                    <th id="total">{{$purchase->grand_total}}</th>
                                                    <th><i class="fa-solid fa-trash"></i></th>
                                                </tfoot>
                                            </table>
                                            <input type="hidden" name="total_qty" value="{{$purchase->total_qty}}"  id="totalQty" >
                                            <input type="hidden" name="total" value="{{$purchase->grand_total}}"  id="total-val" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label><b> Order Tax </b></label>
                                            <select class="form-control" name="order_tax_rate" id="tax_id">
                                                <option value="" selected >No Tax</option>
                                                @foreach($taxs as $tax)
                                                <option value="{{$tax->rate}}" {{$purchase->total_tax == $tax->rate ? 'selected': '' }}>{{$tax->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label>
                                                <strong>Discount</strong>
                                            </label>
                                            <input type="number" name="order_discount" value="{{$purchase->total_discount}}" class="form-control" step="any" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label>
                                                <strong>Shipping Cost</strong>
                                            </label>
                                            <input type="number" name="shipping_cost" value="{{$purchase->shipping_cost}}" class="form-control" step="any" />
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                        <label><b>Note </b></label>
                                        <textarea rows="5" class="form-control" name="note">{{$purchase->note}}</textarea>
                                    </div>
                                </div>
                        </div>
                        <div class="form-group">
                            <button type="submit"  class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="mb-5"></div>
@endsection
@push('script')
    <script>
        var rowindex;
        var customer_group_rate;
        var row_product_cost;
         // client_typeData
         var row = $('#total-row').val();
         var x = row;
         var copy = $('#product').clone();
         $(document).on('change', '#product', function () {
            var id = $("#product").val();
            var vairants = id.split(",");
            var vairant = '';
            if(vairants[1]){
                vairant = vairants[1];
                id = vairants[0];
            }
            else{
                 vairant = '';
            }

            var url = '{{ route('admin.inventory.purchase.product.data',':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                data: {
                            data: vairant
                        },
                dataType: "json",
                success: function ( data) {
                    console.log(data);
                    x++;
                    var read = 'readonly';
                    var variantData = '';
                    if(data[0].is_batch){
                        read = '';
                    }
                    if(data[2]){
                        variantData =  data[2].value+ data[2].name+'-';
                    }
                    $('#product_data').append('<tr id="responsive-table-tr">'+
                        '<input type="hidden" class="purchase-unit" name="purchase_unit[]" value="' +data[0].purchase_unit_id   + '">'+
                        '<td ><input type="hidden" name="product[]" id="product_id-' + x + '" value="' +data[0].id+'" class="form-control" >' +variantData+data[0].name+'</td>'+
                        '<td>'+data[0].code+'</td>'+
                        '<td><input type="number" id= "qty-'+x+'" oninput="qtyCheck('+x+')" name="qty[]" class="form-control" value="0"></td>'+
                        '<td class="recieved-product-qty d-none">Recieved</td>'+

                            '<td><input type="text" name="batch_no[]" class="form-control"   '+read+ ' value="" ></td>'+
                            '<td><input type="date" name="expired_date[]" class="form-control"  '+read+ '  ></td>'+



                        '<td><input type="text" name="price[]" value="'+data[1].cost+'" id="price-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="text" name="discount[]" value="" id="discount-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="text" name="tax[]" value="" id="tax' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="number" name="subtotal[]" id="subtotal-' + x + '" class="form-control" value="" readonly></td>'+
                        '<td><button type="button"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="expensesRemove(' + x + ')">' +
                        '&times;' +
                        '</button></td>'+
                        '<input type="hidden" name="variant[]"  id="variant-' + x + '" value="'+vairant+'" class="form-control"   readonly >'+
                        '</tr>');

                        $("#product option:selected").remove();
                },
                error: function () {
                    location.reload();
                }
            });
         });

        $(document).on('click', '.jDeleteRow', function() {
            $(this).parents('#responsive-table-tr').remove();
            //x--;
            checkAmount();
        });
        //  function expensesRemove(id) {
        //     //alert(id);
        //     // console.log(id);
        //     // var expenseRowTotal = $('#responsive-table-tr-' + id + ' #amount').val();

        //     // var total = $('#total').val();
        //     // var total_balance = $('#total_balance').val();

        //     // var totalResult = parseFloat(total) - parseFloat(expenseRowTotal);
        //     // var totalBalanceResult = parseFloat(total_balance) - parseFloat(expenseRowTotal);

        //     // $('#total').val(totalResult);
        //     //$('#total_balance').val(totalBalanceResult);
        //     $(this).parents('#responsive-table-tr-').remove();
        //     //$('#responsive-table-tr-' + id).remove();
        //    // qtyCheck(id);
        // }
        function qtyCheck(x){
            //
            var totalQty = 0;
            var subtotal = 0;
            var qty = $('#qty-'+x).val();

            if(qty>0){
                checkAmount();
            }
            else{
                $('#qty-'+x).val(0);
                alert("Quantity Can't Negetive")
            }
        }
        function checkAmount(){
               var totalQty = 0;
                var subtotal = 0;
                //alert(x);

                for(var i = 1 ; i <= x; i++){

                    var quantity = $('#qty-'+i).val() ;
                    var totalprice = $('#price-'+i).val();
                    // console.log("quantity ",i,quantity );
                    // console.log("totalprice ",totalprice );
                    if(!quantity){
                        quantity = 0;
                    }
                    if(!totalprice){
                        totalprice = 0;
                    }
                    var subtotalprice = parseFloat(quantity)* parseFloat(totalprice);
                       console.log('quantity',i,quantity);
                       console.log('totalprice',i,totalprice);
                       console.log('subtotalprice',i,subtotalprice);
                     subtotal =  subtotal + subtotalprice;


                    $('#subtotal-'+i).val(subtotalprice);
                    totalQty = parseFloat(totalQty)  + parseFloat(quantity);
                    $('#totalQty').val(totalQty);
                    $('#total-val').val(subtotal);
                    document.getElementById("total-qty").innerHTML = totalQty;
                    document.getElementById("total").innerHTML = subtotal;
                }
        }
        $('#tax_id').select2();
        $('#product').select2();
        $('#supplier_id').select2({
            ajax: {
                url: '{{route('admin.inventory.purchase.supplier.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val()
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
        $('#warehouse_id').on("change", function() {
            var  warehouseId = $('#warehouse_id').val();
            var url = '{{ route('admin.inventory.purchase.warehouse.search', ':id') }}';
                $.ajax({
                    type: 'GET',
                    url: url.replace(':id', warehouseId),
                    success: function(data) {
                        const myArray = data.name.slice(0, 4)+ ' - ';
                        $('#cash_memo').val(myArray  );
                        $('#invoice_no').val(myArray);
                    }
                });
        });
    </script>
@endpush
