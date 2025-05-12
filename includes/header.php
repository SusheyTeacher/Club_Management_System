<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Club Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }

    footer {
      background-color: #212529;
      color: white;
    }

    /* Navbar Styles */
    .navbar {
      background-color: #537D5D;
      padding-top: 1rem;
      padding-bottom: 1rem;
      font-size: 1.15rem;
      box-shadow: 0 9px 10px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.6rem;
      font-weight: bold;
      color: #000 !important;
    }

    .navbar-nav .nav-link {
      margin-right: 20px;
      padding: 10px 18px;
      font-weight: 700;
      color: #000 !important;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      background-color: rgba(241, 230, 230, 0.93);
      color: #000 !important;
    }

    .dropdown-menu {
      font-size: 1rem;
    }

    .nav-container {
      padding-left: 45px;
      padding-right: 45px;
    }

    @media (max-width: 768px) {
      .navbar-brand { font-size: 1.4rem; }
      .nav-container { padding-left: 15px; padding-right: 15px; }
    }
    .hero-section {
  background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
              url('upload/hero1.png') no-repeat center center/cover;
  height: 90vh;
  padding: 0 20px;
  color: #e0f7fa; /* soft light-blue font color */
}

.hero-heading {
  text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
  color:rgb(215, 224, 230);
}

.hero-subtext {
  text-shadow: 0px 1px 4px rgba(0,0,0,0.4);
  color: #f0f8ff;
}

  </style>
</head>
<body>
<?php
if (!isset($logoPath)) {
  $logoPath = (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? '../upload/logo.png' : 'upload/logo.png';
}
?>
<?php
if (!isset($navPrefix)) {
  $navPrefix = (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? '../' : '';
}
?>


<nav class="navbar navbar-expand-lg navbar-light sticky-top">
  <div class="container-fluid nav-container">
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <div class="navbar-brand d-flex align-items-center">
    <img src="<?php echo $logoPath; ?>" alt="Logo" style="height: 60px; filter: drop-shadow(1px 1px 4px rgba(0,0,0,0.5));">
    <span style="font-weight: bold; font-size: 1.4rem; color: #000;" class="ms-2">ClubManSys</span>
  </div>
<?php else: ?>
  <a class="navbar-brand d-flex align-items-center" href="<?php echo $navPrefix; ?>index.php">
    <img src="<?php echo $logoPath; ?>" alt="Logo" style="height: 60px; filter: drop-shadow(1px 1px 4px rgba(0,0,0,0.5));">
    <span style="font-weight: bold; font-size: 1.4rem; color: #000;" class="ms-2">ClubManSys</span>
  </a>
<?php endif; ?>




    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
 <ul class="navbar-nav me-auto mb-2 mb-lg-0">
  <?php if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')): ?>
    <!-- Show only for non-admin users -->
    <li class="nav-item"><a class="nav-link" href="<?php echo $navPrefix; ?>clubs.php">Clubs</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo $navPrefix; ?>index.php#events">Events</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo $navPrefix; ?>index.php#contact">Contact Us</a></li>
  <?php endif; ?>
</ul>



<ul class="navbar-nav">
  <?php if (isset($_SESSION['role'])): ?>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Hi! <?php echo htmlspecialchars($_SESSION['name']); ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <?php
          if ($_SESSION['role'] === 'admin') {
            echo "<li><a class='dropdown-item' href='" . (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false ? 'admin.php' : 'dashboard/admin.php') . "'>Dashboard</a></li>";
          } elseif ($_SESSION['role'] === 'mentor') {
            echo "<li><a class='dropdown-item' href='" . (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false ? 'mentor.php' : 'dashboard/mentor.php') . "'>Dashboard</a></li>";
          } elseif ($_SESSION['role'] === 'student') {
            echo "<li><a class='dropdown-item' href='" . (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false ? 'student.php' : 'dashboard/student.php') . "'>Dashboard</a></li>";
          }
        ?>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? '../logout.php' : 'logout.php'; ?>">
            Logout
          </a>
        </li>
      </ul>
    </li>
  <?php else: ?>
    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-bs-toggle="dropdown">
        Register
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="register-student.php">Register as Student</a></li>
        <li><a class="dropdown-item" href="register-mentor.php">Register as Mentor</a></li>
      </ul>
    </li>
  <?php endif; ?>
</ul>

    </div>
  </div>
</nav>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

