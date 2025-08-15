<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get location statistics using location_statistics view
    $locations_query = "
        SELECT * FROM location_statistics
        ORDER BY avg_series_score DESC
    ";
    $locations = $pdo->query($locations_query)->fetchAll();
    
    // Calculate overall statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_locations,
            AVG(avg_series_score) as overall_avg_score,
            SUM(total_series) as total_series,
            SUM(unique_bowlers) as total_bowlers,
            SUM(series_800_plus) as total_800_plus,
            SUM(series_700_plus) as total_700_plus
        FROM location_statistics
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
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
                                    <strong><?php echo htmlspecialchars($location['name']); ?></strong>
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
