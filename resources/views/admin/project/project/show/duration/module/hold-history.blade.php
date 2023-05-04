<button type="button" class="btn btn-info position-relative">
    Total Hold
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
     {{count($data)}}
    </span>
  </button>
<table class="table mt-2">
    <thead class="table-info" >
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Hold Start Date</th>
            <th scope="col">Hold End Date</th>
        </tr>
      </thead>
        @foreach ($data as $key=>$hold)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$hold->project->project_title}}</td>
                <td>{{$module->start_date}}</td>
                <td class="text-danger fw-bolder">{{$module->end_date}}</td>
                <td>{{$hold->start_date}}</td>
                <td class="text-danger fw-bolder">{{$hold->end_date}}</td>
            </tr>
        @endforeach
</table>
