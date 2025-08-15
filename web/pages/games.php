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
    
    // Build the query to extract individual games from game_series
    $query = "
        SELECT 
            gs.series_id,
            b.nickname AS bowler_name,
            l.name AS location_name,
            gs.series_type,
            gs.event_date,
            gs.game1_score,
            gs.game2_score,
            gs.game3_score,
            gs.total_score AS series_total,
            gs.average_score AS series_average,
            'Game 1' as game_label,
            1 as game_number,
            gs.game1_score as score,
            CASE 
                WHEN gs.game1_score >= 300 THEN 'Perfect Game'
                WHEN gs.game1_score >= 250 THEN 'Excellent'
                WHEN gs.game1_score >= 200 THEN 'Good'
                WHEN gs.game1_score >= 150 THEN 'Average'
                ELSE 'Below Average'
            END AS game_rating
        FROM game_series gs
        JOIN bowlers b ON gs.bowler_id = b.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
        WHERE gs.game1_score > 0
        
        UNION ALL
        
        SELECT 
            gs.series_id,
            b.nickname AS bowler_name,
            l.name AS location_name,
            gs.series_type,
            gs.event_date,
            gs.game1_score,
            gs.game2_score,
            gs.game3_score,
            gs.total_score AS series_total,
            gs.average_score AS series_average,
            'Game 2' as game_label,
            2 as game_number,
            gs.game2_score as score,
            CASE 
                WHEN gs.game2_score >= 300 THEN 'Perfect Game'
                WHEN gs.game2_score >= 250 THEN 'Excellent'
                WHEN gs.game2_score >= 200 THEN 'Good'
                WHEN gs.game2_score >= 150 THEN 'Average'
                ELSE 'Below Average'
            END AS game_rating
        FROM game_series gs
        JOIN bowlers b ON gs.bowler_id = b.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
        WHERE gs.game2_score > 0
        
        UNION ALL
        
        SELECT 
            gs.series_id,
            b.nickname AS bowler_name,
            l.name AS location_name,
            gs.series_type,
            gs.event_date,
            gs.game1_score,
            gs.game2_score,
            gs.game3_score,
            gs.total_score AS series_total,
            gs.average_score AS series_average,
            'Game 3' as game_label,
            3 as game_number,
            gs.game3_score as score,
            CASE 
                WHEN gs.game3_score >= 300 THEN 'Perfect Game'
                WHEN gs.game3_score >= 250 THEN 'Excellent'
                WHEN gs.game3_score >= 200 THEN 'Good'
                WHEN gs.game3_score >= 150 THEN 'Average'
                ELSE 'Below Average'
            END AS game_rating
        FROM game_series gs
        JOIN bowlers b ON gs.bowler_id = b.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
        WHERE gs.game3_score > 0
    ";
    
    // Add filters
    $where_conditions = [];
    $params = [];
    
    if ($bowler_filter) {
        $where_conditions[] = "bowler_name LIKE ?";
        $params[] = "%$bowler_filter%";
    }
    
    if ($location_filter) {
        $where_conditions[] = "location_name LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    if ($series_type_filter) {
        $where_conditions[] = "series_type = ?";
        $params[] = $series_type_filter;
    }
    
    if ($game_number_filter) {
        $where_conditions[] = "game_number = ?";
        $params[] = $game_number_filter;
    }
    
    if ($score_min !== '') {
        $where_conditions[] = "score >= ?";
        $params[] = $score_min;
    }
    
    if ($score_max !== '') {
        $where_conditions[] = "score <= ?";
        $params[] = $score_max;
    }
    
    if (!empty($where_conditions)) {
        $query = "SELECT * FROM ($query) as games WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " ORDER BY event_date DESC, series_id, game_number";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $games = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT nickname FROM bowlers ORDER BY nickname")->fetchAll();
    $locations_stmt = $pdo->query("SELECT DISTINCT name FROM locations WHERE name IS NOT NULL ORDER BY name")->fetchAll();
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM game_series ORDER BY series_type")->fetchAll();
    
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
        FROM (
            SELECT gs.game1_score as score FROM game_series gs JOIN bowlers b ON gs.bowler_id = b.bowler_id LEFT JOIN locations l ON gs.location_id = l.location_id WHERE gs.game1_score > 0
            UNION ALL
            SELECT gs.game2_score as score FROM game_series gs JOIN bowlers b ON gs.bowler_id = b.bowler_id LEFT JOIN locations l ON gs.location_id = l.location_id WHERE gs.game2_score > 0
            UNION ALL
            SELECT gs.game3_score as score FROM game_series gs JOIN bowlers b ON gs.bowler_id = b.bowler_id LEFT JOIN locations l ON gs.location_id = l.location_id WHERE gs.game3_score > 0
        ) all_games
    ";
    
    // Apply filters to statistics
    if (!empty($where_conditions)) {
        $stats_query = "
            SELECT 
                COUNT(*) as total_games,
                AVG(score) as avg_score,
                MAX(score) as highest_score,
                MIN(score) as lowest_score,
                COUNT(CASE WHEN score = 300 THEN 1 END) as perfect_games,
                COUNT(CASE WHEN score >= 250 THEN 1 END) as high_games,
                COUNT(CASE WHEN score >= 200 THEN 1 END) as good_games
            FROM ($query) as filtered_games
        ";
    }
    
    $stats_stmt = $pdo->prepare($stats_query);
    $stats_stmt->execute($params);
    $stats = $stats_stmt->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-gamepad me-2"></i>Game Details (Alternative)</h2>
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
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="page" value="games">
                <div class="col-md-2">
                    <label for="bowler" class="form-label">Bowler</label>
                    <select name="bowler" id="bowler" class="form-select">
                        <option value="">All Bowlers</option>
                        <?php foreach ($bowlers_stmt as $bowler): ?>
                            <option value="<?php echo htmlspecialchars($bowler['nickname']); ?>" 
                                    <?php echo ($bowler_filter === $bowler['nickname']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bowler['nickname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-select">
                        <option value="">All Locations</option>
                        <?php foreach ($locations_stmt as $location): ?>
                            <option value="<?php echo htmlspecialchars($location['name']); ?>" 
                                    <?php echo ($location_filter === $location['name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location['name']); ?>
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
