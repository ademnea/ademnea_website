<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Adaptive Environmental Monitoring Networks for East Africa</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <style>
      #collapseExample.collapse:not(.show) {
        display: flex;
        height: 13rem;
        overflow: hidden;
      }

      #collapseExample.collapsing {
        height: 3rem;
      }

    .box{
   border: none;
  }
  .carousel-inner{
    border-radius: 40px;
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
                    <!-- ======= Album Section ======= -->
      <section id="gallery" class="gallery">
        
          <div class="container">
                  <div class="section-title">
                    <h2>Gallery</h2>
                  </div>
              <div class="container1">
              @foreach($gallery as $item)
              <div class="h4 text-center">
                {{$item -> title}}
              </div>

              <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner" style="height: 500px;">
                <div class="carousel-item active">
                  <img src="{{asset('image/' . explode('|', $item->image)[0])}}" class="d-block w-100" />
                </div>
                @foreach(explode("|", $item->image) as $image)
                <div class="carousel-item">
                    <img src="{{asset('image/' . $image)}}" class="d-block w-100" />
                    </div>
                  @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
              </div>
                <div class="text-center">
                  {{$item -> description}}
                </div>
                <br>
                <br>
                @endforeach
                </div>
            </div>
      </section>
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    @include('website.footer')
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('website.scripts')

    <script>
      img = document.getElementById("img1");

      function enlargeImg(){
        img.style.transform = "scale(1.5)";
        img.style.transition = "transform 0.25s ease";
      }
    </script>

</body>

</html>
