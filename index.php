<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('assets/config/config.php');

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
  // If no session, redirect to login page
  header("Location: login.php");
  exit;
}

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
  // Redirect to the user dashboard if not an admin
  header("Location: dashboard/index.php"); // Change this to your actual user dashboard path
  exit();
}

try {
  // Create a new PDO instance
  $pdo = new PDO("mysql:host=$server_name;dbname=$db_name;charset=utf8", $user_name, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch user details based on session id, including additional fields
  $stmt = $pdo->prepare("SELECT id, name, location, status, img FROM tb_it WHERE id = :id");
  $stmt->bindParam(':id', $_SESSION['id']);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if user was found
  if ($user) {
      $username = htmlspecialchars($user['name']); // Protect against XSS
      $location = htmlspecialchars($user['location']); // Ensure 'location' is also fetched
      $status = htmlspecialchars($user['status']); // Ensure 'status' is also fetched
      $userid = htmlspecialchars($user['id']);
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
  include('assets/includes/head.php');
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
  include('assets/includes/navbar.php');
  include('assets/includes/leftside.php');
  include('assets/includes/main.php');
  include('assets/includes/footer.php');
 ?>
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



</body>

</html>