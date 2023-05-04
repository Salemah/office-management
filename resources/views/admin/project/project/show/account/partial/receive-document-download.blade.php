@if(count($documents) >0 )
<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Download</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($documents as  $key =>  $document )
            <tr>
                <th scope="row">{{$key+1 }}</th>
                <td>{{$document->document_name}}</td>
                <td><a class="btn btn-sm btn-success text-white" style="cursor:pointer" href="{{asset('img/budget/documents/'.$document->document_file)}}"> <i class="bx bxs-download"></i></a></td>
            </tr>
        @endforeach

    </tbody>
</table>

@else
<h1>No Documents</h1>
@endif
