<header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center">

        {{-- <h1 class="logo me-auto"><a href="/">AdEMNEA</a></h1> --}}
        <img class="logo me-auto" src="{{ asset('dash/logo2.png') }}" alt="AdEMNEA">
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

        <nav id="navbar" class="navbar">
            <ul>
                <li><a class="nav-link scrollto active" href="/">Home</a></li>
                <!-- <li class="dropdown"><a href="/#"><span>Work Packages</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>

                        <li><a href="/workpackages-wp1">Networks and Resilience(WP1)</a></li>
                        <li><a href="/workpackages-wp2">Sensors and signal processing(WP2)</a></li>
                        <li><a href="/workpackages-wp3">Data Analytics for Environment Monitoring services(WP3)</a></li>
                        <li><a href="/workpackages-wp4">Project Administration(WP4)</a></li>
                    </ul>
                </li> -->

                 <li><a class="nav-link" href="{{ route('map') }}">Apiary Map</a></li>

                <!-- <li class="dropdown scrollto dropdown"><a href="/" class="nav-link scrollto"><span>Research
                            Profile</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li class="dropdown scrollto dropdown"><a href="/"><span>Masters</span><i
                                    class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li><a href="/mastersprofile-sudan">South Sudan</a></li>
                                <li><a href="/mastersprofile-uganda">Uganda</a></li>
                                <li><a href="/mastersprofile-tanzania">Tanzania</a></li>
                            </ul>
                        </li>
                        <hr>
                        <li class="dropdown scrollto dropdown"><a href="/"><span>PhD</span><i
                                    class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li><a href="/phdprofile-sudan">South Sudan</a></li>
                                <li><a href="/phdprofile-uganda">Uganda</a></li>
                                <li><a href="/phdprofile-tanzania">Tanzania</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> -->
               <!--Commented ou the Bee monitor so as to remove it from our tab -->
                {{-- {{-- <li class="dropdown"><a href="/#"><span>Bee Monitor</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>

                        <li><a href="/admin/farm">Farm Hives</a></li>                        
                        <li><a href="/">Map</a></li>                       
                    </ul>

                </li> --}}



                <!-- A tab for the scholarship -->
                 <li><a  href="/displayscholarships">Scholarships</a></li>


                <li><a href="/displaypublication" class="nav-link scrollto"><span>Publications</span> </a>
                
                    {{-- <ul>
                        <li class="dropdown scrollto dropdown"><a href="/"><span>Masters</span><i
                                    class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li><a href="/mastersscholarship-sudan">South Sudan</a></li>
                                <li><a href="/mastersscholarship-uganda">Uganda</a></li>
                                <li><a href="/mastersscholarship-tanzania">Tanzania</a></li>
                            </ul>
                        </li>
                        <hr>
                        <li class="dropdown scrollto dropdown"><a href="/"><span>PhD</span><i
                                    class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li><a href="/phdscholarship-sudan">South Sudan</a></li>
                                <li><a href="/phdscholarship-uganda">Uganda</a></li>
                                <li><a href="/phdscholarship-tanzania">Tanzania</a></li>
                            </ul>
                        </li>
                    </ul> --}}
                </li>

                <li><a href="/displaynewsletter"><span>Blog</span></a></li>

                <li><a  href="/displayevent">Gallery</a></li>


                <!-- <li><a class="nav-link scrollto" href="/displaynewsletter">Newsletters</a></li> -->
                <!-- <li><a class="nav-link scrollto" href="/displaypublication">Publications</a></li> -->

                {{-- <li class="dropdown"><a href="#"><span>News & Events</span> <i class="bi bi-chevron-down"></i></a> --}}
                {{-- <ul> --}}
                {{-- <li><a class="nav-link scrollto" href="/displaynewsletter">Newsletters</a></li> --}}
                {{-- <li><a class="nav-link scrollto" href="/displaypublication">Publications</a></li> --}}
                {{-- </ul> --}}
                {{-- </li> --}}

                <li><a class="nav-link" href="{{ route('team') }}">Team</a></li>
                <li><a class="nav-link scrollto" href="/login">Login</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

    </div>
</header>
