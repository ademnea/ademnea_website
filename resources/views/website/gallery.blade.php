<style>
.gallery-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
  height: 100%;
}

.gallery-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.gallery-card .carousel-item img {
  height: 300px;
  object-fit: cover;
  width: 100%;
}

.gallery-card .card-content {
  padding: 1.5rem;
}

.gallery-card .venue {
  color: #5cb874;
  font-weight: 600;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.gallery-card .title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 1rem;
}

.gallery-card .description {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.gallery-card .read-more {
  color: #5cb874;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
}

.gallery-card .read-more:hover {
  color: #4a9960;
}

.section-title h2 {
  margin-bottom: 3rem;
}
</style>

<!-- resources/views/website/gallery.blade.php -->
<section id="events" class="events">
  <div class="container">
    <div class="section-title">
      <h2>Events</h2>
    </div>

<div class="row">
  @if($events->count())
    @foreach ($events as $event)
      <div class="col-lg-6 col-md-6 mb-4">
        <div class="gallery-card">
          <a href="/gallery_view?id={{ $event->id }}" style="text-decoration: none; color: inherit;">
            @if ($event->photos->count() > 0)
              <div id="carouselExampleAutoplaying{{ $event->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  @foreach ($event->photos as $key => $photo)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                      <img src="{{ asset('images/events/' . $photo->photo_url) }}" alt="Event photo">
                    </div>
                  @endforeach
                </div>
                @if($event->photos->count() > 1)
                  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying{{ $event->id }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying{{ $event->id }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                @endif
              </div>
            @else
              <div class="d-flex align-items-center justify-content-center" style="height: 300px; background: #f8f9fa;">
                <p class="text-muted">No photos available</p>
              </div>
            @endif
          </a>
          
          <div class="card-content">
            <div class="venue">
              <i class="bi bi-geo-alt"></i> {{ $event->venue }}
            </div>
            <h3 class="title">{{ $event->title }}</h3>
            <p class="description">
              {{ \Illuminate\Support\Str::words($event->description, 15, '...') }}
            </p>
            <a data-bs-toggle="modal" data-bs-target="#modal{{ $event->id }}" class="read-more">
              Read More
            </a>
          </div>

        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="modal{{ $event->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $event->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel{{ $event->id }}">{{ $event->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <strong class="text-success">Venue:</strong> {{ $event->venue }}
                </div>
                <div class="mb-3">
                  <strong>Description:</strong>
                </div>
                <p>{!! nl2br(e($event->description)) !!}</p>
              </div>
              <div class="modal-footer">
                @if($event->article_link)
                  <a href="{{ $event->article_link }}" target="_blank" class="btn btn-success">
                    Read Full Article
                  </a>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @else
    <p>No events yet</p>
  @endif
</div>


  </div>
</section>


