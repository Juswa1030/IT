<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('assets/config/config.php');


// Check for notification message
if (isset($_SESSION['notification'])) {
  echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['notification']) . "</div>";

  // Remove the notification from the session
  unset($_SESSION['notification']);
}

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
  // If no session, redirect to login page
  header("Location: ../login.php");
  exit;
}

try {
  // Create a new PDO instance
  $pdo = new PDO("mysql:host=$server_name;dbname=$db_name;charset=utf8", $user_name, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch user details based on session id, including additional fields
  $stmt = $pdo->prepare("SELECT name, location, status,img FROM tb_it WHERE id = :id");
  $stmt->bindParam(':id', $_SESSION['id']);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if user was found
  if ($user) {
      $username = htmlspecialchars($user['name']); // Protect against XSS
      $location = htmlspecialchars($user['location']); // Ensure 'location' is also fetched
      $status = htmlspecialchars($user['status']); // Ensure 'status' is also fetched
      $profile =htmlspecialchars($user['img']); 
  } else {
      $username = "Unknown User"; // Fallback
      $location = "Unknown Location"; // Fallback
      $status = "Unknown Status"; // Fallback
  }
} catch (PDOException $e) {
  echo "<p class='text-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
  exit;
}

?>

<head>
<?php
  include ('assets/includes/head.php');
?>

</head>

<body>
<?php if (isset($_SESSION['notification'])): ?>
    <div class="sweetalert-modal" id="sweetalert-modal">
        <div class="sweetalert-content">
            <div class="sweetalert-body">
                <div class="checkmark-wrapper">
                    <svg class="checkmark" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14 27l7 7 17-17"/>
                    </svg>
                </div>
                <p>
                    <?php
                    echo $_SESSION['notification'];
                    unset($_SESSION['notification']);
                    ?>
                </p>
            </div>
            <div class="sweetalert-footer">
                <button class="confirm-button" onclick="closeModal();">OK</button>
            </div>
        </div>
    </div>
    <script>
        // Function to close the modal
        function closeModal() {
            document.getElementById('sweetalert-modal').style.display = 'none';
        }


        setTimeout(closeModal, 3000);
    </script>
<?php endif; ?>


 <?php
  include ('assets/includes/navbar.php');
  include ('assets/includes/leftside.php');

 ?>
<main id="main" class="main">



<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
      <li class="breadcrumb-item active">Ongoing</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
      <div class="row">

            <div class="col-12">
              <div class="card recent-sales overflow-auto">


                <div class="card-body">
              
                  <h5 class="card-title">Ongoing <span>| Troubleshoot</span></h5>
                  
                  
    
                  <?php
// Initialize array
$ongoing = [];
try {

    // Prepare and execute the query to get records from tb_it
    $stmt = $pdo->prepare("SELECT 
            tbtrouble.*, 
            tb_it.name AS personnel_name,
            tb_it.id AS personnel_id
        FROM 
            tbtrouble 
        LEFT JOIN 
            tb_it 
        ON 
            tbtrouble.person = tb_it.id 
        WHERE 
            tbtrouble.status='ongoing'; AND
            
            
    ");
    $stmt->execute();
    $ongoing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Store search results in session (optional)
    $_SESSION['search_results'] = $ongoing;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<hr>
<table class="table table-borderless datatable">
    <thead>
        <tr>
            <th scope="col"><input type="checkbox" /></th>
            <th scope="col">Name</th>
            <th scope="col">Status</th>
            <th scope="col">Person</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($ongoing)) : ?>
        <?php $counts = '1'; ?> 
        <?php foreach ($ongoing as $item) : ?> 
            <tr>
                <th scope="row"><input type="checkbox" /> <?php echo $counts ++; ?></th>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>
                    <?php echo htmlspecialchars($item['status']); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($item['personnel_name']); ?>
                </td>
                <td>
               <!-- Call to action buttons -->
               <ul class="list-inline m-0">
                    <!-- Edit Button -->
                    <li class="list-inline-item">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#endorseModal">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    <?php
                    try {
                      // Fetch data from tb_ward
                      $stmt = $pdo->prepare("SELECT id, name FROM tb_it WHERE status='active' and role='user' and stat='available' and location='$location'");
                      $stmt->execute();
                      $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  } catch (PDOException $e) {
                      echo "<p class='text-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
                  }
                  ?>


                        <div class="modal fade" id="endorseModal" tabindex="-1" role="dialog" aria-labelledby="endorseModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="endorseModalLabel">Endorse</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                    <form id="endorseForm" method="post" action="assets/config/ongoingupdate.php" style="display:inline;">
                        <label for="tech_id">Technical</label>
                        <select class="form-control" id="tech_id" name="tech_id" required>
                            <option value="">Select a Technician</option> 
                            <?php if (!empty($user)): ?>
                                <?php foreach ($user as $techs): ?>
                                    <option value="<?php echo htmlspecialchars($techs['id']); ?>">
                                        <?php echo htmlspecialchars($techs['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No technicians available</option>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="endorse" class="btn btn-danger" form="endorseForm">Endorse</button>
                </div>
                        </div>
                    </div>
                </div>
 
                                    
                    
                </ul>
            </td>

            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="5">No records found</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'active':
            return 'bg-success'; 
        case 'deactive':
            return 'bg-warning'; 
        default:
            return ''; 
    }
}
?>





                </div>

              </div>
            </div>


          </div>
        </div><!-- End Left side columns -->


      </div>
    </section>
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 5"></div>
  </main><!-- End #main -->

  
  <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 5"></div>


<script src="assets/js/jquery-3.6.0.min.js"></script>
<script>
function fetchTrouble() {
    $.ajax({
        url: 'assets/config/fetch_data_user.php',
        type: 'GET',
        dataType: 'json', 
        success: function (response) {
            console.log('Raw response:', response);
            try {
                // response is already a JSON object, no need to parse it
                checkForNewTrouble(response);
            } catch (e) {
                console.error('Error processing response:', e);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching new troubles:', error);
        }
    });
}

function checkForNewTrouble(newTrouble) {
    if (newTrouble.length > 0) {
        newTrouble.forEach(function (trouble) {
            showToast(trouble.name, trouble.location);
        });
    }
}

function showToast(name, location) {
    var toastHTML = `
        <div class="toast mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header d-flex">
                <strong class="me-auto pendingtitle">Status Update</strong>
                
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Please Accept Troubleshoot from "${location}" .
            </div>
            <a class="btn btn-secondary btn-sm mt-2" href="index.php">View Now</a>
        </div>
    `;

    var toastContainer = document.getElementById('toastContainer');
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    var toastElement = toastContainer.querySelector('.toast:last-child');
    var toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Remove toast after it hides
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Set interval to fetch data every 10 seconds
setInterval(function () {
    fetchTrouble(); // Check for new troubles
}, 10000); // 10 seconds

$(document).on('click', '.toast-refresh-btn', function(){
    
})
</script>


  

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
 <?php
  include ('assets/includes/footer.php');
 ?>

</body>

</html>