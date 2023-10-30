<?php
require_once './kuro/require.php';

require_once './kuro/controllers/auth.php';

Util::IsAdmin($auth);

require_once './kuro/controllers/logs.php';

$log = new Logs();

$data = $log->GetPaginationData((int)Session::Get("roleId"), (int)Session::Get("orgId"));
$users = $data['logs'];
$totalRecords = $data['totalRecords'];
$limit = $data['limit'];
$totalPages = ceil($totalRecords / $limit);
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

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
                    <a class="nav-link active" href="<?= (SITE_URL); ?>/admin/userlogs">UserLogs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-9 col-xl-6">
                <h4 class="card-title text-center">User Logs</h4>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Status</th>
                            <th scope="col">IpAddress</th>
                            <th scope="col">ApiKey</th>
                            <th scope="col">Timestamp</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user->username ?></td>
                            <td><?= ($user->status == 0) ? 'Failed to login' : 'Success'; ?></td>
                            <td><?= $user->ipAddress ?></td>
                            <td><?= $user->apiKeyUsed ?></td>
                            <td><?= $user->timestamp ?></td>
                            <td>
                                <button class="btn btn-primary editbtn" data-id="1" data-toggle="modal">Edit</button>
                                <button class="btn btn-danger deletebtn" data-id="2" data-toggle="modal">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center">

                        <!-- Previous Button -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $page <= 1 ? '#' : '?page=' . ($page - 1) ?>" tabindex="-1"
                                aria-disabled="<?= $page <= 1 ? 'true' : 'false' ?>">Previous</a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= $page >= $totalPages ? '#' : '?page=' . ($page + 1) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</main>
<?php Util::Footer(); ?>