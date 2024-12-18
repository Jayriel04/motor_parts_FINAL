<!-- for login account -->
<?php
require '../motor-parts/backend/connection.php';

// Retrieve user input
if (isset($_POST['admin'])) {
    $user_name = $_POST["login_username"];
    $user_password = $_POST["login_password"];

    // Query the database
    $sql = "SELECT * FROM users WHERE username = '$user_name' AND password = '$user_password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Update last_login field with current date and time
        $currentDateTime = date('Y-m-d H:i:s');
        $updateSql = "UPDATE users SET last_login = '$currentDateTime' WHERE username = '$user_name'";
        $conn->query($updateSql);

        // Redirect to the dashboard page
        echo "<script>document.getElementById('failed_login').innerText = ''</script>";
        header('Location: dashboard.php');
        exit;
    } else {
        echo "<script>document.getElementById('failed_login').innerText = 'Invalid username or password!'</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="src/css/login.css">
</head>

<body>
  <form action="" method="post" onsubmit="test()">
    <div class="w-50 px-5 m-auto box text-center mt-5">
      <h1 style="color: white;" class="mt-5">LOGIN</h1>
      <div class="mb-3">
        <label for="login_username" class="form-label">Username</label>
        <input type="text" class="form-control form-control-lg login-input" value="" name="login_username" id="login_username" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <label for="login_password" class="form-label">Password</label>
        <input type="password" class="form-control form-control-lg login-input" name="login_password" id="login_password" placeholder="Password" required>
      </div>
      <div class="invalid-feedback" id="failed_login" style="display: block;">        
      </div>
      <button type="submit" class="btn btn-outline-light m-5 btn-lg" name="admin">Login</button>
      <div style="color: white;" class="text-md-center mb-3">Don't have an account?
        <button style="color: white;" type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
          Sign Up
        </button>
      </div>
    </div>
  </form>

  <!-- Modal -->

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return validateForm()">
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Create a new account</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label for="firstname" class="col-sm-2 col-form-label">First name:</label>
              <div class="col-sm-10 mb-3">
                <input type="text" class="form-control" id="firstname" name="firstname">
              </div>
              <label for="lastname" class="col-sm-2 col-form-label">Last name:</label>
              <div class="col-sm-10 mb-3">
                <input type="text" class="form-control" id="lastname" name="lastname">
              </div>
              <label for="user_name" class="col-sm-2 col-form-label">Username:</label>
              <div class="col-sm-10 mb-3">
                <input type="text" class="form-control" id="user_name" name="user_name">
              </div>
              <label for="user_password" class="col-sm-2 col-form-label">Password:</label>
              <div class="col-sm-10 mb-3">
                <input type="password" class="form-control" id="user_password" name="user_password">
              </div>
              <label for="confirmpassword" class="col-sm-2 col-form-label">Confirm Password:</label>
              <div class="col-sm-10 mb-3">
                <input type="password" class="form-control" id="confirmpassword">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="add">Sign Up</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- saving the data of user to database -->
  <?php
if (isset($_POST['add'])) {
  // Retrieve the form data
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $user_name = $_POST['user_name'];
  $user_password = $_POST['user_password'];

  // Query for form
  $sql = "INSERT INTO users (first_name, user_id, last_name, username, password)
          VALUES ('$firstname',null , '$lastname', '$user_name', '$user_password')";

  // Redirect the user to a success page or display a success message
  if ($conn->query($sql) === TRUE) {
    // Redirect to the dashboard page
    header('Location: ../../motor-parts/dashboard.php');
    exit;
  } else {
    echo "ERROR: " . $sql . "<br>" . $conn->error;
  }
} 
  ?>

  <script>
    function test(){
      console.log(document.getElementById("login_username"))
    }
    function validateForm() {
      var firstname = document.getElementById("firstname").value;
      var lastname = document.getElementById("lastname").value;
      var username = document.getElementById("user_name").value;
      var password = document.getElementById("user_password").value;
      var confirmpassword = document.getElementById("confirmpassword").value;

      if (firstname === "" || lastname === "" || username === "" || password === "" || confirmpassword === "") {
        alert("Please fill in all fields.");
        return false;
      }
      console.log(password, confirmpassword)
      if (password !== confirmpassword) {
        alert("Passwords do not match.");
        return false;
      }

      return true;
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>

</html>