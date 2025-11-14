<section id="contact" class="contact">
    <div class="container">

        <div class="section-title">
            <h2>Contact</h2>
            {{-- <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit
                sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias
                ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p> --}}
        </div>

        <div class="row">

            <div class="col-lg-5 d-flex align-items-stretch">
                <div class="info">
                    <div class="address">
                        <i class="bi bi-geo-alt"></i>
                        <h4>Location:</h4>
                        <p>Level 3, Block B - College of Computing & Information Sciences (CoCIS), Makerere University, Kampala Uganda.</p>
                    </div>

                    <div class="email">
                        <i class="bi bi-envelope"></i>
                        <h4>Email:</h4>
                        <p>ademnea@cit.ac.ug</p>
                    </div>

                    <div class="phone">
                        <i class="bi bi-phone"></i>
                        <h4>Call:</h4>
                        <p>+256-772-310-038</p>
                    </div>


                </div>

            </div>

            <div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch">
                <div class="contact-form-wrapper w-100">
                    <div class="contact-form-header mb-4">
                        <h3 class="mb-2">Send us a Message</h3>
                        <p class="text-muted">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                    </div>
                    
                    <form action="{{ route('feedback.store') }}" method="post" role="form" class="contact-form">
                        @csrf
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    Your Name
                                </label>
                                <input type="text" name="name" class="form-control form-control-lg" id="name" 
                                       value="{{ old('name') }}" placeholder="" required>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    Your Email
                                </label>
                                <input type="email" class="form-control form-control-lg" name="email" id="email" 
                                       value="{{ old('email') }}" placeholder="" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="subject" class="form-label fw-semibold">
                                Subject
                            </label>
                            <input type="text" class="form-control form-control-lg" name="subject" id="subject" 
                                   value="{{ old('subject') }}" placeholder="" required>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="message" class="form-label fw-semibold">
                                Message
                            </label>
                            <textarea class="form-control" name="message" id="message" rows="8" 
                                      placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
