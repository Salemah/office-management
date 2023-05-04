<div class="container text-left">
    <div class="row">
        <div class="col-10 align-self-start">
            <div class="row">
                <div class="col-6 col-sm-6 col-md-6">
                    <p><span class="modal-details"><i class='bx bx-calendar me-1 text-danger'></i> Date :</span> <span class="text-info"><b>{{Carbon\Carbon::parse($data->date)->format('M d Y')}}</b></span></p>
                    <p><span class="modal-details"><i class='bx bx-timer me-1 text-danger'></i> Time : </span><span class="text-info"><b>{{Carbon\Carbon::parse($data->time)->format('h:i a')}}</b></span></p>
                </div>

                <div class=" col-6 col-sm-6 col-md-6">
                    <p><span class="modal-details"><i class='bx bxs-user text-primary me-1'></i>Client :</span> {{$data->createdBy->name}}</p>
                    <p><span class="modal-details"><i class='bx bxs-user text-primary me-1'></i>Added By :</span> {{$data->createdBy->name}}</p>
                </div>
                {{-- <div class=" col-6 col-sm-6 col-md-6">
                    <span class="modal-details"><i class='bx bx-shape-triangle me-1 mb-3'></i> Status :
                        @if ($data->status == 0)
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-primary">Complete</span>
                        @endif
                    </span>
                </div> --}}
                <p>
                    <span class="modal-details " >
                        <i class='bx bx-info-circle me-1 text-danger'></i>
                        Details : {!! $data->reminder_note !!}</span>

                </p>

            </div>
            <p><span class="reminder-label " ><i class='bx bxs-vial me-1 text-danger'></i>Status:</span>
                @if ($data->status == 0)
                    <span class="badge bg-warning text-dark">Pending</span>
                @else
                    <span class="badge bg-primary">Complete</span>
                @endif
            </p>


        </div>
    </div>
</div>
