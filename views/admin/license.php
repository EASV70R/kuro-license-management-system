<?php
require_once './kuro/require.php';

require_once './kuro/controllers/auth.php';

Util::IsAdmin($auth);

require_once './kuro/controllers/license.php';

if(Session::Get("isSuperAdmin"))
{
    $data = $licenseController->GetPaginationData();
    $licenses = $data['licenses'];
    $totalRecords = $data['totalRecords'];
    $limit = $data['limit'];
    $totalPages = ceil($totalRecords / $limit);
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
}else{
    $data = $auth->GetOrgsPaginationData((int)Session::Get("orgId"));
    $users = $data['users'];
    $totalRecords = $data['totalRecords'];
    $limit = $data['limit'];
    $totalPages = ceil($totalRecords / $limit);
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
}

Util::Header();
?>

<main class="container-fluid mt-5">
    <div class="testcontainer">
        <div class="modal fade" id="deletelicensemodal" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel"> Delete License </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="licenseId" id="licenseId">
                            <h4>Are you sure you want to delete this license?</h4>
                            <div class="modal-footer">
                                <button class="btn btn-danger btn-block" name="deleteLicense" type="submit"
                                    value="delete">Delete</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                    <a class="nav-link active" href="<?= (SITE_URL); ?>/admin/users">Users</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/organizations">Organizations</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/userlogs">UserLogs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-6 col-xl-6">
                <div class="card border-primary mx-auto" style="max-width: 900px">
                    <div class="card-header bg-primary text-white text-center">
                        Create License
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <button class="btn btn-primary btn-block center" name="createLicense" id="submit"
                                type="submit" value="submit">
                                Create
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-xl-3"></div>
                <div class="col-lg-6 col-xl-6">
                    <hr>
                    <h4 class="card-title text-center">Licenses</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">License Key</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licenses as $license) : ?>
                            <tr>
                                <td scope="row"><?= $license->licenseId; ?></td>
                                <td><?= Util::Print($license->licenseKey); ?></td>
                                <td>
                                    <?= $license->userId ? 'Activated by User ID: '.$license->userId : 'Not Activated'; ?>
                                </td>

                                <td>
                                    <button class="btn btn-danger deletebtn" data-id="<?= $license->licenseId; ?>"
                                        data-toggle="modal" data-bs-target="#deletelicensemodal">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination justify-content-center">

                            <!-- Previous Button -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page <= 1 ? '#' : '?page=' . ($page - 1) ?>"
                                    tabindex="-1" aria-disabled="<?= $page <= 1 ? 'true' : 'false' ?>">Previous</a>
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
    </div>
</main>

<?php Util::Footer(); ?>

<script>
$(document).ready(function() {
    $('.deletebtn').on('click', function() {
        $('#deletelicensemodal').modal('show');
        $tr = $(this).closest('tr');
        var data = $tr.children("td").map(function() {
            return $(this).text();
        }).get();
        console.log(data);
        $('#licenseId').val(data[0]);
    });
});
</script>