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
    
    // Build the query with filters
    $query = "
        SELECT 
            gs.series_id,
            b.nickname,
            l.name as location_name,
            gs.series_type,
            gs.event_date,
            gs.round_name,
            gs.game1_score,
            gs.game2_score,
            gs.game3_score,
            gs.total_score,
            gs.average_score,
            gs.created_at
        FROM game_series gs
        JOIN bowlers b ON gs.bowler_id = b.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($bowler_filter) {
        $query .= " AND b.nickname LIKE ?";
        $params[] = "%$bowler_filter%";
    }
    
    if ($location_filter) {
        $query .= " AND l.name LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    if ($series_type_filter) {
        $query .= " AND gs.series_type = ?";
        $params[] = $series_type_filter;
    }
    
    if ($date_from) {
        $query .= " AND gs.event_date >= ?";
        $params[] = $date_from;
    }
    
    if ($date_to) {
        $query .= " AND gs.event_date <= ?";
        $params[] = $date_to;
    }
    
    $query .= " ORDER BY gs.event_date DESC, gs.series_id DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $series = $stmt->fetchAll();
    
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
    <h2>Series Details</h2>
    <div class="text-muted"><?php echo count($series); ?> series found</div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="?page=series" class="row g-3">
            <div class="col-md-2">
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
            <div class="col-md-2">
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
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php elseif (empty($series)): ?>
    <div class="alert alert-info">No series found matching your criteria.</div>
<?php else: ?>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $s): ?>
                            <tr>
                                <td><?php echo formatDate($s['event_date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($s['nickname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($s['location_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($s['series_type']); ?>">
                                        <?php echo htmlspecialchars($s['series_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s['round_name']); ?></td>
                                <td class="text-center"><?php echo formatScore($s['game1_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($s['game2_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($s['game3_score']); ?></td>
                                <td class="text-center"><strong><?php echo formatScore($s['total_score']); ?></strong></td>
                                <td class="text-center"><?php echo formatAverage($s['average_score']); ?></td>
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

function getSeriesTypeColor(type) {
    switch(type) {
        case 'Tour Stop': return 'primary';
        case 'Playoffs': return 'danger';
        case 'House History': return 'success';
        default: return 'secondary';
    }
}
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
