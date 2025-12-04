<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('admin');

// Handle Create Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_room'])) {
    verify_csrf_token($_POST['csrf_token']);
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    
    $stmt = $pdo->prepare("INSERT INTO rooms (name, location, capacity) VALUES (?, ?, ?)");
    $stmt->execute([$name, $location, $capacity]);
    setFlashMessage('success', 'Room added.');
    header("Location: rooms.php");
    exit;
}

$rooms = $pdo->query("SELECT * FROM rooms ORDER BY name")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Manage Rooms</h1>
            <?php displayFlashMessage(); ?>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">Add Room</div>
                        <div class="card-body">
                            <form method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="create_room" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Room Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" name="capacity" class="form-control" value="1" min="1">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Add Room</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Rooms</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td><?php echo h($room['name']); ?></td>
                                        <td><?php echo h($room['location']); ?></td>
                                        <td><?php echo h($room['capacity']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
