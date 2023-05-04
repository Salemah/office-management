<table class="table">
    <tr>
        <th>Days</th>
        <th id="homme_all">{{$data->days}}</th>
    </tr>
    <tr>
        <th>Paid Per Days</th>
        <th id="med_all">{{$data->paid_per_day}}</th>
    </tr>
    <tr>
        <th>Total Amount</th>
        <th id="trns_all">{{$data->total_amount}}</th>
    </tr>
    <tr>
        <th>Description</th>
        <th id="description">{!! $data->description !!}</th>
    </tr>
</table>
