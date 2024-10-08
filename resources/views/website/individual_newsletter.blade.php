<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Adaptive Environmental Monitoring Networks for East Africa</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <style>
        #collapseExample.collapse:not(.show) {
            display: block;
            height: 3rem;
            overflow: hidden;
        }

        #collapseExample.collapsing {
            height: 3rem;
        }
    </style>
    @include('website.links')

    <!-- =======================================================
  * Template Name: Green - v4.3.0
  * Template URL: https://bootstrapmade.com/green-free-one-page-bootstrap-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <!-- ======= Top Bar ======= -->
    @include('website.top_bar')

    <!-- ======= Header ======= -->
    @include('website.header')
    <!-- End Header -->



    <main id="main">
        <!-- ======= Scholarship Section ======= -->
        <section id="scholarship" class="about"style="width:80%; margin: 0 110px;">
            <div class="container">

                <div
                    class="h5 text-center container"style="background-color:#5cb874; width:auto; height: auto; border-radius: 20px; padding:30px 20px; ">
                    <h3> <b><p>{!! $newsletter->title !!}</p></b></h3>

                    <hr>
                    <br>

                    {{-- <h5>Description:</h5> --}}
                    <p style=""><i>{!! $newsletter->description !!}</i></p>
                </div>
                <br>
                <br>

                <div class="h5 text-center container ">
                    <h1>News Article 1</h1>
                </div>

               {!! $newsletter->article !!}

                {{-- @if ($scholarships->count()) --}}
                {{-- <div class="section-title">
                    </div> --}}
                {{-- @foreach ($scholarships as $scholarship)
                        <div class="container card text-justify">
                            <div class="icon-box">
                                <p class="description">{!! $scholarship->instructions !!}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>There are currently no scholarships</p>
                @endif --}}
            </div>
        </section>

        <!-- End Scholarship Section -->



    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    @include('website.footer')
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('website.scripts')

</body>

</html>
