<?php
require_once 'config.php';

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
    
} catch(PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog me-2"></i>Admin Panel</h2>
    <div class="text-muted">Data Management</div>
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

<!-- Admin Forms -->
<div class="row">
    <!-- Add Bowler -->
    <div class="col-md-4">
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

    <!-- Add Location -->
    <div class="col-md-4">
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Game Series</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="bowler_id" class="form-label">Bowler *</label>
                        <select name="bowler_id" id="bowler_id" class="form-select" required>
                            <option value="">Select Bowler</option>
                            <?php foreach ($bowlers as $bowler): ?>
                                <option value="<?php echo $bowler['bowler_id']; ?>">
                                    <?php echo htmlspecialchars($bowler['nickname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

<script>
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
});
</script>
