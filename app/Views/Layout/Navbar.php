<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">

        <a class="navbar-brand" >Hi, <?= (logged_in()) ? $nama : 'Kamu'  ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="nav justify-content-end nav-tabs">
            
            <li class="nav-item">
                <a class="nav-link <?= ($routes == 'comics/creates' ) ? 'active' : ' ' ?>"    href="/comics/creates">Daftar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($routes == 'comics/search') ? 'active' : ' ' ?>" href="/comics/search">Cari</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($routes == 'Comics' || $routes == '' ) ? 'active' : ' ' ?> " href="/Comics">Koleksi</a>
            </li>
        </ul>

        <?php if (logged_in()) : ?>
            <button type="button" class="btn btn-link" onclick="return confirm('apakah anda ingin logout?')">
                <a class="nav-item nav-link nav justify-content-end" href="/logout">Logout</a>
            </button>
        <?php else : ?>
            <a class="nav-item nav-link nav justify-content-end " href="/login">Login</a>
        <?php endif; ?>
    </div>
    </div>
</nav>