<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bill</title>
    <script src="https://kit.fontawesome.com/34590e0ca8.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .print {
            cursor: pointer;
            background: #22546d;
            color: #fff;
        }
        .md-heading {
            text-align: center;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        .print {
            cursor: pointer;
            background: #00aa9a;
            color: #fff;
        }
        #subtotal {
            margin-top: 20px;
        }
        @media print {
            .print {
                display: none;
            }

            #back{
                display: none;
            }
        }
        th ,td{
            padding:3px !important;

        }
    </style>
</head>

<body>
    <div class="container">

        <div class="sub2-row16">
            <div class="l26 md-heading">

                <a href="#" class="print btn btn-sm btn-info" onclick="window.print()"
                    class="btn btn-inverse waves-effect waves-light"><label for="">Print</label> <i
                        class="fa fa-print f-16"></i></a>
                        <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-dark" id="back">Back to list</a>
            </div>
        </div>
    </div>
    <section>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-center">
                        <div class="text-center">
                            <h1 class="mb-0"><b>Bill</b></h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <span class="">
                            <strong>Status : {{ $transaction->status == 0 ? 'Pending' : 'Approved' }} </strong>
                        </span>
                    </div>
                    <div class="d-flex bd-highlight">
                        <div class=" w-100 bd-highlight">
                            <span> <strong> Invoice : {{$revenueInvoice->revenue_invoice_no }} </strong></span> <br><br>
                                <span>House 22(3rd Floor), Road 17, Sector 13, Uttara , Dhaka, Bangladesh</span> <br>
                                <span>Email: info@wardan.tech</span><br>
                                <span>Phone: +880 01792-156494</span><br><br>

                        </div>
                        <div class="p-2 flex-shrink-1 bd-highlight ">
                            <div>
                                <img src="{{ asset('img/dashboard/wardan_dark.png') }}"class="sidebar-brand-full"width="200"
                                    height="70" alt="dashboard logo"> <br>

                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-12 ">
                            <table class="table table table-bordered border-dark">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Voucher No</th>
                                        <th>Revenue Invoice Date</th>
                                        <th>Revenue By</th>
                                        <th>Revenue From</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row"><strong>{{ $revenueInvoice->revenue_invoice_no }}</strong>
                                        </th>
                                        <td><strong>{{ $revenueInvoice->revenue_invoice_date }}</strong></td>
                                        <td><strong>@if(isset($revenueInvoice->revenueBy)){{ $revenueInvoice->revenueBy->name }} @endif</strong></td>
                                        <td><strong>@if(isset($revenueInvoice->clientId)){{ $revenueInvoice->clientId->name }} @endif</strong></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="table-responsive-sm">
                        <table class="table table-striped table-info table table-bordered border-dark">
                            <thead>
                                <tr>
                                    <th class="right" width="1%">SL#</th>
                                    <th class="center" width="12%">Revenue Date</th>
                                    <th class="right" width="13%">Category</th>
                                    <th class="center">Description</th>
                                    <th class="right"width="8%">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($revenue_details as $key => $revenueDetails)
                                    <tr>
                                        <td class="center">{{ $key + 1 }}</td>
                                        <td class="left">{{ $revenueDetails->revenue_date}}</td>
                                        <td class="left">{{ $revenueDetails->revenueCategory->name }}</td>
                                        <td class="center">{{ $revenueDetails->description }}</td>
                                        <td class="text-end">{{number_format( $revenueDetails->amount,2) }}</td>
                                    </tr>
                                @endforeach

                                <tr id="subtotal" style="border-top:1px solid black;margin-top:14px">
                                    <td class="center"></td>
                                    <td class="left"></td>
                                    <td class="left"></td>
                                    <td><b>Sub Total</b></td>
                                    <td class="text-end"><b>{{number_format( $revenue_details->sum('amount'),2) }}</b></td>
                                </tr>
                                @if ($revenueInvoice->vat_rate != 0)
                                    <tr>
                                        <td class="center"></td>
                                        <td class="left"></td>
                                        <td class="left"></td>
                                        <td class="left collspan-2">
                                            <b>Vat Rate({{ number_format( $revenueInvoice->vat_rate,2) }}%)</b>
                                        </td>
                                        <td class="text-end">
                                            <b>{{number_format(  $revenue_details->sum('amount') + ($revenueInvoice->vat_rate / 100) * $revenue_details->sum('amount'),2) }}
                                            </b></td>
                                    </tr>
                                @endif
                                @if ($revenueInvoice->adjustment_balance != 0)
                                    <tr>
                                        <td class="center"></td>
                                        <td class="left"></td>
                                        <td class="left"></td>
                                        <td class="left">
                                            @if ($revenueInvoice->adjustment_type == 1)
                                                <b> Addition</b>
                                            @elseif($revenueInvoice->adjustment_type == 2)
                                                <b> Subtraction </b>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="text-end"><b>{{ number_format(  $revenueInvoice->adjustment_balance,2) }}</b>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="center"></td>
                                    <td class="left"></td>
                                    <td class="left"></td>
                                    <td class="left">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format( $revenueInvoice->total,2) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12 ">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th> Amount In Word</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row"><strong>{{$totalString }} Taka Only</strong>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-12 ">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Approved By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row" style="height:35px"><strong>@if($revenueInvoice->status == 2 ){{ $transaction->updatedByUser->name }}@else -- @endif</strong>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</body>

</html>
