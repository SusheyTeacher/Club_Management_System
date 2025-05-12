<?php
include('db.php');
include('includes/header.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $employment_id = trim($_POST['employment_id']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    $exists = mysqli_query($conn, "SELECT id FROM users WHERE employment_id='$employment_id'");
    if (mysqli_num_rows($exists) > 0) $errors[] = "Employment ID already registered.";

    if (empty($errors)) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, employment_id, password, role) 
                VALUES ('$name', '$employment_id', '$pass_hash', 'mentor')";
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center'>Mentor registered. <a href='login.php'>Login here</a></div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to register. Try again.</div>";
        }
    }
}
?>

<div class="container my-5" style="max-width: 600px;">
  <h2 class="mb-4 text-center">Mentor Registration</h2>

  <?php if (!empty($errors)) {
    echo "<div class='alert alert-danger'><ul>";
    foreach ($errors as $e) echo "<li>$e</li>";
    echo "</ul></div>";
  } ?>

  <form method="POST">
    <div class="mb-3"><label>Full Name</label><input name="name" type="text" class="form-control" required></div>
    <div class="mb-3"><label>Employment ID</label><input name="employment_id" type="text" class="form-control" required></div>
    <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
    <div class="mb-3"><label>Confirm Password</label><input name="confirm" type="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Register</button>
  </form>
</div>

<?php include('includes/footer.php'); ?>
