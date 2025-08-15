<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $series_type_filter = isset($_GET['series_type']) ? $_GET['series_type'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    // Build the query with filters
    $query = "
        SELECT 
            nickname,
            location,
            series_type,
            event_date,
            total_score,
            average_score,
            game1_score,
            game2_score,
            game3_score,
            series_rank
        FROM recent_performance
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($bowler_filter) {
        $query .= " AND nickname LIKE ?";
        $params[] = "%$bowler_filter%";
    }
    
    if ($location_filter) {
        $query .= " AND location LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    if ($series_type_filter) {
        $query .= " AND series_type = ?";
        $params[] = $series_type_filter;
    }
    
    $query .= " ORDER BY event_date DESC, series_rank ASC LIMIT ?";
    $params[] = $limit;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $recent_performances = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT nickname FROM bowlers ORDER BY nickname");
    $bowlers = $bowlers_stmt->fetchAll();
    
    $locations_stmt = $pdo->query("SELECT DISTINCT name FROM locations ORDER BY name");
    $locations = $locations_stmt->fetchAll();
    
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM game_series ORDER BY series_type");
    $series_types = $series_types_stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Recent Performance</h2>
    <div class="text-muted"><?php echo count($recent_performances); ?> recent performances</div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="?page=recent" class="row g-3">
            <div class="col-md-3">
                <label for="bowler" class="form-label">Bowler</label>
                <select name="bowler" id="bowler" class="form-select">
                    <option value="">All Bowlers</option>
                    <?php foreach ($bowlers as $bowler): ?>
                        <option value="<?php echo htmlspecialchars($bowler['nickname']); ?>" 
                                <?php echo ($bowler_filter === $bowler['nickname']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($bowler['nickname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="location" class="form-label">Location</label>
                <select name="location" id="location" class="form-select">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $location): ?>
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
                    <?php foreach ($series_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['series_type']); ?>" 
                                <?php echo ($series_type_filter === $type['series_type']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['series_type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="limit" class="form-label">Show</label>
                <select name="limit" id="limit" class="form-select">
                    <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo ($limit == 100) ? 'selected' : ''; ?>>100</option>
                    <option value="200" <?php echo ($limit == 200) ? 'selected' : ''; ?>>200</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="?page=recent" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php elseif (empty($recent_performances)): ?>
    <div class="alert alert-info">No recent performances found matching your criteria.</div>
<?php else: ?>
    <!-- Recent Performance Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="recentTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bowler</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Rank</th>
                            <th>Game 1</th>
                            <th>Game 2</th>
                            <th>Game 3</th>
                            <th>Total</th>
                            <th>Average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_performances as $rp): ?>
                            <tr>
                                <td><?php echo formatDate($rp['event_date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($rp['nickname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($rp['location'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($rp['series_type']); ?>">
                                        <?php echo htmlspecialchars($rp['series_type']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?php echo $rp['series_rank']; ?></span>
                                </td>
                                <td class="text-center"><?php echo formatScore($rp['game1_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($rp['game2_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($rp['game3_score']); ?></td>
                                <td class="text-center"><strong><?php echo formatScore($rp['total_score']); ?></strong></td>
                                <td class="text-center"><?php echo formatAverage($rp['average_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Performance Summary Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Highest Total</h5>
                    <h3 class="text-primary">
                        <?php 
                        $max_total = max(array_column($recent_performances, 'total_score'));
                        echo $max_total;
                        ?>
                    </h3>
                    <p class="text-muted">
                        <?php 
                        $max_bowler = array_filter($recent_performances, function($rp) use ($max_total) {
                            return $rp['total_score'] == $max_total;
                        });
                        $max_bowler = array_values($max_bowler)[0];
                        echo htmlspecialchars($max_bowler['nickname']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Highest Average</h5>
                    <h3 class="text-success">
                        <?php 
                        $max_avg = max(array_column($recent_performances, 'average_score'));
                        echo number_format($max_avg, 1);
                        ?>
                    </h3>
                    <p class="text-muted">
                        <?php 
                        $max_avg_bowler = array_filter($recent_performances, function($rp) use ($max_avg) {
                            return $rp['average_score'] == $max_avg;
                        });
                        $max_avg_bowler = array_values($max_avg_bowler)[0];
                        echo htmlspecialchars($max_avg_bowler['nickname']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Perfect Games</h5>
                    <h3 class="text-warning">
                        <?php 
                        $perfect_games = 0;
                        foreach ($recent_performances as $rp) {
                            if ($rp['game1_score'] == 300) $perfect_games++;
                            if ($rp['game2_score'] == 300) $perfect_games++;
                            if ($rp['game3_score'] == 300) $perfect_games++;
                        }
                        echo $perfect_games;
                        ?>
                    </h3>
                    <p class="text-muted">300 Games</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Tournament Series</h5>
                    <h3 class="text-danger">
                        <?php 
                        $tournament_count = count(array_filter($recent_performances, function($rp) {
                            return $rp['series_type'] == 'Tour Stop';
                        }));
                        echo $tournament_count;
                        ?>
                    </h3>
                    <p class="text-muted">Tour Stops</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#recentTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc'], [4, 'asc']],
        columnDefs: [
            { targets: [4,5,6,7,8,9], className: 'text-center' }
        ]
    });
});
</script>

<?php
function getSeriesTypeColor($type) {
    switch($type) {
        case 'Tour Stop': return 'primary';
        case 'Playoffs': return 'danger';
        case 'House History': return 'success';
        default: return 'secondary';
    }
}
?>
