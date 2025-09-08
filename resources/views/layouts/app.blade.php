<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AdeMNEA</title>
    @include('layouts.links')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.1.4/dist/tailwind.min.css">

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="
             https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js
             "></script>

    <!--Map Scripts-->
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    <script
        src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AvvQV4ch_5cSpbgNw94ftFt3Xsy41cLVIBmOuhrd3WRvTFzKW2kLP09WeNPSfgrg"
        async defer></script>

    <script src="https://kit.fontawesome.com/347c42edee.js" crossorigin="anonymous"></script>
    
</head>

<body>

    @include('layouts.navbar')
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="p-4 sm:ml-64 bg-white mt-5">
        @yield('content')
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>

    @include('layouts.scripts')
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    @yield('page_scripts')
    <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>   -->

    @yield('scripts')


</body>

</html>
