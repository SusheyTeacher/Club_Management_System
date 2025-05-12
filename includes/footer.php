
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php
$imgPrefix = (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? '../' : '';
?>
<footer class="bg-dark text-white pt-4">
  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-md-12 mb-3 text-center">
        <h6 class="text-uppercase mb-3">Developed By</h6>
        <div class="d-flex justify-content-center align-items-center gap-4">
          <div class="d-flex align-items-center">
            <img src="<?php echo $imgPrefix; ?>upload/paro.jpg" alt="Saraswati Rai" height = "45" width="45" class="me-2 rounded-circle">
            <span>Saraswati Rai || 987680</span>
          </div>
          <div class="d-flex align-items-center">
            <img src="<?php echo $imgPrefix; ?>upload/contact.jpg" alt="Sushila Puri" height = "45" width="45" class="me-2 rounded-circle">
            <span>Sushila Puri || 987679</span>
          </div>
        </div>
      </div>
    </div>
    <div class="text-center mt-3 border-top pt-3">
      <p class="small mb-0"> All rights reserved. &copy; 2025</p>
    </div>
  </div>
</footer>

