<nav class="navbar navbar-expand navbar-light navbar-top">
    <div class="container-fluid">
        <a href="#" class="burger-btn d-block"><i class="bi bi-justify fs-3"></i></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-lg-0">
                
            </ul>
            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-menu d-flex">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 text-gray-600">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="user-img d-flex align-items-center">                                            
                            @if (Auth::user()->profile)
                            <div class="round-image-3">
                                <img class="w-100 active" src="{{ asset('storage/profil/' . Auth::user()->profile) }}" data-bs-target="#Gallerycarousel" data-bs-slide-to="0">
                            <div class="avatar avatar-md">
                            @else
                            <div class="avatar avatar-md">
                                <img class="w-100 active" src="{{ asset('dist/assets/images/faces/1.jpg') }}" data-bs-target="#Gallerycarousel" data-bs-slide-to="0">
                            </div>
                             @endif
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                    <li>
                        <h6 class="dropdown-header">Hello {{ Auth::user()->name }}</h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('user.profile.index')}}"><i class="icon-mid bi bi-person me-2"></i> My Profile</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="icon-mid bi bi-box-arrow-left me-2"></i> 
                        {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>