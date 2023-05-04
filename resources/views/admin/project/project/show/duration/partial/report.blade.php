@push('css')
    <style>

    </style>
@endpush


<h3 class="text-center text-success ">Project Duration</h3>
        <span class="badge bg-success my-3 py-2">Total Spent Day : <b> <span class="text-dark" id="spent_day" ></span>  </b></span>
        <span class="badge bg-warning my-3 py-2">Total Due Day : <b> <span class="text-dark" id="due_day">{{$total_hour}}</span>  </b></span>
        <span class="badge bg-success my-3 py-2">Total Hour : <b> <span class="text-danger">{{$total_hour}}</span>  </b></span>
        <span class="badge bg-danger my-3 py-2">Total Hour : <b> <span class="text-lite">{{$total_hour}}</span>  </b></span>
<table class="table table-bordered   table-hover  ">
    <thead class="table-primary ">
      <tr>
        <th scope="col">Type</th>
        <th scope="col">Start Date</th>
        <th scope="col">End Date</th>
        <th scope="col">Estimate Day</th>
        <th scope="col">Estimate Hour</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($projects as $key=>$project)
        <tr>
            <td>@if($key == 0) <p class="text-primary">Initial</p> @else <p class="text-danger"> Extend  </p> @endif </td>
            <td>{{$project->start_date}} </td>
            <td>{{$project->end_date}}</td>
            <td>{{$project->total_day}}</td>
            <td>{{$project->total_hour}}</td>
          </tr>
        @endforeach
           <tr>
            <td colspan="4" style="text-align: right;font-weight:bold"> Total Project Day:{{$total_day}}</td>
            <td colspan="1" style="text-align: right;font-weight:bold"> Total Project Hour :{{$total_hour}}</td>
          </tr>
    </tbody>
</table>

<h5>Module Report</h5>
<table class="table table-bordered   table-hover  ">
    <thead class="table-success ">
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Start Date</th>
        <th scope="col">End Date</th>
        <th scope="col">Status</th>
        <th scope="col">Day</th>
        <th scope="col">Hour/Day</th>
        <th scope="col">Total Hour</th>
        <th scope="col" style="background: green;color:white">Spent Day</th>
        <th scope="col" style="background: red">Due Day</th>
      </tr>
    </thead>
    <tbody>
       @foreach ($projectModules  as $key=>$project)
       <tr >
            <td>{{$project->module_name}}</td>
            <td>{{$project->module_start_date}}</td>
            <td>{{$project->module_end_date}}</td>
            <td>@if ($project->status ==1)
                Up Coming
                @elseif ($project->status == 2)
                On Going
                @elseif ($project->status == 3)
                Complete
                @elseif ($project->status == 4)
                Cancel
                @elseif ($project->status == 5)
                On Hold
                @endif
            </td>
            <td>{{$project->module_total_day }}</td>
            <td>{{$project->module_estimate_hour}}</td>
            <td>{{$project->module_final_hour}}</td>
            <td>
                @php
                    $date = \Carbon\Carbon::parse($project->module_start_date);
                   $now = \Carbon\Carbon::now();
                   $diff = $date->diffInDays($now);
                @endphp
                {{$diff}}
            </td>
            <td>{{$project->module_final_hour}}</td>
        </tr>

     @endforeach
        <tr>
            <td colspan="4" style="text-align: right;font-weight:bold"> Total Estmate Day : {{$module_day}}</td>
            <td colspan="2" style="text-align: right;font-weight:bold">Total Estmate Hour :{{$module_hour}}</td>
        </tr>
    </tbody>
</table>


@push('script')
<script>
    $(document).ready(function () {
        let projectCount =$('#project_count').val();
        // for (let i = 0; i < projectCount; i++) {
            var date1 = new Date($('#project_start_date_0').val());
            var date3 = new Date($('#project_end_date_0').val());
            var date2 = new Date();
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
                document.getElementById('spent_day').innerHTML =  diffDays  ;
            var dueDays = parseInt((date3 - date2) / (1000 * 60 * 60 * 24), 10) +1;
                   document.getElementById('due_day').innerHTML =  dueDays  ;

        // }
        });

    function calculateDay(){

            var date1 = new Date($('#start_date').val());
            var date2 = new Date($('#end_date').val());
            if(date2 < date1){
                swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "  Less Than "+ date1,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#end_date').val('');
                        }
                    });
            }
            else{
                var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
                $('#duration_total_day').val(diffDays);

            }
        }
</script>
@endpush
