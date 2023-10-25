<?php
require_once './kuro/require.php';

require_once './kuro/controllers/auth.php';

Util::IsAdmin($auth);

require_once './kuro/controllers/org.php';

Util::Header();
?>

<main class="container-fluid mt-5">
    <div class="testcontainer">
        <div class="modal fade" id="editmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Edit Organzation Data </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="orgId" id="orgId">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Name" name="mName" id="mName"
                                    minlength="3" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mRegenApi" id="mRegenApi">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Regenarate API key
                                </label>
                            </div>
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
                        <h5 class="modal-title" id="exampleModalLabel"> Delete Organization </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="orgId" id="deleteid">

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
                    <a class="nav-link active" href="<?= (SITE_URL); ?>/admin/organizations">Organizations</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/admin/logs">Logs</a>
                    <a class="nav-link" href="<?= (SITE_URL); ?>/logout">Logout</a>
                </nav>
            </aside>
            <div class="col-lg-6 col-xl-6">
                <div class="card border-primary mx-auto" style="max-width: 900px">
                    <div class="card-header bg-primary text-white text-center">
                        Create Organization
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Organization Name" name="orgName"
                                    minlength="3" required>
                            </div>
                            <button class="btn btn-primary btn-block center" name="add" id="submit" type="submit"
                                value="submit">
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
                    <h4 class="card-title text-center">Organizations</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">ApiKey</th>
                                <th scope="col">Creation Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($org->GetAllOrgs() as $orgs) : ?>
                            <tr>
                                <td scope="row"><?= $orgs->orgId; ?></td>
                                <td><?= Util::Print($orgs->orgName); ?></td>
                                <td><?= Util::Print($orgs->apiKey); ?></td>
                                <td><?= Util::Print($orgs->createdAt); ?></td>
                                <td>
                                    <button class="btn btn-primary editbtn" data-id="<?= $orgs->orgId; ?>"
                                        data-toggle="modal">Edit</button>
                                    <button class="btn btn-danger deletebtn" data-id="<?= $orgs->orgId; ?>"
                                        data-toggle="modal">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

        $('#orgId').val(data[0]);
        $('#mName').val(data[1]);
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
</script>