<?php
include('db.php');
include('includes/header.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $class = (int)$_POST['class'];
    $student_number = trim($_POST['student_number']);
    $email = trim($_POST['email']);
    $club_id = (int)$_POST['club_id'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($class < 1 || $class > 12) $errors[] = "Class must be between 1 and 12.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    $exists = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($exists) > 0) $errors[] = "Email already registered.";

    if (empty($errors)) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, class, student_number, email, password, role, club_id) 
                VALUES ('$name', $class, '$student_number', '$email', '$pass_hash', 'student', $club_id)";
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center'>Registration successful. <a href='login.php'>Login here</a></div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to register. Try again.</div>";
        }
    }
}
?>

<div class="container my-5" style="max-width: 600px;">
  <h2 class="mb-4 text-center">Student Registration</h2>

  <?php if (!empty($errors)) {
    echo "<div class='alert alert-danger'><ul>";
    foreach ($errors as $e) echo "<li>$e</li>";
    echo "</ul></div>";
  } ?>

  <form method="POST">
    <div class="mb-3"><label>Full Name</label><input name="name" type="text" class="form-control" required></div>
    <div class="mb-3"><label>Class (1–12)</label><input name="class" type="number" class="form-control" min="1" max="12" required></div>
    <div class="mb-3"><label>Student Number</label><input name="student_number" type="text" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
    <div class="mb-3">
      <label>Club Membership</label>
      <select name="club_id" class="form-control" required>
        <option value="">Select a club</option>
        <?php
        $clubs = mysqli_query($conn, "SELECT id, name FROM clubs");
        while ($club = mysqli_fetch_assoc($clubs)) {
          echo "<option value='{$club['id']}'>" . htmlspecialchars($club['name']) . "</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
    <div class="mb-3"><label>Confirm Password</label><input name="confirm" type="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Register</button>
  </form>
</div>

<?php include('includes/footer.php'); ?>
