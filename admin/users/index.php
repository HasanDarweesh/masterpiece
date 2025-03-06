<?php
session_start();
require_once "../../includes/database/config.php";

// For an admin, show only users with role 'user'. For superadmin, show all.
if ($_SESSION['role'] === 'admin') {
    $query = "SELECT * FROM users WHERE role = 'user' ORDER BY deleted_at ASC";
} else if ($_SESSION['role'] === 'superadmin') {
    $query = "SELECT * FROM users ORDER BY deleted_at ASC";
} else {
    header("Location: ../../public/login/index.php");
    exit;
}
$stmt = $pdo->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
  <?php include('../../includes/admin/navbar/navbar.php') ?>
        

   <div class="container-fluid mt-3">
    <div class="row">
      <!-- Sidebar -->
      <?php include('../../includes/admin/sidebar/sidebar.php') ?>


        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
        <div class="container my-auto mx-auto">
        
         <h1>Users Management</h1>
         
         <!-- Live Search Input (client-side filtering) -->
         <input type="text" id="liveSearch" class="form-control" placeholder="Search by name, phone, email or role">
       
         <!-- Button to trigger Create Modal -->
         <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
       Add New User
         </button>
       
         <div class="table-responsive">
           <table id="main_table" class="table ">
                  <thead class="table-dark">
           <tr>
             <th>Profile Picture</th>
             <th>Name</th>
             <th>Email</th>
             <th>Phone</th>
             <th>Address</th>
             <th>Role</th>
             <th>Actions</th>
           </tr>
                  </thead>
                  <tbody>
           <?php
           while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
               // If soft-deleted, mark row red.
               $rowClass = !empty($user['deleted_at']) ? 'table-danger' : '';
               // Build profile picture URL.
               if (!empty($user['profile_picture'])) {
                   $imgPath = "../../uploads/avatars/" . $user['profile_picture'];
               } else {
                   $imgPath = "../../uploads/default-avatar.png";
               }
           ?>
           <tr class="<?= $rowClass ?>">
             <td>
               <a href="view_picture.php?id=<?= htmlspecialchars($user['id']) ?>">
                 <img src="<?= $imgPath ?>" alt="Profile Picture" width="50" height="50">
               </a>
             </td>
             <td><?= htmlspecialchars($user['name']) ?></td>
             <td><?= htmlspecialchars($user['email']) ?></td>
             <td><?= htmlspecialchars($user['phone']) ?></td>
             <td><?= htmlspecialchars($user['address']) ?></td>
             <td><?= htmlspecialchars($user['role']) ?></td>
             <td>
               <?php
               // If the user is soft-deleted, show the Reactivate button.
               if (!empty($user['deleted_at'])) {
                   echo '<a class="btn btn-success btn-sm" href="reactivate.php?id='
                         . htmlspecialchars($user['id']) . '"><i class="bi bi-arrow-counterclockwise"></i> Reactivate</a>';
               } else {
                   // For active users, show Edit and Delete buttons for both admin and superadmin.
                   echo '<div class="d-flex gap-2">
                           <button type="button" class="btn btn-primary btn-md edit-btn"
                                   data-id="' . htmlspecialchars($user['id']) . '"
                                   data-name="' . htmlspecialchars($user['name'], ENT_QUOTES) . '"
                                   data-email="' . htmlspecialchars($user['email'], ENT_QUOTES) . '"
                                   data-phone="' . htmlspecialchars($user['phone'], ENT_QUOTES) . '"
                                   data-address="' . htmlspecialchars($user['address'], ENT_QUOTES) . '"
                                   data-role="' . htmlspecialchars($user['role'], ENT_QUOTES) . '"
                                   data-profile="' . $imgPath . '"
                                   data-bs-toggle="modal" data-bs-target="#editModal">
                             <i class="bi bi-pencil"></i>
                           </button>
                           <button type="button" class="btn btn-danger btn-md delete-btn"
                                   data-id="' . htmlspecialchars($user['id']) . '"
                                   data-bs-toggle="modal" data-bs-target="#deleteModal">
                             <i class="bi bi-trash"></i>
                           </button>
                         </div>';
               }
               ?>
             </td>
           </tr>
           <?php } ?>
                  </tbody>
           </table>
         </div>
   </div>
    </main>
    </div>
    </div>

  
  <!-- Create Modal (loaded via an iframe) -->
  <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createModalLabel">Create New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <iframe src="create.php" frameborder="0" style="width: 100%; height: 600px;"></iframe>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="editForm" action="edit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" id="editUserId">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Form fields -->
            <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password (leave blank to keep unchanged)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
              <label>Phone</label>
              <input type="number" name="phone" id="editPhone" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Address</label>
              <input type="text" name="address" id="editAddress" class="form-control">
            </div>
            <div class="mb-3">
              <label>Role</label>
              <?php if ($_SESSION['role'] === 'superadmin'): ?>
                <select name="role" id="editRole" class="form-select">
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                  <option value="superadmin">Superadmin</option>
                </select>
              <?php else: ?>
                <input type="hidden" name="role" value="user">
                <p class="form-control-plaintext">User</p>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label>Profile Picture (optional)</label>
              <input type="file" name="profile_picture" class="form-control">
              <br>
              <img id="editProfilePic" src="" alt="Current Profile Picture" width="100" height="100">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteForm" action="delete.php" method="POST">
        <input type="hidden" name="user_id" id="deleteUserId">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this user?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Delete</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript to populate modals -->
  <script>
    // Edit Modal population
    document.querySelectorAll('.edit-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const userId  = this.getAttribute('data-id');
        const name    = this.getAttribute('data-name');
        const email   = this.getAttribute('data-email');
        const phone   = this.getAttribute('data-phone');
        const address = this.getAttribute('data-address');
        const role    = this.getAttribute('data-role');
        const profile = this.getAttribute('data-profile');
        
        document.getElementById('editUserId').value = userId;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editAddress').value = address;
        <?php if($_SESSION['role'] === 'superadmin'): ?>
          document.getElementById('editRole').value = role;
        <?php endif; ?>
        document.getElementById('editProfilePic').src = profile;
      });
    });

    // Delete Modal population
    document.querySelectorAll('.delete-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const userId = this.getAttribute('data-id');
        document.getElementById('deleteUserId').value = userId;
      });
    });

    // Live Search filtering
    document.getElementById('liveSearch').addEventListener('keyup', function() {
      var filter = this.value.toUpperCase();
      var rows = document.querySelectorAll("#main_table tbody tr");
      rows.forEach(function(row) {
        var text = row.textContent || row.innerText;
        row.style.display = (text.toUpperCase().indexOf(filter) > -1) ? "" : "none";
      });
    });
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
