<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get overall statistics using direct queries
    $stats_query = "
        SELECT 
            COUNT(DISTINCT b.bowler_id) as total_bowlers,
            COUNT(DISTINCT l.location_id) as total_locations,
            COUNT(gs.series_id) as total_series,
            AVG(gs.average_score) as overall_average,
            MAX(gs.total_score) as highest_series,
            COUNT(CASE WHEN gs.total_score >= 800 THEN 1 END) as series_800_plus,
            COUNT(CASE WHEN gs.total_score >= 700 THEN 1 END) as series_700_plus
        FROM bowlers b
        LEFT JOIN game_series gs ON b.bowler_id = gs.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
    ";
    $stats = $pdo->query($stats_query)->fetch();
    

    
    // Get top performers using bowler_performance_summary view
    $top_performers_query = "
        SELECT 
            nickname,
            total_series,
            overall_average,
            highest_series,
            series_800_plus
        FROM bowler_performance_summary
        ORDER BY overall_average DESC
        LIMIT 5
    ";
    $top_performers = $pdo->query($top_performers_query)->fetchAll();
    
    // Get series type distribution
    $series_types_query = "
        SELECT 
            series_type,
            COUNT(*) as count
        FROM game_series
        GROUP BY series_type
        ORDER BY count DESC
    ";
    $series_types = $pdo->query($series_types_query)->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    <div class="text-muted">Bowling Database Overview</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['total_bowlers']); ?></h3>
                    <p class="mb-0">Total Bowlers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['total_locations']); ?></h3>
                    <p class="mb-0">Locations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <i class="fas fa-list fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['total_series']); ?></h3>
                    <p class="mb-0">Total Series</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['overall_average'], 1); ?></h3>
                    <p class="mb-0">Overall Average</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Highlights -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Highest Series</h5>
                    <h2 class="text-success"><?php echo number_format($stats['highest_series']); ?></h2>
                    <p class="text-muted">Best Series Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">800+ Series</h5>
                    <h2 class="text-warning"><?php echo number_format($stats['series_800_plus']); ?></h2>
                    <p class="text-muted">Elite Performances</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">700+ Series</h5>
                    <h2 class="text-info"><?php echo number_format($stats['series_700_plus']); ?></h2>
                    <p class="text-muted">Excellent Performances</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Data -->
    <div class="row">
        <!-- Top Performers -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Performers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bowler</th>
                                    <th>Series</th>
                                    <th>Average</th>
                                    <th>Best</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_performers as $bowler): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($bowler['nickname']); ?></strong></td>
                                        <td><?php echo number_format($bowler['total_series']); ?></td>
                                        <td><span class="badge bg-success"><?php echo number_format($bowler['overall_average'], 1); ?></span></td>
                                        <td><?php echo number_format($bowler['highest_series']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Series Type Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Series Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="seriesTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>


<?php endif; ?>

<script>
$(document).ready(function() {
    // Series Type Chart
    const ctx = document.getElementById('seriesTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($series_types, 'series_type')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($series_types, 'count')); ?>,
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe',
                    '#00f2fe'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
