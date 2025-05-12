<?php include('db.php'); ?>
<?php include('includes/header.php'); ?>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Explore Our Clubs</h2>
    <form method="GET" class="d-flex">
      <input type="text" name="search" placeholder="Search clubs..." class="form-control me-2" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      <button class="btn btn-outline-primary">Search</button>
    </form>
  </div>

  <div class="row">
    <?php
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $query = "SELECT * FROM clubs WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      while ($club = mysqli_fetch_assoc($result)) {
        $clubId = $club['id'];
        $clubName = htmlspecialchars($club['name']);
        $clubDesc = htmlspecialchars($club['description']);
        $shortDesc = htmlspecialchars(substr($club['description'], 0, 100)) . '...';
        $advisor = htmlspecialchars($club['advisor']);
        $image = $club['image'];
      
        echo "
          <div class='col-md-4 mb-4'>
            <div class='card h-100 shadow-sm'>
              <img src='upload/{$image}' class='card-img-top' style='height: 200px; object-fit: cover;' alt='club image'>
              <div class='card-body'>
                <h5 class='card-title'>{$clubName}</h5>
                <p class='card-text'>{$shortDesc}</p>
                <p class='text-muted'><small>Advisor: {$advisor}</small></p>
                <button class='btn btn-outline-secondary btn-sm' data-bs-toggle='modal' data-bs-target='#clubModal{$clubId}'>Read More</button>
              </div>
            </div>
          </div>
      
          <!-- Modal -->
          <div class='modal fade' id='clubModal{$clubId}' tabindex='-1' aria-labelledby='clubModalLabel{$clubId}' aria-hidden='true'>
            <div class='modal-dialog'>
              <div class='modal-content bg-light'>
                <div class='modal-header'>
                  <h5 class='modal-title' id='clubModalLabel{$clubId}'>{$clubName}</h5>
                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body'>
                  <img src='upload/{$image}' class='img-fluid mb-3' style='border-radius: 10px;' alt='club image'>
                  <p><strong>Advisor:</strong> {$advisor}</p>
                  <p>{$clubDesc}</p>
                </div>
                <div class='modal-footer'>
                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                </div>
              </div>
            </div>
          </div>
        ";
      }
      
    } else {
      echo '<p class="text-muted text-center">No clubs found.</p>';
    }
    ?>
  </div>
</div>

<?php include('includes/footer.php'); ?>
