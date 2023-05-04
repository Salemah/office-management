@extends('layouts.dashboard.app')

@section('title', 'Sale Return')

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
                 <a href="{{route('admin.inventory.sale-return.index')}}">Sale Return </a>
            </li>
            <li class="breadcrumb-item">
                 Create
            </li>
        </ol>
        <a href="{{ route('admin.inventory.sale-return.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.sale-return.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="date"><b>Date</b><span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('datee') is-invalid @enderror"value="{{ old('date') }}" name="date" id="datepicker">
                                    @error('date')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="invoice_id"><b>Invoice</b><span class="text-danger">*</span></label>
                                        <select name="invoice_id" id="invoice_id"class="form-select @error('invoice_id') is-invalid @enderror">
                                            <option value="" selected >--Select Invoice--</option>
                                            @forelse ($invoices as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->invoice_no }}
                                                </option>
                                            @empty
                                                <option value="">No Invoice</option>
                                            @endforelse
                                        </select>
                                    @error('invoice_id')
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
                                        <label><b> Order Tax </b></label>
                                        <select class="form-control" name="order_tax_rate" id="tax_id">
                                            <option value="" selected >No Tax</option>
                                            @foreach($taxs as $tax)
                                            <option value="{{$tax->rate}}">{{$tax->name}}</option>
                                            @endforeach
                                        </select>
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                        <label><b> Select Product </b><span class="text-danger">*</span></label>
                                        <select  id="product"  class="form-control" >
                                            <option value="" >Select Product</option>

                                        </select>

                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3" id="purchase" style="display: none">
                                            <h5>Purchase Data</h5>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Code</th>
                                                    <th scope="col">Ware House</th>
                                                    <th scope="col">Quantity</th>
                                                    </tr>
                                                </thead>
                                                <div >
                                                    <tbody id="purchase_product">
                                                    </tbody>
                                                </div>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5>Sale Return </h5>
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
                                                <tbody id="product_data">
                                                    {{-- <div class="" >

                                                    </div> --}}
                                                </tbody>
                                                <tfoot class="tfoot active">
                                                    <th colspan="2">Total</th>
                                                    <th id="total-qty"> 0.00</th>
                                                    <th class="recieved-product-qty d-none"></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th id="total-discount">0.00</th>
                                                    <th id="total-tax">0.00</th>
                                                    <th id="total"> 0.00</th>
                                                    <th><i class="fa-solid fa-trash"></i></th>
                                                </tfoot>
                                            </table>
                                            <input type="hidden" name="total_qty"  id="totalQty" >
                                            <input type="hidden" name="total"  id="total-val" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                        <label><b>Note </b></label>
                                        <textarea rows="5" class="form-control" name="note"></textarea>
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
        var removedItems = [];
         // client_typeData
         var x = 0;
         $(document).on('change', '#product', function () {
            var id = $("#product").val();
            console.log(id);
            var vairants = id.split(",");
            var vairant = '';
            if(vairants[1]){
             vairant = vairants[0];
             id = vairants[1];
            }
            else{
                 vairant = '';
            }

            var selectedOption = $(this).find('option:selected');

            console.log(selectedOption);
            selectedOption.remove();
            removedItems.push(selectedOption);

            var url = '{{ route('admin.inventory.sale.product.data',':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                data: {
                            data: vairant
                        },
                dataType: "json",
                success: function ( data) {
                    console.log(data[1]);
                    x++;
                    var read = 'readonly';
                    var variantData = '';
                    if(data[0].is_batch){
                        read = '';
                    }
                    if(data[2].value){
                        variantData =  data[2].value+ data[2].name+'-';
                    }
                    $('#product_data').append('<tr id="responsive-table-tr">'+
                                                '<input type="hidden" class="purchase-unit" name="purchase_unit[]" value="' +data[0].purchase_unit_id   + '">'+
                                                '<td ><input type="hidden" name="product[]" id="product_id-' + x + '" value="' +data[0].id+'" class="form-control" >' +variantData+data[0].name+'</td>'+
                                                '<td>'+data[0].code+' </td>'+
                                                '<td><input type="number" id= "qty-'+x+'" oninput="qtyCheck('+x+')" name="qty[]" class="form-control" value="0"></td>'+
                                                '<td class="recieved-product-qty d-none">Recieved</td>'+
                                                '<td><input type="text" name="batch_no[]" class="form-control"   '+read+ ' value="" ></td>'+
                                                '<td><input type="date" name="expired_date[]" class="form-control"  '+read+ '  ></td>'+
                                                '<td><input type="text" name="price[]" value="'+data[1].price+'" id="price-' + x + '" class="form-control"   readonly ></td>'+
                                                '<td><input type="text" name="discount[]" value="" id="price-' + x + '" class="form-control"   readonly ></td>'+
                                                '<td><input type="text" name="tax[]" value="" id="price-' + x + '" class="form-control"   readonly ></td>'+
                                                '<td><input type="number" name="subtotal[]" id="subtotal-' + x + '" class="form-control" value="" readonly></td>'+
                                                '<td><input type="hidden" name="variant[]"  id="variant-'+ x +'" value="'+vairant+'" class="form-control"   readonly ><button type="button"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="expensesRemove(' + x + ')">' +
                                                '&times;' +
                                                '</button></td>'+
                                                // ''+
                                                '</tr>');


                },
                error: function () {
                   // location.reload();
                }
            });
         });

        $(document).on('click', '.jDeleteRow', function() {
            $(this).parents('#responsive-table-tr').remove();
            checkAmount();

        });
        function qtyCheck(x){
            var totalQty = 0;
            var subtotal = 0;
            var qty = $('#qty-'+x).val();
            var variant = $('#variant-'+x).val();
            var productId = $('#product_id-'+x).val();
            var variantQuantity = $('#variant_id_'+productId+variant).val();

            if(qty>0){
                if(variant == variantQuantity){
                    var purchaseQuantity = $('#purchase_quantity_'+productId+variantQuantity).val();
                    if(parseFloat(qty)>parseFloat(purchaseQuantity)){
                    alert('quantity exceeded maximam limit');
                    $('#qty-'+x).val(0);
                }
            }
                else{
                    var purchaseQuantity = $('#purchase_quantity_'+productId).val();
                    if(parseFloat(qty)>parseFloat(purchaseQuantity)){
                    alert('quantity exceeded maximam limit');
                    $('#qty-'+x).val(0);
                }
                }
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
                   // console.log(subtotalprice);
                     subtotal =  subtotal + subtotalprice;
                    // console.log(subtotal);

                    $('#subtotal-'+i).val(subtotalprice);
                    totalQty = parseFloat(totalQty)  + parseFloat(quantity);
                    $('#totalQty').val(totalQty);
                    $('#total-val').val(subtotal);
                    document.getElementById("total-qty").innerHTML = totalQty;
                    document.getElementById("total").innerHTML = subtotal;
                }
        }
        $('#tax_id').select2();

        $('#product').select2({
            ajax: {
                url: '{{route('admin.inventory.sale-return.product.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#invoice_id').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log(data);
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item,i) {
                            console.log(item);
                               if(Array.isArray(item)){
                                if (item[0].variant_id) {
                                    var p =  item[0].variant.value +' '+item[0].variant.name+'-'+ item[0].products.name;
                                    var v =  item[0].variant.id +','+item[0].products.id;
                                    return {
                                    text: p,
                                    value: v,
                                    id: v,
                                    }
                                    }
                                    else{

                                    return {
                                    text:  item.products.name,
                                    value: item.products.id,
                                    id: item.products.id,
                                    }
                                }

                            }
                            else{
                                    return {
                                    text:  item.products.name,
                                    value: item.products.id,
                                    id: item.products.id,
                                    }
                                }
                        })
                    };
                }
            }
        });

        var j = 0;
        $('#invoice_id').on("change",  function() {
                var  invoiceId = $('#invoice_id').val();
                $('#purchase').show();
                var url = '{{ route('admin.inventory.sale-return.sale.search', ':id') }}';
                $.ajax({
                    type: 'GET',
                    url: url.replace(':id', invoiceId),
                    success: function(data) {
                        console.log(data);
                        $('#purchase_product').empty();
                        j++;
                        //
                        data.forEach((product) =>{
                            var val = '';
                            var varId = '';
                            var varName = '';
                            if(product.variant){
                                console.log(product.variant);
                                val = product.variant.value;
                                varName = product.variant.name;
                                varId = product.variant.id;
                            }
                        $('#purchase_product').append(
                            '<tr>'+
                                '<th scope="row">' + val +varName+' '  + product.products.name+'</th>'+
                                '<td>'+product.products.code+'</td>'+
                                '<td>'+product.warehouse.name+'</td>'+
                                '<td><input type="hidden" id="variant_id_' + product.products.id +varId+ '" value="'+varId+'" class="form-control"   readonly > <input type="hidden" id="purchase_quantity_' + product.products.id +varId+ '" value="'+product.stock_out+'" class="form-control"   readonly >' +product.stock_out+'</td>'+
                            '</tr>');
                        })
                    }
                });
        });

    </script>
@endpush
