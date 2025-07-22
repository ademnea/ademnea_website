<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Adaptive Environmental Monitoring Networks for East Africa (AdEMNEA)</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">


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

    <!-- ======= Hero Section ======= -->
    @include('website.hero_section')
    <!-- End Hero -->

    <main id="main">
    <!-- ======= About Us Section ======= -->
         
    @include('website.about_us')
    <!-- End About Us Section -->
       
    <!-- ======= Featured Services Section ======= -->
        @include('website.featured_services')
        <!-- End Featured Services Section -->

        

        <!-- ======= Scholarship Section ======= -->
        @include('website.scholarship')
        <!-- End Scholarship Section -->
         
        <!-- ======= Gallery Section ======= -->
        @include('website.gallery')
        <!-- End Gallery Section -->


        <!-- ======= Why Us Section ======= -->
        @include('website.why_us')
        <!-- End Why Us Section -->

        <!-- ======= Our Clients Section ======= -->
        @include('website.our_clients')
        <!-- End Our Clients Section -->

        <!-- ======= Services Section ======= -->
        {{-- @include('website.services') --}}
        <!-- End Services Section -->

        <!-- ======= Cta Section ======= -->
        {{-- @include('website.cta') --}}
        <!-- End Cta Section -->

        <!-- ======= Portfolio Section ======= -->
        {{-- @include('website.portfolio') --}}
        <!-- End Portfolio Section -->

        <!-- ======= Team Section ======= -->
        @include('website.team')
        <!-- End Team Section -->

        <!-- ======= Contact Section ======= -->
        @include('website.contact')
        <!-- End Contact Section -->

        <!-- ======= Map Section ======= -->
        @include('website.map')
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    @include('website.footer')
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('website.scripts')

</body>

</html>
