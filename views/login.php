<?php
Util::Header();
Util::Navbar();
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Login</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button class="btn btn-primary btn-block" name="login" id="submit" type="submit" value="submit">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php Util::Footer(); ?>