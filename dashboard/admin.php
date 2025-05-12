 <?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
include('../db.php');
include('../includes/header.php');

$toast = "";

// Delete user
if (isset($_GET['delete_user'])) {
  $uid = (int)$_GET['delete_user'];
  mysqli_query($conn, "DELETE FROM users WHERE id = $uid");
  $toast = createToast("User deleted successfully.", "bg-warning");
}

// Delete club
if (isset($_GET['delete_club'])) {
  $cid = (int)$_GET['delete_club'];
  mysqli_query($conn, "DELETE FROM clubs WHERE id = $cid");
  $toast = createToast("Club deleted successfully.", "bg-warning");
}

// Add club
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_club'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $desc = mysqli_real_escape_string($conn, $_POST['description']);
  $advisor = mysqli_real_escape_string($conn, $_POST['advisor']);
  $img = '';

  if (!empty($_FILES['image']['name'])) {
    if (!file_exists('../upload')) {
      mkdir('../upload', 0777, true);
    }
    $img = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], '../upload/' . $img);
  }

  $insert = mysqli_query($conn, "INSERT INTO clubs (name, description, advisor, image) 
                                 VALUES ('$name', '$desc', '$advisor', '$img')");
  $toast = createToast($insert ? "Club added successfully." : "Failed to add club.", $insert ? "bg-success" : "bg-danger");
}

function createToast($message, $color) {
  return '<div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast align-items-center text-white ' . $color . ' shadow" role="alert" data-bs-delay="3000">
              <div class="d-flex">
                <div class="toast-body">' . $message . '</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>
            </div>
          </div>';
}
?>

<style>
  .sidebar {
    position: fixed;
    top: 80px;
    left: 0;
    width: 220px;
    height: calc(100vh - 80px);
    background-color: #f4f9f5;
    padding: 90px 15px;
    border-right: 1px solid #ccc;
    overflow-y: auto;
  }

  .sidebar .btn {
    width: 100%;
    margin-bottom: 15px;
    font-weight: 600;
  }

  .main-content {
    margin-left: 240px;
    padding: 30px;
  }

  @media (max-width: 768px) {
    .sidebar {
      position: static;
      width: 100%;
      height: auto;
      border-right: none;
    }

    .main-content {
      margin-left: 0;
      padding: 15px;
    }
  }
  .modal-backdrop.show {
  opacity: 0.2; /* Less dark background */
}
.modal-backdrop {
    background-color: #000 !important;
    opacity: 0.2 !important;
  }
  .fade.modal-backdrop.show {
    opacity: 0.2 !important;
  }
</style>

<!-- Sidebar -->
<div class="sidebar">
  <a href="#users" class="btn btn-outline-dark">👤 Manage Users</a>
  <a href="#addClub" class="btn btn-outline-success">➕ Add Club</a>
  <a href="#clubs" class="btn btn-outline-primary">🏫 Existing Clubs</a>
</div>

<!-- Main Content -->
<div class="main-content">
<?php
// Count users
$totalUsers     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$totalStudents  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'student'"))['total'];
$totalMentors   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'mentor'"))['total'];
$totalClubs     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM clubs"))['total'];
?>

  <h2 class="text-center mb-4">Admin Dashboard – Welcome, <?php echo $_SESSION['name']; ?></h2>
  
  <?php if (!empty($toast)) echo $toast; ?>
  <div class="row text-center mb-4">
  <div class="col-md-3 mb-3">
    <div class="card shadow border-start border-success border-3">
      <div class="card-body">
        <h5 class="card-title text-muted">Total Users</h5>
        <h2 class="fw-bold text-success"><?php echo $totalUsers; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card shadow border-start border-primary border-3">
      <div class="card-body">
        <h5 class="card-title text-muted">Students</h5>
        <h2 class="fw-bold text-primary"><?php echo $totalStudents; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card shadow border-start border-info border-3">
      <div class="card-body">
        <h5 class="card-title text-muted">Mentors</h5>
        <h2 class="fw-bold text-info"><?php echo $totalMentors; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card shadow border-start border-warning border-3">
      <div class="card-body">
        <h5 class="card-title text-muted">Clubs</h5>
        <h2 class="fw-bold text-warning"><?php echo $totalClubs; ?></h2>
      </div>
    </div>
  </div>
