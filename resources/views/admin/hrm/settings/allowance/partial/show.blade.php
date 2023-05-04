<table class="table">
    <tr>
        <th>Home Allowance</th>
        <th id="homme_all">{{$allowance->home_allowance}}</th>
    </tr>
    <tr>
        <th>Medical Allowance</th>
        <th id="med_all">{{$allowance->medical_allowance}}</th>
    </tr>
    <tr>
        <th>Transport Allowance</th>
        <th id="trns_all">{{$allowance->transport_allowance}}</th>
    </tr>
    <tr>
        <th>Mobile Allowance</th>
        <th id="mob_all">{{$allowance->mobile_allowance}}</th>
    </tr>
    <tr>
        <th>Leave Allowance</th>
        <th id="leave_all">{{$allowance->leave_allowance}}</th>
    </tr>
    <tr>
        <th>Description</th>
        <th id="description">{!! $allowance->description !!}</th>
    </tr>
</table>
