<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $series_type_filter = isset($_GET['series_type']) ? $_GET['series_type'] : '';
    $game_number_filter = isset($_GET['game_number']) ? (int)$_GET['game_number'] : '';
    $score_min = isset($_GET['score_min']) ? (int)$_GET['score_min'] : '';
    $score_max = isset($_GET['score_max']) ? (int)$_GET['score_max'] : '';
    
    // Build the query with filters using game_details view
    $query = "
        SELECT * FROM game_details
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($bowler_filter) {
        $query .= " AND bowler_name LIKE ?";
        $params[] = "%$bowler_filter%";
    }
    
    if ($location_filter) {
        $query .= " AND location_name LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    if ($series_type_filter) {
        $query .= " AND series_type = ?";
        $params[] = $series_type_filter;
    }
    
    if ($game_number_filter) {
        $query .= " AND game_number = ?";
        $params[] = $game_number_filter;
    }
    
    if ($score_min !== '') {
        $query .= " AND score >= ?";
        $params[] = $score_min;
    }
    
    if ($score_max !== '') {
        $query .= " AND score <= ?";
        $params[] = $score_max;
    }
    
    $query .= " ORDER BY event_date DESC, series_id, game_number";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $games = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT bowler_name FROM game_details ORDER BY bowler_name")->fetchAll();
    $locations_stmt = $pdo->query("SELECT DISTINCT location_name FROM game_details WHERE location_name IS NOT NULL ORDER BY location_name")->fetchAll();
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM game_details ORDER BY series_type")->fetchAll();
    
    // Calculate statistics with the same filters
    $stats_query = "
        SELECT 
            COUNT(*) as total_games,
            AVG(score) as avg_score,
            MAX(score) as highest_score,
            MIN(score) as lowest_score,
            COUNT(CASE WHEN score = 300 THEN 1 END) as perfect_games,
            COUNT(CASE WHEN score >= 250 THEN 1 END) as high_games,
            COUNT(CASE WHEN score >= 200 THEN 1 END) as good_games
        FROM game_details
        WHERE 1=1
    ";
    
    // Apply the same filters to statistics
    $stats_params = [];
    
    if ($bowler_filter) {
        $stats_query .= " AND bowler_name LIKE ?";
        $stats_params[] = "%$bowler_filter%";
    }
    
    if ($location_filter) {
        $stats_query .= " AND location_name LIKE ?";
        $stats_params[] = "%$location_filter%";
    }
    
    if ($series_type_filter) {
        $stats_query .= " AND series_type = ?";
        $stats_params[] = $series_type_filter;
    }
    
    if ($game_number_filter) {
        $stats_query .= " AND game_number = ?";
        $stats_params[] = $game_number_filter;
    }
    
    if ($score_min !== '') {
        $stats_query .= " AND score >= ?";
        $stats_params[] = $score_min;
    }
    
    if ($score_max !== '') {
        $stats_query .= " AND score <= ?";
        $stats_params[] = $score_max;
    }
    
    $stats_stmt = $pdo->prepare($stats_query);
    $stats_stmt->execute($stats_params);
    $stats = $stats_stmt->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-gamepad me-2"></i>Game Details</h2>
    <div class="text-muted"><?php echo number_format($stats['total_games']); ?> games found</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Games</h5>
                    <h3><?php echo number_format($stats['total_games']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Avg Score</h5>
                    <h3><?php echo number_format($stats['avg_score'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Highest Score</h5>
                    <h3><?php echo number_format($stats['highest_score']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">Perfect Games</h5>
                    <h3><?php echo number_format($stats['perfect_games']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">250+ Games</h5>
                    <h3><?php echo number_format($stats['high_games']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-secondary">200+ Games</h5>
                    <h3><?php echo number_format($stats['good_games']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="?page=games" class="row g-3">
                <div class="col-md-2">
                    <label for="bowler" class="form-label">Bowler</label>
                    <select name="bowler" id="bowler" class="form-select">
                        <option value="">All Bowlers</option>
                        <?php foreach ($bowlers_stmt as $bowler): ?>
                            <option value="<?php echo htmlspecialchars($bowler['bowler_name']); ?>" 
                                    <?php echo ($bowler_filter === $bowler['bowler_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bowler['bowler_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-select">
                        <option value="">All Locations</option>
                        <?php foreach ($locations_stmt as $location): ?>
                            <option value="<?php echo htmlspecialchars($location['location_name']); ?>" 
                                    <?php echo ($location_filter === $location['location_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location['location_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="series_type" class="form-label">Series Type</label>
                    <select name="series_type" id="series_type" class="form-select">
                        <option value="">All Types</option>
                        <?php foreach ($series_types_stmt as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['series_type']); ?>" 
                                    <?php echo ($series_type_filter === $type['series_type']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['series_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="game_number" class="form-label">Game #</label>
                    <select name="game_number" id="game_number" class="form-select">
                        <option value="">All</option>
                        <option value="1" <?php echo ($game_number_filter == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo ($game_number_filter == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo ($game_number_filter == 3) ? 'selected' : ''; ?>>3</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="score_min" class="form-label">Min Score</label>
                    <input type="number" name="score_min" id="score_min" class="form-control" 
                           min="0" max="300" value="<?php echo htmlspecialchars($score_min); ?>">
                </div>
                <div class="col-md-2">
                    <label for="score_max" class="form-label">Max Score</label>
                    <input type="number" name="score_max" id="score_max" class="form-control" 
                           min="0" max="300" value="<?php echo htmlspecialchars($score_max); ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                </div>
            </form>
            <div class="mt-2">
                <a href="?page=games" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
            </div>
        </div>
    </div>

    <!-- Games Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="gamesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bowler</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Game #</th>
                            <th>Score</th>
                            <th>Rating</th>
                            <th>Series Total</th>
                            <th>Series Avg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($game['event_date'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($game['bowler_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($game['location_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($game['series_type']); ?>">
                                        <?php echo htmlspecialchars($game['series_type']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo $game['game_number']; ?></span>
                                </td>
                                <td class="text-center">
                                    <strong class="<?php echo getScoreClass($game['score']); ?>">
                                        <?php echo number_format($game['score']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getGameRatingColor($game['game_rating']); ?>">
                                        <?php echo htmlspecialchars($game['game_rating']); ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo number_format($game['series_total']); ?></td>
                                <td class="text-center"><?php echo number_format($game['series_average'], 1); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#gamesTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']], // Sort by date descending
        columnDefs: [
            { targets: [4,5,7,8], className: 'text-center' }
        ]
    });
});
</script>

<?php
function getGameRatingColor($rating) {
    switch($rating) {
        case 'Perfect Game': return 'warning';
        case 'Excellent': return 'danger';
        case 'Good': return 'success';
        case 'Average': return 'info';
        case 'Below Average': return 'secondary';
        default: return 'secondary';
    }
}
?>
