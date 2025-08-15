<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get basic location information with optional statistics
    $locations_query = "
        SELECT 
            l.location_id,
            l.name AS location_name,
            l.city,
            l.state,
            COUNT(DISTINCT gs.bowler_id) AS unique_bowlers,
            COUNT(gs.series_id) AS total_series,
            AVG(gs.average_score) AS avg_series_score,
            MAX(gs.total_score) AS highest_series,
            MIN(gs.total_score) AS lowest_series,
            COUNT(CASE WHEN gs.total_score >= 700 THEN 1 END) AS series_700_plus,
            COUNT(CASE WHEN gs.total_score >= 800 THEN 1 END) AS series_800_plus
        FROM locations l
        LEFT JOIN game_series gs ON l.location_id = gs.location_id
        GROUP BY l.location_id, l.name, l.city, l.state
        ORDER BY total_series DESC, avg_series_score DESC
    ";
    $locations = $pdo->query($locations_query)->fetchAll();
    
    // Calculate overall statistics
    $stats_query = "
        SELECT 
            COUNT(DISTINCT l.location_id) as total_locations,
            AVG(gs.average_score) as overall_avg_score,
            COUNT(gs.series_id) as total_series,
            COUNT(DISTINCT gs.bowler_id) as total_bowlers,
            COUNT(CASE WHEN gs.total_score >= 800 THEN 1 END) as total_800_plus,
            COUNT(CASE WHEN gs.total_score >= 700 THEN 1 END) as total_700_plus
        FROM locations l
        LEFT JOIN game_series gs ON l.location_id = gs.location_id
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
    // Debug: Check if there's any data
    $debug_locations = $pdo->query("SELECT COUNT(*) as count FROM locations")->fetch();
    $debug_series = $pdo->query("SELECT COUNT(*) as count FROM game_series")->fetch();
    $debug_bowlers = $pdo->query("SELECT COUNT(*) as count FROM bowlers")->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-map-marker-alt me-2"></i>Locations</h2>
    <div class="text-muted"><?php echo count($locations); ?> locations found</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Debug Information -->
    <div class="alert alert-info">
        <strong>Debug Info:</strong> 
        Locations: <?php echo $debug_locations['count']; ?> | 
        Series: <?php echo $debug_series['count']; ?> | 
        Bowlers: <?php echo $debug_bowlers['count']; ?>
    </div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Locations</h5>
                    <h3><?php echo number_format($stats['total_locations']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Overall Avg Score</h5>
                    <h3><?php echo number_format($stats['overall_avg_score'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">Total Series</h5>
                    <h3><?php echo number_format($stats['total_series']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Total Bowlers</h5>
                    <h3><?php echo number_format($stats['total_bowlers']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">800+ Series</h5>
                    <h3><?php echo number_format($stats['total_800_plus']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-secondary">700+ Series</h5>
                    <h3><?php echo number_format($stats['total_700_plus']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="locationsTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Location</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Unique Bowlers</th>
                            <th>Total Series</th>
                            <th>Avg Series Score</th>
                            <th>800+ Series</th>
                            <th>700+ Series</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $index => $location): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?php echo getRankColor($index + 1); ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($location['location_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($location['city']); ?></td>
                                <td><?php echo htmlspecialchars($location['state']); ?></td>
                                <td class="text-center"><?php echo number_format($location['unique_bowlers']); ?></td>
                                <td class="text-center"><?php echo number_format($location['total_series']); ?></td>
                                <td class="text-center">
                                    <strong class="<?php echo getAverageColor($location['avg_series_score']); ?>">
                                        <?php echo number_format($location['avg_series_score'], 1); ?>
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($location['series_800_plus'] > 0): ?>
                                        <span class="badge bg-danger"><?php echo $location['series_800_plus']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($location['series_700_plus'] > 0): ?>
                                        <span class="badge bg-warning"><?php echo $location['series_700_plus']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
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
    $('#locationsTable').DataTable({
        pageLength: 25,
        order: [[6, 'desc']], // Sort by average series score descending
        columnDefs: [
            { targets: [4,5,6,7,8], className: 'text-center' }
        ]
    });
});
</script>
