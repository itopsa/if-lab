<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $series_type_filter = isset($_GET['series_type']) ? $_GET['series_type'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $min_total = isset($_GET['min_total']) ? (int)$_GET['min_total'] : '';
    $max_total = isset($_GET['max_total']) ? (int)$_GET['max_total'] : '';
    
    // Build the query with filters
    $query = "
        SELECT * FROM series_details
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
    
    if ($date_from) {
        $query .= " AND event_date >= ?";
        $params[] = $date_from;
    }
    
    if ($date_to) {
        $query .= " AND event_date <= ?";
        $params[] = $date_to;
    }
    
    if ($min_total !== '') {
        $query .= " AND total_score >= ?";
        $params[] = $min_total;
    }
    
    if ($max_total !== '') {
        $query .= " AND total_score <= ?";
        $params[] = $max_total;
    }
    
    $query .= " ORDER BY event_date DESC, series_id DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $series = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT bowler_name FROM series_details ORDER BY bowler_name")->fetchAll();
    $locations_stmt = $pdo->query("SELECT DISTINCT location_name FROM series_details WHERE location_name IS NOT NULL ORDER BY location_name")->fetchAll();
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM series_details ORDER BY series_type")->fetchAll();
    
    // Calculate statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_series,
            AVG(average_score) as avg_average,
            MAX(total_score) as highest_total,
            MIN(total_score) as lowest_total,
            COUNT(CASE WHEN total_score >= 800 THEN 1 END) as series_800_plus,
            COUNT(CASE WHEN total_score >= 700 THEN 1 END) as series_700_plus,
            COUNT(CASE WHEN total_score >= 600 THEN 1 END) as series_600_plus
        FROM series_details
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list me-2"></i>Series Details</h2>
    <div class="text-muted"><?php echo count($series); ?> series found</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Series</h5>
                    <h3><?php echo number_format($stats['total_series']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Avg Average</h5>
                    <h3><?php echo number_format($stats['avg_average'], 1); ?></h3>
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
                    <h5 class="card-title text-info">700+ Series</h5>
                    <h3><?php echo number_format($stats['series_700_plus']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-secondary">600+ Series</h5>
                    <h3><?php echo number_format($stats['series_600_plus']); ?></h3>
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
            <form method="GET" action="?page=series" class="row g-3">
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
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="?page=series" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
            
            <!-- Additional Filters -->
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="min_total" class="form-label">Min Total Score</label>
                    <input type="number" name="min_total" id="min_total" class="form-control" 
                           min="0" max="900" value="<?php echo htmlspecialchars($min_total); ?>">
                </div>
                <div class="col-md-3">
                    <label for="max_total" class="form-label">Max Total Score</label>
                    <input type="number" name="max_total" id="max_total" class="form-control" 
                           min="0" max="900" value="<?php echo htmlspecialchars($max_total); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Series Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="seriesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bowler</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Round</th>
                            <th>Game 1</th>
                            <th>Game 2</th>
                            <th>Game 3</th>
                            <th>Total</th>
                            <th>Average</th>
                            <th>Style</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $s): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($s['event_date'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($s['bowler_name']); ?></strong>
                                    <?php if ($s['uba_id']): ?>
                                        <br><small class="text-muted">UBA: <?php echo htmlspecialchars($s['uba_id']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($s['location_name'] ?? 'N/A'); ?>
                                    <?php if ($s['city'] && $s['state']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($s['city'] . ', ' . $s['state']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($s['series_type']); ?>">
                                        <?php echo htmlspecialchars($s['series_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s['round_name']); ?></td>
                                <td class="text-center <?php echo getScoreClass($s['game1_score']); ?>">
                                    <strong><?php echo number_format($s['game1_score']); ?></strong>
                                </td>
                                <td class="text-center <?php echo getScoreClass($s['game2_score']); ?>">
                                    <strong><?php echo number_format($s['game2_score']); ?></strong>
                                </td>
                                <td class="text-center <?php echo getScoreClass($s['game3_score']); ?>">
                                    <strong><?php echo number_format($s['game3_score']); ?></strong>
                                </td>
                                <td class="text-center">
                                    <strong class="<?php echo getTotalScoreClass($s['total_score']); ?>">
                                        <?php echo number_format($s['total_score']); ?>
                                    </strong>
                                </td>
                                <td class="text-center"><?php echo number_format($s['average_score'], 1); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getDexterityColor($s['dexterity']); ?>">
                                        <?php echo htmlspecialchars($s['dexterity'] ?? 'N/A'); ?>
                                    </span>
                                    <br>
                                    <span class="badge bg-<?php echo getStyleColor($s['style']); ?>">
                                        <?php echo htmlspecialchars($s['style'] ?? 'N/A'); ?>
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
    $('#seriesTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { targets: [5,6,7,8,9], className: 'text-center' }
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

function getScoreClass($score) {
    if ($score >= 300) return 'text-warning';
    if ($score >= 250) return 'text-danger';
    if ($score >= 200) return 'text-success';
    if ($score >= 150) return 'text-info';
    return 'text-muted';
}

function getTotalScoreClass($total) {
    if ($total >= 800) return 'text-warning';
    if ($total >= 700) return 'text-danger';
    if ($total >= 600) return 'text-success';
    if ($total >= 500) return 'text-info';
    return 'text-muted';
}

function getDexterityColor($dexterity) {
    switch($dexterity) {
        case 'Right': return 'primary';
        case 'Left': return 'success';
        case 'Ambidextrous': return 'warning';
        default: return 'secondary';
    }
}

function getStyleColor($style) {
    switch($style) {
        case '1 Handed': return 'info';
        case '2 Handed': return 'danger';
        default: return 'secondary';
    }
}
?>
