<?php
require_once 'config.php';
// require_once '../auth.php'; // Authentication disabled for now

// Check if user is logged in
// if (!isLoggedIn()) {
//     header('Location: ?page=login');
//     exit;
// }

try {
    $pdo = getDBConnection();
    
    // Handle form submissions
    $message = '';
    $error = '';
    
    // Add Bowler
    if (isset($_POST['add_bowler'])) {
        $nickname = trim($_POST['nickname']);
        $dexterity = $_POST['dexterity'];
        $style = $_POST['style'];
        $uba_id = trim($_POST['uba_id']);
        $usbc_id = trim($_POST['usbc_id']);
        $home_house_id = $_POST['home_house_id'] ?: null;
        
        if (empty($nickname)) {
            $error = "Nickname is required";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO bowlers (nickname, dexterity, style, uba_id, usbc_id, home_house_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nickname, $dexterity, $style, $uba_id, $usbc_id, $home_house_id]);
            $message = "Bowler '$nickname' added successfully!";
        }
    }
    
    // Update Bowler
    if (isset($_POST['update_bowler'])) {
        $bowler_id = (int)$_POST['bowler_id'];
        $nickname = trim($_POST['nickname']);
        $dexterity = $_POST['dexterity'];
        $style = $_POST['style'];
        $uba_id = trim($_POST['uba_id']);
        $usbc_id = trim($_POST['usbc_id']);
        $home_house_id = $_POST['home_house_id'] ?: null;
        
        if (empty($nickname)) {
            $error = "Nickname is required";
        } else {
            $stmt = $pdo->prepare("
                UPDATE bowlers 
                SET nickname = ?, dexterity = ?, style = ?, uba_id = ?, usbc_id = ?, home_house_id = ?
                WHERE bowler_id = ?
            ");
            $stmt->execute([$nickname, $dexterity, $style, $uba_id, $usbc_id, $home_house_id, $bowler_id]);
            $message = "Bowler '$nickname' updated successfully!";
        }
    }
    
    // Add Location
    if (isset($_POST['add_location'])) {
        $name = trim($_POST['location_name']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        
        if (empty($name)) {
            $error = "Location name is required";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO locations (name, city, state)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $city, $state]);
            $message = "Location '$name' added successfully!";
        }
    }
    
    // Add Game Series
    if (isset($_POST['add_series'])) {
        $bowler_id = $_POST['bowler_id'];
        $location_id = $_POST['location_id'] ?: null;
        $series_type = $_POST['series_type'];
        $event_date = $_POST['event_date'];
        $game1_score = (int)$_POST['game1_score'];
        $game2_score = (int)$_POST['game2_score'];
        $game3_score = (int)$_POST['game3_score'];
        
        if (empty($bowler_id) || empty($event_date)) {
            $error = "Bowler and event date are required";
        } elseif ($game1_score < 0 || $game1_score > 300 || $game2_score < 0 || $game2_score > 300 || $game3_score < 0 || $game3_score > 300) {
            $error = "Game scores must be between 0 and 300";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO game_series (bowler_id, location_id, series_type, event_date, game1_score, game2_score, game3_score)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$bowler_id, $location_id, $series_type, $event_date, $game1_score, $game2_score, $game3_score]);
            
            // Calculate totals for display message
            $total_score = $game1_score + $game2_score + $game3_score;
            $average_score = $total_score / 3;
            $message = "Game series added successfully! Total: $total_score, Average: " . number_format($average_score, 1);
        }
    }
    
    // Get data for dropdowns
    $bowlers = $pdo->query("SELECT bowler_id, nickname FROM bowlers ORDER BY nickname")->fetchAll();
    $locations = $pdo->query("SELECT location_id, name FROM locations ORDER BY name")->fetchAll();
    
    // Get bowler details for update form
    $selected_bowler = null;
    if (isset($_GET['update_bowler_id'])) {
        $bowler_id = (int)$_GET['update_bowler_id'];
        $stmt = $pdo->prepare("SELECT * FROM bowlers WHERE bowler_id = ?");
        $stmt->execute([$bowler_id]);
        $selected_bowler = $stmt->fetch();
    }
    
    // Handle CSV Upload
    if (isset($_POST['upload_csv'])) {
        $csv_type = $_POST['csv_type'];
        $upload_message = '';
        $upload_error = '';
        $debug_info = [];
        
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $file = $_FILES['csv_file'];
            $filename = $file['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if ($filetype == 'csv') {
                $handle = fopen($file['tmp_name'], 'r');
                if ($handle) {
                    $row = 1;
                    $success_count = 0;
                    $error_count = 0;
                    
                    // Skip header row
                    $headers = fgetcsv($handle);
                    
                    while (($data = fgetcsv($handle)) !== false) {
                        $row++;
                        $row_debug = "Row $row: " . implode(',', $data);
                        try {
                            switch ($csv_type) {
                                case 'bowlers':
                                    if (count($data) >= 1) { // At least nickname required
                                        $nickname = trim($data[0]);
                                        $dexterity = isset($data[1]) ? trim($data[1]) : '';
                                        $style = isset($data[2]) ? trim($data[2]) : '';
                                        $uba_id = isset($data[3]) ? trim($data[3]) : '';
                                        $usbc_id = isset($data[4]) ? trim($data[4]) : '';
                                        $home_house = isset($data[5]) ? trim($data[5]) : '';
                                        
                                        // Get home_house_id if location name provided
                                        $home_house_id = null;
                                        if (!empty($home_house)) {
                                            $stmt = $pdo->prepare("SELECT location_id FROM locations WHERE name = ?");
                                            $stmt->execute([$home_house]);
                                            $result = $stmt->fetch();
                                            $home_house_id = $result ? $result['location_id'] : null;
                                        }
                                        
                                        $stmt = $pdo->prepare("
                                            INSERT INTO bowlers (nickname, dexterity, style, uba_id, usbc_id, home_house_id)
                                            VALUES (?, ?, ?, ?, ?, ?)
                                        ");
                                        $stmt->execute([$nickname, $dexterity, $style, $uba_id, $usbc_id, $home_house_id]);
                                        $success_count++;
                                        $debug_info[] = "✓ $row_debug - Bowler added successfully";
                                    }
                                    break;
                                    
                                case 'locations':
                                    if (count($data) >= 1) { // At least name required
                                        $name = trim($data[0]);
                                        $city = isset($data[1]) ? trim($data[1]) : '';
                                        $state = isset($data[2]) ? trim($data[2]) : '';
                                        
                                        $stmt = $pdo->prepare("
                                            INSERT INTO locations (name, city, state)
                                            VALUES (?, ?, ?)
                                        ");
                                        $stmt->execute([$name, $city, $state]);
                                        $success_count++;
                                        $debug_info[] = "✓ $row_debug - Location added successfully";
                                    }
                                    break;
                                    
                                case 'game_series':
                                    if (count($data) >= 4) { // bowler, location, date, game1 required
                                        $bowler_nickname = trim($data[0]);
                                        $location_name = isset($data[1]) ? trim($data[1]) : '';
                                        $event_date = trim($data[2]);
                                        $game1_score = (int)$data[3];
                                        $game2_score = isset($data[4]) ? (int)$data[4] : 0;
                                        $game3_score = isset($data[5]) ? (int)$data[5] : 0;
                                        $series_type = isset($data[6]) ? trim($data[6]) : 'League';
                                        
                                        // Get bowler_id
                                        $stmt = $pdo->prepare("SELECT bowler_id FROM bowlers WHERE nickname = ?");
                                        $stmt->execute([$bowler_nickname]);
                                        $bowler_result = $stmt->fetch();
                                        
                                        if ($bowler_result) {
                                            $bowler_id = $bowler_result['bowler_id'];
                                            
                                            // Get location_id if location name provided
                                            $location_id = null;
                                            if (!empty($location_name)) {
                                                $stmt = $pdo->prepare("SELECT location_id FROM locations WHERE name = ?");
                                                $stmt->execute([$location_name]);
                                                $location_result = $stmt->fetch();
                                                $location_id = $location_result ? $location_result['location_id'] : null;
                                            }
                                            
                                            $stmt = $pdo->prepare("
                                                INSERT INTO game_series (bowler_id, location_id, series_type, event_date, game1_score, game2_score, game3_score)
                                                VALUES (?, ?, ?, ?, ?, ?, ?)
                                            ");
                                            $stmt->execute([$bowler_id, $location_id, $series_type, $event_date, $game1_score, $game2_score, $game3_score]);
                                            $success_count++;
                                            $debug_info[] = "✓ $row_debug - Game series added successfully (Bowler ID: $bowler_id, Location ID: " . ($location_id ?? 'NULL') . ")";
                                        } else {
                                            $error_count++;
                                            $debug_info[] = "✗ $row_debug - Bowler '$bowler_nickname' not found";
                                        }
                                    }
                                    break;
                            }
                        } catch (Exception $e) {
                            $error_count++;
                            $debug_info[] = "✗ $row_debug - SQL Error: " . $e->getMessage();
                            error_log("CSV Import Error Row $row: " . $e->getMessage());
                        }
                    }
                    fclose($handle);
                    
                    if ($success_count > 0) {
                        $upload_message = "Successfully imported $success_count records from CSV.";
                        if ($error_count > 0) {
                            $upload_message .= " $error_count records had errors.";
                        }
                    } else {
                        $upload_error = "No records were imported. Please check your CSV format.";
                    }
                } else {
                    $upload_error = "Could not read the CSV file.";
                }
            } else {
                $upload_error = "Please upload a valid CSV file.";
            }
        } else {
            $upload_error = "Please select a file to upload.";
        }
    }
    
} catch(PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog me-2"></i>Admin Panel</h2>
    <div class="text-muted">Data Management</div>
    <!-- Authentication UI disabled for now
    <div class="d-flex align-items-center">
        <div class="text-muted me-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
        <a href="?page=admin&logout=1" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
        </a>
    </div>
    -->
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($upload_message) && $upload_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($upload_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($upload_error) && $upload_error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($upload_error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($debug_info) && !empty($debug_info)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bug me-2"></i>CSV Import Debug Information</h6>
        </div>
        <div class="card-body">
            <div style="max-height: 300px; overflow-y: auto;">
                <?php foreach ($debug_info as $info): ?>
                    <div class="mb-1">
                        <small class="<?php echo strpos($info, '✓') === 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo htmlspecialchars($info); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Admin Forms -->
<div class="row">
    <!-- Add Bowler -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add Bowler</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nickname" class="form-label">Nickname *</label>
                        <input type="text" name="nickname" id="nickname" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dexterity" class="form-label">Dexterity</label>
                                <select name="dexterity" id="dexterity" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Right">Right</option>
                                    <option value="Left">Left</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="style" class="form-label">Style</label>
                                <select name="style" id="style" class="form-select">
                                    <option value="">Select</option>
                                    <option value="1 Handed">1 Handed</option>
                                    <option value="2 Handed">2 Handed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="uba_id" class="form-label">UBA ID</label>
                                <input type="text" name="uba_id" id="uba_id" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usbc_id" class="form-label">USBC ID</label>
                                <input type="text" name="usbc_id" id="usbc_id" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="home_house_id" class="form-label">Home House</label>
                        <select name="home_house_id" id="home_house_id" class="form-select">
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location['location_id']; ?>">
                                    <?php echo htmlspecialchars($location['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_bowler" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Bowler
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Bowler -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Bowler</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <input type="hidden" name="page" value="admin">
                    <div class="mb-3">
                        <label for="update_bowler_id" class="form-label">Select Bowler</label>
                        <select name="update_bowler_id" id="update_bowler_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Choose a bowler...</option>
                            <?php foreach ($bowlers as $bowler): ?>
                                <option value="<?php echo $bowler['bowler_id']; ?>" 
                                        <?php echo (isset($_GET['update_bowler_id']) && $_GET['update_bowler_id'] == $bowler['bowler_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($bowler['nickname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                
                <?php if ($selected_bowler): ?>
                    <form method="POST">
                        <input type="hidden" name="bowler_id" value="<?php echo $selected_bowler['bowler_id']; ?>">
                        <div class="mb-3">
                            <label for="update_nickname" class="form-label">Nickname *</label>
                            <input type="text" name="nickname" id="update_nickname" class="form-control" 
                                   value="<?php echo htmlspecialchars($selected_bowler['nickname']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_dexterity" class="form-label">Dexterity</label>
                                    <select name="dexterity" id="update_dexterity" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Right" <?php echo ($selected_bowler['dexterity'] == 'Right') ? 'selected' : ''; ?>>Right</option>
                                        <option value="Left" <?php echo ($selected_bowler['dexterity'] == 'Left') ? 'selected' : ''; ?>>Left</option>
                                        <option value="Ambidextrous" <?php echo ($selected_bowler['dexterity'] == 'Ambidextrous') ? 'selected' : ''; ?>>Ambidextrous</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_style" class="form-label">Style</label>
                                    <select name="style" id="update_style" class="form-select">
                                        <option value="">Select</option>
                                        <option value="1 Handed" <?php echo ($selected_bowler['style'] == '1 Handed') ? 'selected' : ''; ?>>1 Handed</option>
                                        <option value="2 Handed" <?php echo ($selected_bowler['style'] == '2 Handed') ? 'selected' : ''; ?>>2 Handed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_uba_id" class="form-label">UBA ID</label>
                                    <input type="text" name="uba_id" id="update_uba_id" class="form-control" 
                                           value="<?php echo htmlspecialchars($selected_bowler['uba_id'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_usbc_id" class="form-label">USBC ID</label>
                                    <input type="text" name="usbc_id" id="update_usbc_id" class="form-control" 
                                           value="<?php echo htmlspecialchars($selected_bowler['usbc_id'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="update_home_house_id" class="form-label">Home House</label>
                            <select name="home_house_id" id="update_home_house_id" class="form-select">
                                <option value="">Select Location</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo $location['location_id']; ?>" 
                                            <?php echo ($selected_bowler['home_house_id'] == $location['location_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($location['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="update_bowler" class="btn btn-info w-100">
                            <i class="fas fa-save me-2"></i>Update Bowler
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-edit fa-3x mb-3"></i>
                        <p>Select a bowler from the dropdown above to update their information.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Location -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-marker-plus me-2"></i>Add Location</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="location_name" class="form-label">Location Name *</label>
                        <input type="text" name="location_name" id="location_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" name="city" id="city" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" name="state" id="state" class="form-control" maxlength="2">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_location" class="btn btn-success w-100">
                        <i class="fas fa-plus me-2"></i>Add Location
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Game Series -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Game Series</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3 position-relative">
                        <label for="bowler_search" class="form-label">Bowler *</label>
                        <input type="text" id="bowler_search" class="form-control" placeholder="Type bowler name..." autocomplete="off" required>
                        <input type="hidden" name="bowler_id" id="bowler_id" required>
                        <div id="bowler_suggestions" class="list-group position-absolute" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none; width: 100%; top: 100%; border: 1px solid #ddd; border-radius: 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);"></div>
                    </div>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select name="location_id" id="location_id" class="form-select">
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location['location_id']; ?>">
                                    <?php echo htmlspecialchars($location['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="series_type" class="form-label">Series Type</label>
                                <select name="series_type" id="series_type" class="form-select">
                                    <option value="League">League</option>
                                    <option value="Tournament">Tournament</option>
                                    <option value="Practice">Practice</option>
                                    <option value="Tour Stop">Tour Stop</option>
                                    <option value="Playoffs">Playoffs</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Date *</label>
                                <input type="date" name="event_date" id="event_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="game1_score" class="form-label">Game 1</label>
                                <input type="number" name="game1_score" id="game1_score" class="form-control" min="0" max="300" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="game2_score" class="form-label">Game 2</label>
                                <input type="number" name="game2_score" id="game2_score" class="form-control" min="0" max="300" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="game3_score" class="form-label">Game 3</label>
                                <input type="number" name="game3_score" id="game3_score" class="form-control" min="0" max="300" value="0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_series" class="btn btn-warning w-100">
                        <i class="fas fa-plus me-2"></i>Add Series
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary"><?php echo count($bowlers); ?></h4>
                            <p class="text-muted mb-0">Total Bowlers</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success"><?php echo count($locations); ?></h4>
                            <p class="text-muted mb-0">Total Locations</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <?php 
                            $series_count = $pdo->query("SELECT COUNT(*) as count FROM game_series")->fetch()['count'];
                            ?>
                            <h4 class="text-warning"><?php echo number_format($series_count); ?></h4>
                            <p class="text-muted mb-0">Total Series</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <?php 
                            $recent_series = $pdo->query("SELECT COUNT(*) as count FROM game_series WHERE event_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch()['count'];
                            ?>
                            <h4 class="text-info"><?php echo number_format($recent_series); ?></h4>
                            <p class="text-muted mb-0">Last 30 Days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSV Upload Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-csv me-2"></i>CSV Import</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6>Bulk Import Options</h6>
                        <p class="text-muted small">Upload CSV files to import multiple records at once.</p>
                    </div>
                    <div class="col-md-8">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="csv_type" class="form-label">Import Type</label>
                                    <select name="csv_type" id="csv_type" class="form-select" required>
                                        <option value="">Select type...</option>
                                        <option value="bowlers">Bowlers</option>
                                        <option value="locations">Locations</option>
                                        <option value="game_series">Game Series</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="csv_file" class="form-label">CSV File</label>
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" name="upload_csv" class="btn btn-primary w-100">
                                        <i class="fas fa-upload me-2"></i>Import
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- CSV Format Instructions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>CSV Format Instructions</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>Bowlers CSV</strong>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Headers:</strong> nickname,dexterity,style,uba_id,usbc_id,home_house<br>
                                            <strong>Example:</strong><br>
                                            John Doe,Right,1 Handed,12345,67890,Lodi Lanes
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <strong>Locations CSV</strong>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Headers:</strong> name,city,state<br>
                                            <strong>Example:</strong><br>
                                            Lodi Lanes,Lodi,NJ
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <strong>Game Series CSV</strong>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Headers:</strong> bowler_nickname,location_name,event_date,game1_score,game2_score,game3_score,series_type<br>
                                            <strong>Example:</strong><br>
                                            John Doe,Lodi Lanes,2024-01-15,200,220,180,League
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Force cache refresh for autocomplete functionality
console.log('Admin page loaded - version 1.1');

$(document).ready(function() {
    // Set default date to today
    $('#event_date').val(new Date().toISOString().split('T')[0]);
    
    // Auto-calculate total and average
    $('input[name="game1_score"], input[name="game2_score"], input[name="game3_score"]').on('input', function() {
        const game1 = parseInt($('#game1_score').val()) || 0;
        const game2 = parseInt($('#game2_score').val()) || 0;
        const game3 = parseInt($('#game3_score').val()) || 0;
        
        const total = game1 + game2 + game3;
        const average = total / 3;
        
        // You could display this in a preview area if needed
        console.log(`Total: ${total}, Average: ${average.toFixed(1)}`);
    });
    
    // Bowler autocomplete functionality
    const bowlers = <?php echo json_encode($bowlers); ?>;
    console.log('Bowlers data sample:', bowlers.slice(0, 3));
    const bowlerSearch = $('#bowler_search');
    const bowlerSuggestions = $('#bowler_suggestions');
    const bowlerIdInput = $('#bowler_id');
    
    console.log('Autocomplete initialized with', bowlers.length, 'bowlers');
    console.log('Bowler search element:', bowlerSearch.length);
    console.log('Bowler suggestions element:', bowlerSuggestions.length);
    
    // Test if the input field exists and can be typed in
    if (bowlerSearch.length > 0) {
        console.log('Bowler search field found, adding event handlers');
        bowlerSearch.focus();
        console.log('Focus test - if you see this, the field is working');
    } else {
        console.error('Bowler search field not found!');
    }
    
    bowlerSearch.on('input', function() {
        console.log('Input event triggered, value:', $(this).val());
        const query = $(this).val().toLowerCase();
        
        if (query.length < 1) {
            console.log('Query too short, hiding suggestions');
            bowlerSuggestions.hide();
            return;
        }
        
        console.log('Filtering bowlers for query:', query);
        const filteredBowlers = bowlers.filter(bowler => 
            bowler.nickname.toLowerCase().includes(query)
        );
        
        console.log('Found', filteredBowlers.length, 'matching bowlers');
        
        if (filteredBowlers.length > 0) {
            bowlerSuggestions.empty();
            filteredBowlers.forEach(bowler => {
                const item = $(`<a href="#" class="list-group-item list-group-item-action" data-id="${bowler.bowler_id}" data-name="${bowler.nickname}">${bowler.nickname}</a>`);
                bowlerSuggestions.append(item);
            });
            console.log('Showing suggestions');
            bowlerSuggestions.show();
        } else {
            console.log('No matches, hiding suggestions');
            bowlerSuggestions.hide();
        }
    });
    
    // Handle suggestion selection
    bowlerSuggestions.on('click', 'a', function(e) {
        e.preventDefault();
        const bowlerId = $(this).data('id');
        const bowlerName = $(this).data('name');
        
        bowlerSearch.val(bowlerName);
        bowlerIdInput.val(bowlerId);
        bowlerSuggestions.hide();
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#bowler_search, #bowler_suggestions').length) {
            bowlerSuggestions.hide();
        }
    });
    
    // Handle keyboard navigation
    bowlerSearch.on('keydown', function(e) {
        const visibleSuggestions = bowlerSuggestions.find('a:visible');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const currentFocus = bowlerSuggestions.find('a:focus');
            if (currentFocus.length) {
                currentFocus.next().focus();
            } else {
                visibleSuggestions.first().focus();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const currentFocus = bowlerSuggestions.find('a:focus');
            if (currentFocus.length) {
                currentFocus.prev().focus();
            } else {
                visibleSuggestions.last().focus();
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const focusedSuggestion = bowlerSuggestions.find('a:focus');
            if (focusedSuggestion.length) {
                focusedSuggestion.click();
            }
        } else if (e.key === 'Escape') {
            bowlerSuggestions.hide();
        }
    });
});
</script>
