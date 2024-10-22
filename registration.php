<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="images/logo.png" rel="icon">
    <title>Registration</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-image: url('images/bg.jpg'); background-repeat: no-repeat;
     background-size: cover;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header text-center">
                        <img src="images/logo.png" class="img-fluid" alt="Logo" style="max-width: 200px;">
                        <h3 class="my-3">Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="assets/config/register.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label for="pass" class="form-label">Password</label>
                                <input type="password" class="form-control" id="pass" name="pass" placeholder="Enter your password" required>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <select id="location" name="location" class="form-select" required>
                                    <option value="" disabled selected>Select your location</option>
                                    <?php
                                    // Include the configuration file
                                    include('assets/config/config.php');

                                    try {
                                        // Create a new PDO instance
                                        $pdo = new PDO("mysql:host=$server_name;dbname=$db_name;charset=utf8", $user_name, $password);
                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                        // Fetch locations from the database
                                        $stmt = $pdo->prepare("SELECT location_name FROM tblocation ORDER BY location_name");
                                        $stmt->execute();
                                        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        // Populate the dropdown options
                                        foreach ($locations as $location) {
                                            echo "<option value=\"" . htmlspecialchars($location['location_name']) . "\">" . htmlspecialchars($location['location_name']) . "</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option value=\"\" disabled>Error loading locations</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="" disabled selected>Select your status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>


                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                            <div class="text-center">
                                <p>Already have an account? <a href="login.php">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (Optional, but useful for certain Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
