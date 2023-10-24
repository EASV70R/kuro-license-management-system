<?php
require_once './kuro/require.php';

Util::IsAdmin();

Util::Header();
?>

<main class="container-fluid mt-5">
    <div class="testcontainer">
        <div class="row">
            <aside class="col-lg-3 col-xl-3">
                <nav class="nav flex-lg-column nav-pills mb-4">
                    <a class="nav-link active" href="<?= (SITE_URL); ?>/admin/admin">Admin</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/users">Users</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/organizations">Organizations</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/logs">Logs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-6 col-xl-6">
                <div class="card border-primary mx-auto" style="max-width: 900px">
                    <div class="card-header bg-primary text-white text-center">
                        User Information
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h5 class="card-title mb-0">User Name</h5>
                                <p class="card-text"><?= Session::Get("username") ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="card-title mb-0">Email</h5>
                                <p class="card-text"><?= Session::Get("email") ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="card-title mb-0">Role</h5>
                                <p class="card-text"><?= Session::Get("role") ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="card-title mb-0">Created</h5>
                                <p class="card-text"><?= Session::Get("createdAt") ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php Util::Footer(); ?>