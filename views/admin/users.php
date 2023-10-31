<?php
require_once './kuro/require.php';

require_once './kuro/controllers/auth.php';

Util::IsAdmin($auth);

require_once './kuro/controllers/license.php';

if(Session::Get("isSuperAdmin"))
{
    $data = $auth->getPaginationData();
    $users = $data['users'];
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
        <div class="modal fade" id="editmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Edit User Data </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="userId" id="userId">
                            <?php if(Session::Get("isOrgAdmin")): ?>
                            <input type="hidden" name="mOrgId" id="mOrgId">
                            <?php endif; ?>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Username" name="mUsername"
                                    id="mUsername" minlength="3" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="mPassword" id="generatedPassword1"
                                    readonly required>
                                <button class="btn btn-secondary generatePasswordBtn"
                                    data-target="generatedPassword1">Generate Password</button>

                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="email" name="mEmail" id="mEmail"
                                    required>
                            </div>
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="mStatus" id="mStatus">
                                    <label class="form-check-label" for="mStatus">Ban</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <select class="form-select" aria-label="Default select example" name="mRoleId">
                                    <option selected>Role Selection</option>
                                    <?php if(Session::Get("isSuperAdmin")): ?>
                                    <option value="1">Super admin</option>
                                    <?php endif; ?>
                                    <option value="2">Organization Admin</option>
                                    <option value="3">User</option>
                                </select>
                            </div>
                            <?php if(Session::Get("isSuperAdmin")): ?>
                            <div class="form-group">
                                <select class="form-select" aria-label="Default select example" name="mOrgId">
                                    <option selected>Organization Selection</option>
                                    <?php foreach ($auth->GetAllOrgs() as $row) : ?>
                                    <option value="<?= $row->orgId; ?>"><?= $row->orgName; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="modal-footer">
                                <button class="btn btn-primary btn-block" name="edit" id="edit" type="edit"
                                    value="edit">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Delete User Data </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="uid" id="deleteid">

                            <h4>Are you sure about this?</h4>
                            <div class="modal-footer">
                                <button class="btn btn-primary btn-block" name="delete" id="delete" type="delete"
                                    value="delete">
                                    Yes
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="assignLicenseModal" tabindex="-1" aria-labelledby="assignLicenseLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignLicenseLabel">Assign License to User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="assignLicenseUserId" id="assignLicenseUserId">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="licenseKey" name="licenseKey"
                                    id="licenseKey" minlength="3" required>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary btn-block" name="assignLicense" id="assignLicense"
                                    type="assignLicense" value="assignLicense">
                                    Assign
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editLicenseModal" tabindex="-1" aria-labelledby="editLicenseModal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLicenseModal">Edit User License</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="editLicenseUserId" id="editLicenseUserId">
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="licenseStatus"
                                        id="licenseStatus">
                                    <label class="form-check-label" for="licenseStatus">Active</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="licenseStartDate">Start Date</label>
                                <input type="date" class="form-control" name="licenseStartDate" id="licenseStartDate"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="licenseEndDate">End Date</label>
                                <input type="date" class="form-control" name="licenseEndDate" id="licenseEndDate"
                                    required>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary btn-block" name="editLicense" id="editLicense"
                                    type="editLicense" value="editLicense">
                                    Edit
                                </button>
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
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/license">License</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/userlogs">UserLogs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-6 col-xl-6">
                <div class="card border-primary mx-auto" style="max-width: 900px">
                    <div class="card-header bg-primary text-white text-center">
                        Create User
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Username" name="username"
                                    minlength="3" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="generatedPassword" id="generatedPassword2"
                                    readonly required>
                                <button class="btn btn-secondary generatePasswordBtn"
                                    data-target="generatedPassword2">Generate Password</button>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" aria-label="Default select example" name="roleId">
                                    <option value="3" selected>Role Selection</option>
                                    <?php if(Session::Get("isSuperAdmin")): ?>
                                    <option value="1">Super admin</option>
                                    <?php endif; ?>
                                    <option value="2">Organization Admin</option>
                                    <option value="3">User</option>
                                </select>
                            </div>
                            <?php if(Session::Get("isSuperAdmin")): ?>
                            <div class="mb-3">
                                <select class="form-select" aria-label="Default select example" name="orgId">
                                    <option value="2" selected>Organization Selection</option>
                                    <?php foreach ($auth->GetAllOrgs() as $row) : ?>
                                    <option value="<?= $row->orgId; ?>"><?= $row->orgName; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="orgId" value="<?= Session::Get("orgId") ?>">
                            <?php endif; ?>
                            <button class="btn btn-primary btn-block center" name="registerSuperAdmin" id="submit"
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
                    <h4 class="card-title text-center">Users</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Organization</th>
                                <th scope="col">Status</th>
                                <th scope="col">License Status</th>
                                <th scope="col">License Start Date</th>
                                <th scope="col">License Expiry Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                            <tr>
                                <td scope="row"><?= $user->userId; ?></td>
                                <td><?= Util::Print($user->username); ?></td>
                                <td><?= Util::Print($user->email); ?></td>
                                <td><?= Util::Print(Util::GetRoleName($user->roleId)); ?></td>
                                <td><?= Util::Print($auth->GetOrgName($user->orgId)); ?></td>
                                <?php if($user->status == 1): ?>
                                <td>Banned</td>
                                <?php else: ?>
                                <td>Active</td>
                                <?php endif; ?>
                                <?php if(isset($user->licenseKey)): ?>
                                <td><?= $user->licenseStatus == 1 ? 'Active' : 'Inactive'; ?></td>
                                <?php else: ?>
                                <td>N/A</td>
                                <?php endif; ?>
                                <td><?= isset($user->licenseKey) ? Util::ConvertDate($user->startDate) : 'N/A'; ?></td>
                                <td><?= isset($user->licenseKey) ? Util::ConvertDate($user->expiryDate) : 'N/A'; ?></td>
                                <td>
                                    <button class="btn btn-primary editbtn" data-id="<?= $user->userId; ?>"
                                        data-toggle="modal">Edit</button>
                                    <button class="btn btn-danger deletebtn" data-id="<?= $user->userId; ?>"
                                        data-toggle="modal">Delete</button>
                                    <?php if(!isset($user->licenseKey)): ?>
                                    <button class="btn btn-info assignLicensebtn" data-id="<?= $user->userId; ?>"
                                        data-toggle="modal">Assign License</button>
                                    <?php else: ?>
                                    <button class="btn btn-warning editLicensebtn" data-id="<?= $user->userId; ?>"
                                        data-toggle="modal">Edit License</button>
                                    <?php endif; ?>
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

    $('.editbtn').on('click', function() {

        $('#editmodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function() {
            return $(this).text();
        }).get();

        console.log(data);

        $('#userId').val(data[0]);
        $('#mUsername').val(data[1]);
        $('#mEmail').val(data[2]);
        $('#mRoleId').val(data[3]);
        <?php if(Session::Get("isSuperAdmin")): ?>
        $('#mOrgId').val(data[4]);
        <?php endif; ?>
        if (data[5] == 1) {
            $('#mStatus').prop('checked', true);
        } else {
            $('#mStatus').prop('checked', false);
        }
    });
});

$(document).ready(function() {

    $('.deletebtn').on('click', function() {

        $('#deletemodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function() {
            return $(this).text();
        }).get();

        console.log(data);

        $('#deleteid').val(data[0]);
    });
});
// For Assign License
$(document).ready(function() {
    $('.assignLicensebtn').on('click', function() {
        $('#assignLicenseModal').modal('show');
        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function() {
            return $(this).text();
        }).get();

        console.log(data);

        $('#assignLicenseUserId').val(data[0]);
    });
});

// For Edit License
$(document).ready(function() {
    $('.editLicensebtn').on('click', function() {
        $('#editLicenseModal').modal('show');
        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function() {
            return $(this).text();
        }).get();

        console.log(data);

        $('#editLicenseUserId').val(data[0]);
        if (data[6] == 'Active') {
            $('#licenseStatus').prop('checked', true);
        } else {
            $('#licenseStatus').prop('checked', false);
        }
        $('#licenseStartDate').val(data[7]);
        $('#licenseEndDate').val(data[8]);
    });
});
</script>