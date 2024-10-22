<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="images/logo.png" rel="icon">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-image: url('images/bg.jpg'); background-repeat: no-repeat;
     background-size: cover;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header text-center">
                        <img src="images/logo.png" class="img-fluid" alt="Logo" style="max-width: 200px;">
                        <h3 class="my-3">Login</h3>
                    </div>
                    <div class="card-body">
                        <form action="assets/config/login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                            <div class="text-center">
                                <p>Don't have an account? <a href="registration.php">Sign Up Here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
