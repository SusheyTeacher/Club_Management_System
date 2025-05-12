<?php
session_start();
include('db.php');
include('includes/header.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $id = trim($_POST['id']);
    $password = $_POST['password'];

    if ($role === 'admin') {
        if ($id === 'admin@school.com' && $password === 'admin123') {
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = 'Administrator';
            header("Location: dashboard/admin.php");
            exit;
        } else {
            $error = "Invalid admin credentials.";
        }
    }

    elseif ($role === 'mentor') {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE employment_id='$id' AND role='mentor'");
        if ($user = mysqli_fetch_assoc($query)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['role'] = 'mentor';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("Location: dashboard/mentor.php");
                exit;
            }
        }
        $error = "Invalid mentor credentials.";
    }

    elseif ($role === 'student') {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$id' AND role='student'");
        if ($user = mysqli_fetch_assoc($query)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['role'] = 'student';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("Location: dashboard/student.php");
                exit;
            }
        }
        $error = "Invalid student credentials.";
    }
}
?>

<div class="container my-5" style="max-width: 500px;">
  <h2 class="mb-4 text-center">Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="mentor">Mentor</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Email / Employment ID / Admin ID</label>
      <input type="text" name="id" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>


<?php include('includes/footer.php'); ?>
