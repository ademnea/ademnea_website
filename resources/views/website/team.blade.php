<style>
/*--------------------------------------------------------------
# Team Section
--------------------------------------------------------------*/
.team .team-member .member-img {
  height: 250px; /* Or your desired fixed height */
  display: flex; /* To center the image vertically if needed */
  align-items: center; /* To center the image vertically if needed */
  justify-content: center; /* To center the image horizontally if needed */
  overflow: hidden; /* Ensure image overflow is hidden */
}
.team .team-member .member-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 8px; /* Added for rounded corners */
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
  transition: color 0.3s;
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
.team .team-member .social a i {
  line-height: 0;
  font-size: 16px;
}
.team .team-member .social a:hover {
  background: #1ec3e0;
}
.team .team-member .social i {
  font-size: 18px;
  margin: 0 2px;
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
  text-align: left; /* Added for left alignment */
}
.team .team-member:hover .social {
  opacity: 1;
}
    </style>
 <!-- ======= Team Section ======= -->
 <section id="team" class="team">
  <div class="container" data-aos="fade-up">
    <div class="section-title">
      <h2>Our Team</h2>
      <p>AdEMNEA core Research and Intern team and their profiles</p>
    </div>

<section id="team" class="team  py-0">
  <div class="container" data-aos="fade-up">
      <h2>Our Researchers</h2>
      <p>AdEMNEA core research team and their profiles</p>
  

    @if($researchers->count())
    <div class="row gy-5">
      @foreach($researchers as $team)
      <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
        <div class="team-member">
          <div class="member-img">
            <img src="{{ asset('images/' . $team->image_path) }}" class="img-fluid" alt="">
          </div>
          <div class="member-info">
            <div class="social">
              <a href=""><i class="bi bi-twitter"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
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
    <p>No researchers found.</p>
    @endif
  </div>
</section>


<section class="team">
  <div class="container" data-aos="fade-up">
      <h2>Our Interns</h2>
      <p>Meet our AdEMNEA interns</p>

    @if($interns->count())
    <div class="row gy-5">
      @foreach($interns as $team)
      <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
        <div class="team-member">
          <div class="member-img">
            <img src="{{ asset('images/' . $team->image_path) }}" class="img-fluid" alt="">
          </div>
          <div class="member-info">
            <div class="social">
              <a href=""><i class="bi bi-twitter"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
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
    <p>No interns found.</p>
    @endif
  </div>
</section>






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




                     