<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $series_type_filter = isset($_GET['series_type']) ? $_GET['series_type'] : '';
    $limit_filter = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // Build the query with filters using recent_performance view
    $query = "
        SELECT * FROM recent_performance
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
    
    $query .= " ORDER BY nickname, event_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $recent_data = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT nickname FROM recent_performance ORDER BY nickname")->fetchAll();
    $locations_stmt = $pdo->query("SELECT DISTINCT location FROM recent_performance WHERE location IS NOT NULL ORDER BY location")->fetchAll();
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM recent_performance ORDER BY series_type")->fetchAll();
    
    // Calculate statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_records,
            AVG(total_score) as avg_total_score,
            AVG(average_score) as avg_average_score,
            MAX(total_score) as highest_total,
            COUNT(CASE WHEN total_score >= 800 THEN 1 END) as series_800_plus,
            COUNT(CASE WHEN total_score >= 700 THEN 1 END) as series_700_plus
        FROM recent_performance
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clock me-2"></i>Recent Performance</h2>
    <div class="text-muted"><?php echo count($recent_data); ?> recent series found</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Records</h5>
                    <h3><?php echo number_format($stats['total_records']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Avg Total Score</h5>
                    <h3><?php echo number_format($stats['avg_total_score'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">Avg Average</h5>
                    <h3><?php echo number_format($stats['avg_average_score'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Highest Total</h5>
                    <h3><?php echo number_format($stats['highest_total']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">800+ Series</h5>
                    <h3><?php echo number_format($stats['series_800_plus']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-secondary">700+ Series</h5>
                    <h3><?php echo number_format($stats['series_700_plus']); ?></h3>
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
                <input type="hidden" name="page" value="recent">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-select">
                        <option value="">All Locations</option>
                        <?php foreach ($locations_stmt as $location): ?>
                            <option value="<?php echo htmlspecialchars($location['location']); ?>" 
                                    <?php echo ($location_filter === $location['location']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location['location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="?page=recent" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Performance Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="recentTable">
                    <thead>
                        <tr>
                            <th>Bowler</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Total Score</th>
                            <th>Average</th>
                            <th>Game 1</th>
                            <th>Game 2</th>
                            <th>Game 3</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_data as $record): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($record['nickname']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($record['location'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($record['series_type']); ?>">
                                        <?php echo htmlspecialchars($record['series_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($record['event_date'])); ?></td>
                                <td class="text-center">
                                    <strong class="<?php echo getTotalScoreClass($record['total_score']); ?>">
                                        <?php echo number_format($record['total_score']); ?>
                                    </strong>
                                </td>
                                <td class="text-center"><?php echo number_format($record['average_score'], 1); ?></td>
                                <td class="text-center <?php echo getScoreClass($record['game1_score']); ?>">
                                    <strong><?php echo number_format($record['game1_score']); ?></strong>
                                </td>
                                <td class="text-center <?php echo getScoreClass($record['game2_score']); ?>">
                                    <strong><?php echo number_format($record['game2_score']); ?></strong>
                                </td>
                                <td class="text-center <?php echo getScoreClass($record['game3_score']); ?>">
                                    <strong><?php echo number_format($record['game3_score']); ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo getRankColor($record['series_rank']); ?>">
                                        #<?php echo $record['series_rank']; ?>
                                    </span>
                                </td>
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
    $('#recentTable').DataTable({
        pageLength: 25,
        order: [[3, 'desc']], // Sort by date descending
        columnDefs: [
            { targets: [4,5,6,7,8,9], className: 'text-center' }
        ]
    });
});
</script>
