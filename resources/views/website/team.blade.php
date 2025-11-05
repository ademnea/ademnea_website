<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>AdEMNEA | Our Team</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Raleway:300,400,500,700|Poppins:300,400,500,600,700" rel="stylesheet">

  @include('website.links')

  <style>
    /*--------------------------------------------------------------
    # Team Section
    --------------------------------------------------------------*/
    .team .team-member .member-img {
      height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .team .team-member .member-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 8px;
    }

    .team .team-member .social {
      position: absolute;
      left: 0;
      top: -18px;
      right: 0;
      opacity: 0;
      transition: ease-in-out 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .team .team-member .social a {
      color: blue;
      background: #0ea2bd;
      margin: 0 5px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      transition: 0.3s;
    }

    .team .team-member .social a:hover {
      background: #1ec3e0;
    }

    .team .team-member .member-info {
      padding: 30px 15px;
      text-align: center;
      box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.1);
      background: #ffffff;
      margin: -50px 20px 0 20px;
      position: relative;
      border-radius: 8px;
    }

    .team .team-member .member-info h4 {
      font-weight: 400;
      margin-bottom: 5px;
      font-size: 24px;
      color: #485664;
    }

    .team .team-member .member-info span {
      display: block;
      font-size: 16px;
      font-weight: 400;
      color: var(--color-gray);
    }

    .team .team-member .member-info p {
      font-style: italic;
      font-size: 14px;
      line-height: 26px;
      color: #6c757d;
      text-align: left;
    }

    .team .team-member:hover .social {
      opacity: 1;
    }
  </style>
</head>

<body>

  <!-- ======= Top Bar ======= -->
  @include('website.top_bar')

  <!-- ======= Header ======= -->
  @include('website.header')

<main id="main">

  <!-- ======= Breadcrumb / Page Title ======= -->
  <section class="breadcrumbs py-3 bg-light text-center">
    <div class="container">
      <h2>Our Team</h2>
      <p class="text-muted">AdEMNEA core Research, PhD, and Intern team profiles</p>
    </div>
  </section>

  <!-- ======= Researchers Section ======= -->
  <section id="researchers" class="team section-bg py-4">
    <div class="container" data-aos="fade-up">
      <h2 class="text-center mb-3">Our Researchers</h2>
      <p class="text-center mb-4">AdEMNEA core research team and their profiles</p>

      @if($researchers->count())
      <div class="row gy-4">
        @foreach($researchers as $team)
        <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
          <div class="team-member">
            <div class="member-img">
              <img src="{{ asset('images/' . $team->image_path) }}" class="img-fluid" alt="{{ $team->name }}">
            </div>
            <div class="member-info">
              <h4>{{ $team->name }}</h4>
              <span>{{ $team->title }}</span>
              @php
              $words = explode(' ', $team->description);
              $shortDescription = implode(' ', array_slice($words, 0, 40));
              @endphp
              <p>
                <span id="short-{{ $team->id }}">{{ $shortDescription }}...</span>
                <span id="full-{{ $team->id }}" style="display: none;">{{ $team->description }}</span>
                @if(count($words) > 40)
                <a href="javascript:void(0);" onclick="toggleDescription({{ $team->id }})"
                  class="text-blue-500" id="toggle-{{ $team->id }}">Read More</a>
                @endif
              </p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p class="text-center">No researchers found.</p>
      @endif
    </div>
  </section>

  <!-- ======= PhD Students Section ======= -->
  <section id="phd-students" class="team py-4">
    <div class="container" data-aos="fade-up">
      <h2 class="text-center mb-3">Our PhD Students</h2>
      <p class="text-center mb-4">Meet our PhD students at AdEMNEA</p>

      @if($phdStudents->count())
      <div class="row gy-4">
        @foreach($phdStudents as $team)
        <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
          <div class="team-member">
            <div class="member-img">
              <img src="{{ asset('images/' . $team->image_path) }}" class="img-fluid" alt="{{ $team->name }}">
            </div>
            <div class="member-info">
              <h4>{{ $team->name }}</h4>
              <span>{{ $team->title }}</span>
              @php
              $words = explode(' ', $team->description);
              $shortDescription = implode(' ', array_slice($words, 0, 40));
              @endphp
              <p>
                <span id="short-{{ $team->id }}">{{ $shortDescription }}...</span>
                <span id="full-{{ $team->id }}" style="display: none;">{{ $team->description }}</span>
                @if(count($words) > 40)
                <a href="javascript:void(0);" onclick="toggleDescription({{ $team->id }})"
                  class="text-blue-500" id="toggle-{{ $team->id }}">Read More</a>
                @endif
              </p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p class="text-center">No PhD students found.</p>
      @endif
    </div>
  </section>


  <!-- ======= Interns Section ======= -->
  <section id="interns" class="team section-bg py-4">
    <div class="container" data-aos="fade-up">
      <h2 class="text-center mb-3">Our Interns</h2>
      <p class="text-center mb-4">Meet our AdEMNEA interns</p>

      @if($interns->count())
      <div class="row gy-4">
        @foreach($interns as $team)
        <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
          <div class="team-member">
            <div class="member-img">
              <img src="{{ asset('images/' . $team->image_path) }}" class="img-fluid" alt="{{ $team->name }}">
            </div>
            <div class="member-info">
              <h4>{{ $team->name }}</h4>
              <span>{{ $team->title }}</span>
              @php
              $words = explode(' ', $team->description);
              $shortDescription = implode(' ', array_slice($words, 0, 40));
              @endphp
              <p>
                <span id="short-{{ $team->id }}">{{ $shortDescription }}...</span>
                <span id="full-{{ $team->id }}" style="display: none;">{{ $team->description }}</span>
                @if(count($words) > 40)
                <a href="javascript:void(0);" onclick="toggleDescription({{ $team->id }})"
                  class="text-blue-500" id="toggle-{{ $team->id }}">Read More</a>
                @endif
              </p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p class="text-center">No interns found.</p>
      @endif
    </div>
  </section>

</main>

  <!-- ======= Footer ======= -->
  @include('website.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  @include('website.scripts')

  <script>
    function toggleDescription(id) {
      let shortDesc = document.getElementById('short-' + id);
      let fullDesc = document.getElementById('full-' + id);
      let toggleBtn = document.getElementById('toggle-' + id);

      if (shortDesc.style.display === 'none') {
        shortDesc.style.display = 'inline';
        fullDesc.style.display = 'none';
        toggleBtn.innerText = 'Read More';
      } else {
        shortDesc.style.display = 'none';
        fullDesc.style.display = 'inline';
        toggleBtn.innerText = 'Read Less';
      }
    }
  </script>

</body>
</html>
