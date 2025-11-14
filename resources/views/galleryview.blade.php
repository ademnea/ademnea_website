<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Gallery - AdEMNEA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/lightbox.css" type="text/css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .gallery-header {
            background: linear-gradient(135deg, #5cb874 0%, #4a9960 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
        }
        
        .back-btn {
            background-color: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-btn:hover {
            background-color: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        .gallery-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 1rem 0 0 0;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0;
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: white;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
        }
        
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        
        .gallery-overlay-content {
            color: white;
            width: 100%;
        }
        
        .view-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(92, 184, 116, 0.9);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover .view-icon {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.1);
        }
        
        .photo-count {
            background: rgba(92, 184, 116, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .no-photos {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        
        .no-photos i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="gallery-header">
        <div class="container">
            <a href="/displayevent" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                Back to Events
            </a>
            @foreach ($names as $name)
                <h1 class="gallery-title">{{ $name->title }}</h1>
            @endforeach
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="container mb-5">
        @if($events->count() > 0)
            <div class="photo-count">
                <i class="bi bi-images"></i>
                {{ $events->count() }} {{ $events->count() == 1 ? 'Photo' : 'Photos' }}
            </div>
            
            <div class="gallery-grid">
                @foreach ($events as $event)
                    <div class="gallery-item">
                        <a href="images/events/{{ $event->photo_url }}" data-lightbox="event-gallery" data-title="{{ $event->photo_url }}">
                            <img src="images/events/{{ $event->photo_url }}" alt="Event Photo" loading="lazy">
                            <div class="gallery-overlay">
                                <div class="gallery-overlay-content">
                                    <small>Click to view full size</small>
                                </div>
                            </div>
                            <div class="view-icon">
                                <i class="bi bi-zoom-in"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-photos">
                <i class="bi bi-images"></i>
                <h3>No Photos Available</h3>
                <p>This event doesn't have any photos yet.</p>
                <a href="/displayevent" class="btn btn-success mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Back to Events
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/lightbox-plus-jquery.js"></script>
</body>
</html>