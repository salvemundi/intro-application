<header class="header" id="header">
    <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
</header>
<div class="l-navbar" id="nav-bar">
    <nav class="nav">
        <div>
            <a href="/dashboard" class="nav_logo">
                <img src="{{ asset('images/logo.svg') }}" width="40" height="40"/>
                <span class="nav_logo-name">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="nav_list">
                <a id="dashboard" href="/dashboard" class="nav_link">
                    <i class="fas fa-tachometer-alt dashboard"></i>
                    <span class="nav_name">Dashboard</span>
                </a>
                <a id="qrcode" href="/qrcode" class="nav_link">
                    <i class="fas fa-qrcode nav_icon"></i>
                    <span class="nav_name">QR code</span>
                </a>
{{--                <a id="registrations" href="/registrations" class="nav_link">--}}
{{--                    <i class='bx bx-user nav_icon'></i> <span class="nav_name">Aanmeldingen</span>--}}
{{--                </a>--}}
                <a id="participants" href="/participants" class="nav_link">
                    {{--<i class="bx bi-clipboard-check fixIconClipboard"></i>--}}
                    <i class='bx bx-user nav_icon'></i>
                    <span class="nav_name">Deelnemers</span>
                </a>
                <a id="blogs" href="/blogsadmin" class="nav_link">
                    <i class="fas fa-newspaper fixIconClipboard"></i>
                    <span class="nav_name">Blogs / Nieuws</span>
                </a>
                <a id="add" href="/add" class="nav_link">
                    <i class='bx bi bi-plus-circle circlePlus'></i>
                    <span class="nav_name">Voeg toe</span>
                </a>
                <a id="bus" href="/bus" class="nav_link">
                    <i class='fas fa-bus bus'></i>
                    <span class="nav_name">Bus</span>
                </a>
            </div>
        </div>
        <a href="/signout" class="nav_link">
            <i class='bx bx-log-out nav_icon'></i>
            <span class="nav_name">Log uit</span>
        </a>
    </nav>
</div>
