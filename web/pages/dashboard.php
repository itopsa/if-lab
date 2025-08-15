<?php
// Get dashboard statistics
$stats = [];

// Total bowlers
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bowlers");
$stats['total_bowlers'] = $stmt->fetch()['count'];

// Total locations
$stmt = $pdo->query("SELECT COUNT(*) as count FROM locations");
$stats['total_locations'] = $stmt->fetch()['count'];

// Total series
$stmt = $pdo->query("SELECT COUNT(*) as count FROM game_series");
$stats['total_series'] = $stmt->fetch()['count'];

// Average series score
$stmt = $pdo->query("SELECT AVG(average_score) as avg FROM game_series");
$stats['avg_series_score'] = round($stmt->fetch()['avg'], 2);

// Recent series (last 5)
$stmt = $pdo->query("
    SELECT b.nickname, l.name as location, gs.total_score, gs.average_score, gs.event_date
    FROM game_series gs
    JOIN bowlers b ON gs.bowler_id = b.bowler_id
    LEFT JOIN locations l ON gs.location_id = l.location_id
    ORDER BY gs.event_date DESC
    LIMIT 5
");
$recent_series = $stmt->fetchAll();

// Top performers
$stmt = $pdo->query("
    SELECT nickname, overall_average, total_series
    FROM bowler_performance_summary
    ORDER BY overall_average DESC
    LIMIT 5
");
$top_performers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    <div class="text-muted">Last updated: <?php echo date('M j, Y g:i A'); ?></div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h3><?php echo $stats['total_bowlers']; ?></h3>
                <p class="mb-0">Total Bowlers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                <h3><?php echo $stats['total_locations']; ?></h3>
                <p class="mb-0">Locations</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-list fa-2x mb-2"></i>
                <h3><?php echo $stats['total_series']; ?></h3>
                <p class="mb-0">Total Series</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h3><?php echo $stats['avg_series_score']; ?></h3>
                <p class="mb-0">Avg Series Score</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Series -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Series</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bowler</th>
                                <th>Location</th>
                                <th>Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_series as $series): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($series['nickname']); ?></td>
                                <td><?php echo htmlspecialchars($series['location'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $series['total_score']; ?></span>
                                    <small class="text-muted">(<?php echo $series['average_score']; ?> avg)</small>
                                </td>
                                <td><?php echo date('M j', strtotime($series['event_date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Performers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bowler</th>
                                <th>Average</th>
                                <th>Series</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_performers as $index => $bowler): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-warning me-2">#<?php echo $index + 1; ?></span>
                                        <?php echo htmlspecialchars($bowler['nickname']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo $bowler['overall_average']; ?></span>
                                </td>
                                <td><?php echo $bowler['total_series']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="?page=bowlers" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-users me-2"></i>View All Bowlers
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="?page=series" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-list me-2"></i>Series Details
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="?page=recent" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-clock me-2"></i>Recent Performance
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="?page=tournaments" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-trophy me-2"></i>Tournament Stats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
