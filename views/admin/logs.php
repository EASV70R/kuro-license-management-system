<?php
require_once './kuro/require.php';
Util::Header();
?>
<main class="container-fluid mt-5">
    <div class="testcontainer">
        <div class="row">
            <div class="col-12 mt-3 mb-2">
                <?php if (isset($response)) : ?>
                <div class="alert alert-primary" role="alert">
                    <?= $response; ?>
                </div>
                <?php endif; ?>
            </div>
            <aside class="col-lg-3 col-xl-3">
                <nav class="nav flex-lg-column nav-pills mb-4">
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/admin">Admin</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/users">Users</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/organizations">Organizations</a>
                    <a class="nav-link active" href="<?= (SITE_URL); ?>/admin/logs">Logs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-9 col-xl-6">
                <h4 class="card-title text-center">User Logs</h4>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Log Message</th>
                            <th scope="col">Timestamp</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= 'test' ?></td>
                            <td><?= 'test' ?></td>
                            <td><?= 'test' ?></td>
                            <td>
                                <button class="btn btn-primary editbtn" data-id="1" data-toggle="modal">Edit</button>
                                <button class="btn btn-danger deletebtn" data-id="2" data-toggle="modal">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td><?= 'test' ?></td>
                            <td><?= 'test' ?></td>
                            <td><?= 'test' ?></td>
                            <td>
                                <button class="btn btn-primary editbtn" data-id="1" data-toggle="modal">Edit</button>
                                <button class="btn btn-danger deletebtn" data-id="2" data-toggle="modal">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php Util::Footer(); ?>