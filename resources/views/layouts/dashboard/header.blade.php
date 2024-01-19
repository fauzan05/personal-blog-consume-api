<header class="position-fixed z-3">
    <nav class="m-4 shadow-sm rounded main-navbar d-flex flex-row justify-content-around">
        <ul class="d-flex gap-3 align-items-center justify-content-center" style="width: auto">
            {{-- <li><img class="logo" src="{{asset('logo/logo.png')}}" alt=""></li> --}}
            <li>
                <img class="logo" src="{{ asset('logo/logo.svg') }}" alt="">
            </li>
            <li class="title">Admin Dashboard</li>
        </ul>
        <ul class="d-flex align-items-center justify-content-around">
            <li><a  class="{{(integer)Cookie::get('current-page') == 1 || !isset($_COOKIE['current-page']) ? 'active' : ''}} admin-page-button" href="#"><i class="fa-solid fa-house"></i></a></li>
            <li><a  class="{{(integer)Cookie::get('current-page') == 2 ? 'active' : ''}} admin-page-button" href="#">Postingan</a></li>
            <li><a  class="{{(integer)Cookie::get('current-page') == 3 ? 'active' : ''}} admin-page-button" href="#">Pengaturan</a></li>
            <li><a  class="{{(integer)Cookie::get('current-page') == 4 ? 'active' : ''}} admin-page-button" href="#">Tentang</a></li>
        </ul>
        <li><span class="section mt-2"></span></li>
        <ul class="d-flex me-5 dark-mode align-items-center justify-content-center" style="width: auto;">
            <div id="dark-mode-switch">
                <li><i class="fa-solid fa-sun"></i></li>
                <li><i class="fa-solid fa-moon"></i></li>
            </div>
        </ul>
        <ul class="d-flex me-5 align-items-center justify-content-center" style="width: auto;">
            <li><a href="{{url('logout')}}"><i id="logout-button" class="logout-button fa-solid fa-power-off mt-2" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Logout"></i></a></li>
        </ul>
    </nav>
</header>

