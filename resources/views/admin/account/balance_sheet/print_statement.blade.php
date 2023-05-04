<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Statement</title>
    <script src="https://kit.fontawesome.com/34590e0ca8.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <style>
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
            @media print {
                .print {
                    display: none;
                }
                #back{
                    display: none;
                }
            }
            th ,td{
                padding:1px !important;

            }
           td{
                padding:1px !important;

            }
            .table-head{
                background: rgb(68, 68, 68);
                color: white;
            }
            .taka-border-head{
                width: 15%;
            }
            .balance{
                font-size: 20px;
            }
            #debit{
                font-size: 20px;
            }
            #credit{
                font-size: 20px;
            }
            #total{
                font-size: 20px;
            }
            .signature{
                border-top: 1px solid black;
            }
            /* .signature_shams{
                margin-top: 20px;
            } */
    </style>
</head>
<body>
<!--End Alert -->
<div class="container">
    <div class="row">
        <div class=" mb-4">
            <div class="container mt-3">
                <div class="sub2-row16">
                    <div class="l26 md-heading">
                        <a href="#" class="print btn btn-sm btn-info" onclick="window.print()"
                            class="btn btn-inverse waves-effect waves-light"><label for="">Print</label> <i
                                class="fa fa-print f-16"></i></a>
                        <a href="{{ route('admin.account.monthly.balance.sheet') }}" class="btn btn-sm btn-dark "
                            id="back">Back to list</a>
                    </div>
                </div>
            </div>
            <div class="row ">
                <table class="table table-bordered border-dark">
                    <thead>
                        <tr >
                            <td colspan="5"><h1 class="text-center">Account Statement For {{$monthName}} {{$year}}</h1></td>
                        </tr>
                      <tr>
                        {{-- <th width="2%" class="text-center">SL#</th> --}}
                        {{-- <th width="5%"> Transaction Date</th> --}}
                        <th width="8%" class="text-center"> Date</th>
                        <th width="57%" class="text-center"> Perticular</th>
                        <th width="11%" class="text-center"> Debit</th>
                        <th width="11%" class="text-center"> Credit</th>
                        <th width="11%" class="text-center"> Balance</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr id="previous_tr" class="bg-secondary" >
                            <td class="text-center text-light">{{Carbon\Carbon::parse($start_date)->format('d/m/Y') }}</td>
                            <td  class="text-start text-light">Closing Balance</td>
                            <td  class="text-end text-light">@if($postBalance<0){{number_format($postBalance,2)}} @else 0.00 @endif</td>
                            <td  class="text-end text-light">@if($postBalance>0 ){{number_format($postBalance,2)}} @else 0.00 @endif</td>
                            <td id="prevBalance" class="text-light text-end" >{{number_format($postBalance,2)}}</td>
                        </tr>
                        @foreach ($data as $key=>$item)
                            <tr>
                                {{-- <th >{{$key+1}}</th> --}}
                                <td class="text-center">{{Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                                <td >{!!$details[$key]!!}</td>
                                <td class="text-end">{{number_format($debit[$key],2)}}</td>
                                <td class="text-end">{{number_format($credit[$key],2)}}</td>
                                <td  class="text-end">{{number_format($balance[$key],2)}}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="2" class="text-end balance" ><b>Total</b></td>
                            <td class="text-end" id="debit"><b>{{number_format($totalDebit,2)}}</b></td>
                            <td class="text-end" id="credit"><b>{{number_format($totalCredit,2)}}</b></td>
                            <td class="text-end" id="total"><b>{{number_format($totalBalance,2)}}</b></td>
                        </tr>
                    </tbody>
                  </table>

            </div>
        </div>
    </div>
    <div class="container text-center mt-5 ">
        <div class="row align-items-start">
          <div class="col">
            <span class="text-center" >{{Auth::user()->name}}</span>
            <div class="signature" >
                Created By
            </div>

          </div>
          <div class="col mt-4 ">

           <div class="signature ">
                    Checked By
           </div>
          </div>
          <div class="col mt-4">

               <div class="signature ">
                     Managing Director/Chairman
               </div>
          </div>
        </div>
      </div>
</div>

{{-- <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