</div>

  <!-- Manage Users -->
<section id="users" class="card mb-5 shadow">
  <div class="card-body">
    <h4 class="mb-3">Manage Users</h4>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr><th>ID</th><th>Name</th><th>Email / ID</th><th>Role</th><th>Club</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php
          $users = mysqli_query($conn, "SELECT u.*, c.name AS club FROM users u LEFT JOIN clubs c ON u.club_id = c.id");
          while ($u = mysqli_fetch_assoc($users)) {
            $id = $u['id'];
            $info = $u['role'] === 'mentor' ? $u['employment_id'] : $u['email'];
          ?>
            <tr>
              <td><?php echo $id; ?></td>
              <td><?php echo $u['name']; ?></td>
              <td><?php echo $info; ?></td>
              <td><?php echo $u['role']; ?></td>
              <td><?php echo $u['club'] ?? '-'; ?></td>
              <td>
                <!-- Button to trigger modal -->
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?php echo $id; ?>">Delete</button>
              </td>
            </tr>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteUserModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="deleteUserLabel<?php echo $id; ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserLabel<?php echo $id; ?>">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete <strong><?php echo htmlspecialchars($u['name']); ?></strong>?
                  </div>
                  <div class="modal-footer">
                    <a href="?delete_user=<?php echo $id; ?>" class="btn btn-danger">Yes, Delete</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  </div>
                </div>
              </div>
            </div>

          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
  <!-- Add Club -->
  <section id="addClub" class="card shadow mb-5">
    <div class="card-body">
      <h4 class="mb-3">Add New Club</h4>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_club" value="1">
        <div class="mb-3"><label class="form-label">Club Name</label><input name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" required></textarea></div>
        <div class="mb-3"><label class="form-label">Advisor</label><input name="advisor" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Club Image</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
        <button type="submit" class="btn btn-success w-100">Add Club</button>
      </form>
    </div>
  </section>

 <!-- Existing Clubs -->
<section id="clubs" class="card shadow">
  <div class="card-body">
    <h4 class="mb-3">Existing Clubs</h4>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr><th>ID</th><th>Club</th><th>Advisor</th><th>Image</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php
          $clubs = mysqli_query($conn, "SELECT * FROM clubs");
          while ($c = mysqli_fetch_assoc($clubs)) {
            echo "<tr>
                    <td>{$c['id']}</td>
                    <td>{$c['name']}</td>
                    <td>{$c['advisor']}</td>
                    <td><img src='../upload/{$c['image']}' width='80'></td>
                    <td>
                      <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteClubModal{$c['id']}'>Delete</button>

                      <!-- Delete Confirmation Modal -->
                      <div class='modal fade' id='deleteClubModal{$c['id']}' tabindex='-1' aria-labelledby='deleteClubLabel{$c['id']}' aria-hidden='true'>
                        <div class='modal-dialog modal-dialog-centered'>
                          <div class='modal-content'>
                            <div class='modal-header bg-danger text-white'>
                              <h5 class='modal-title' id='deleteClubLabel{$c['id']}'>Confirm Deletion</h5>
                              <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                            </div>
                            <div class='modal-body'>
                              Are you sure you want to delete the club <strong>" . htmlspecialchars($c['name']) . "</strong>?
                            </div>
                            <div class='modal-footer'>
                              <a href='?delete_club={$c['id']}' class='btn btn-danger'>Yes, Delete</a>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

</div>

<?php include('../includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toastEl = document.querySelector('.toast');
  if (toastEl) {
    new bootstrap.Toast(toastEl).show();
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
