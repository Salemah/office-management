@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="fw-semibold">Successfully completed!</div> {{ session('success') }}
        <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif (session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
         {{ session('message') }}
        <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>

@elseif (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-semibold">Error!</div> {{ session('error') }}
        <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
