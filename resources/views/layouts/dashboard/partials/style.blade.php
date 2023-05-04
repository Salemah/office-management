<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('dashboard/assets/favicon/apple-icon-57x57.png') }}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('dashboard/assets/favicon/apple-icon-60x60.png')}}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('dashboard/assets/favicon/apple-icon-72x72.png')}}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard/assets/favicon/apple-icon-76x76.png')}}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('dashboard/assets/favicon/apple-icon-114x114.png')}}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('dashboard/assets/favicon/apple-icon-120x120.png')}}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('dashboard/assets/favicon/apple-icon-144x144.png')}}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('dashboard/assets/favicon/apple-icon-152x152.png')}}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('dashboard/assets/favicon/apple-icon-180x180.png')}}">
<link rel="icon" type="image/png" sizes="192x192" href="@if($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->favicon)}} @else {{asset('img/no-image/noman.jpg')}}  @endif ">
<link rel="icon" type="image/png" sizes="32x32" href="@if($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->favicon)}} @else {{asset('img/no-image/noman.jpg')}}@endif  ">
<link rel="icon" type="image/png" sizes="96x96" href="@if($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->favicon)}} @else {{asset('img/no-image/noman.jpg')}}@endif ">
<link rel="icon" type="image/png" sizes="16x16" href="@if($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->favicon)}} @else {{asset('img/no-image/noman.jpg')}}@endif">
<link rel="manifest" href="{{ asset('dashboard/assets/favicon/manifest.json')}}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="@if($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->favicon)}} @else {{asset('img/no-image/noman.jpg')}} @endif  ">
<meta name="theme-color" content="#ffffff">
<!-- Vendors styles-->
<link rel="stylesheet" href="{{ asset('dashboard/vendors/simplebar/css/simplebar.css')}}">
<link rel="stylesheet" href="{{ asset('dashboard/css/vendors/simplebar.css')}}">
<link rel="stylesheet" href="{{ asset('css/custom/dataTable-custom.css')}}">
<!-- Main styles for this application-->
<link href="{{ asset('dashboard/css/style.css')}}" rel="stylesheet">
<!-- We use those styles to show code examples, you should remove them in your application.-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.23.0/themes/prism.css">
<link href="{{ asset('dashboard/css/examples.css')}}" rel="stylesheet">

<!-- Box Icon -->
<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

<!-- Toastr -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


<!-- Global site tag (gtag.js) - Google Analytics-->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>

<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    // Shared ID
    gtag('config', 'UA-118965717-3');
    // Bootstrap ID
    gtag('config', 'UA-118965717-5');
</script>
<link href="{{ asset('vendors/@coreui/chartjs/css/coreui-chartjs.css')}}" rel="stylesheet">

@stack('css')
