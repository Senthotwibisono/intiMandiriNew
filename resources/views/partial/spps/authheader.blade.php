<div class="dropdown">
                                <a href="#" id="topbarUserDropdown" class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
                                   
                                    @if (Auth::user()->profil)
                                            <div class="avatar avatar-md2 ">
                                        <img class="round-image-3" src="{{ asset('profil/' .Auth::user()->profil) }}" alt="Avatar">
                                        </div>
                                    @else
                                    <div class="avatar avatar-md2" >
                                        <img src="{{asset('dist/assets/images/faces/1.jpg')}}" alt="Avatar">
                                    </div>
                                    @endif
                                    <div class="text">
                                        <h6 class="user-dropdown-name">{{ Auth::user()->name }}</h6>
                                        <p class="user-dropdown-status text-sm text-muted">{{ Auth::user()->email }}</p>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                                  
                                  <li><a class="dropdown-item" href="/profile">Settings</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                                class="icon-mid bi bi-box-arrow-left me-2"></i> 
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form></li>
                                </ul>
                            </div>
