<!-- ajax -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- CoreUI and necessary plugins-->
<script src="{{ asset('dashboard/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/vendors/simplebar/js/simplebar.min.js') }}"></script>
<!-- Plugins and scripts required by this view-->
<script src="{{ asset('dashboard/vendors/chart.js/js/chart.min.js') }}"></script>
<script src="{{ asset('dashboard/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>
<script src="{{ asset('dashboard/vendors/@coreui/utils/js/coreui-utils.js') }}"></script>
<script src="{{ asset('dashboard/js/main.js') }}"></script>

<!-- Box Icon -->
<script src="https://unpkg.com/boxicons@2.1.2/dist/boxicons.js"></script>

<!-- Tostr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('toastr-success'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
    toastr.success("{{ session('toastr-success') }}");
    @endif
        @if(Session::has('toastr-error'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.error("{{ session('toastr-error') }}");
    @endif

        @if(Session::has('toastr-info'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.info("{{ session('toastr-info') }}");
    @endif
        @if(Session::has('toastr-warning'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.warning("{{ session('toastr-warning') }}");
    @endif
</script>

@stack('script')
