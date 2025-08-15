<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    
    // Build the query for tournament data (Tour Stops and Playoffs)
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
            gs.average_score
        FROM game_series gs
        JOIN bowlers b ON gs.bowler_id = b.bowler_id
        LEFT JOIN locations l ON gs.location_id = l.location_id
        WHERE gs.series_type IN ('Tour Stop', 'Playoffs')
    ";
    
    $params = [];
    
    if ($location_filter) {
        $query .= " AND l.name LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    if ($date_from) {
        $query .= " AND gs.event_date >= ?";
        $params[] = $date_from;
    }
    
    if ($date_to) {
        $query .= " AND gs.event_date <= ?";
        $params[] = $date_to;
    }
    
    $query .= " ORDER BY gs.event_date DESC, gs.series_type, gs.round_name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tournaments = $stmt->fetchAll();
    
    // Get unique values for filters
    $locations_stmt = $pdo->query("SELECT DISTINCT name FROM locations ORDER BY name");
    $locations = $locations_stmt->fetchAll();
    
    // Calculate tournament statistics
    $total_tournaments = count(array_filter($tournaments, function($t) { return $t['series_type'] == 'Tour Stop'; }));
    $total_playoffs = count(array_filter($tournaments, function($t) { return $t['series_type'] == 'Playoffs'; }));
    $highest_total = max(array_column($tournaments, 'total_score'));
    $highest_avg = max(array_column($tournaments, 'average_score'));
    
    // Group by tournament events
    $tournament_events = [];
    foreach ($tournaments as $tournament) {
        $event_key = $tournament['location_name'] . ' - ' . $tournament['event_date'];
        if (!isset($tournament_events[$event_key])) {
            $tournament_events[$event_key] = [
                'location' => $tournament['location_name'],
                'date' => $tournament['event_date'],
                'rounds' => []
            ];
        }
        $tournament_events[$event_key]['rounds'][] = $tournament;
    }
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tournament Performance</h2>
    <div class="text-muted"><?php echo count($tournaments); ?> tournament entries</div>
</div>

<!-- Tournament Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Tour Stops</h5>
                <h3 class="text-primary"><?php echo $total_tournaments; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Playoff Rounds</h5>
                <h3 class="text-danger"><?php echo $total_playoffs; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Highest Total</h5>
                <h3 class="text-success"><?php echo $highest_total; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Highest Average</h5>
                <h3 class="text-info"><?php echo number_format($highest_avg, 1); ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="?page=tournaments" class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="?page=tournaments" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php elseif (empty($tournaments)): ?>
    <div class="alert alert-info">No tournament data found matching your criteria.</div>
<?php else: ?>
    <!-- Tournament Events -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tournament Events</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="tournamentAccordion">
                <?php $event_count = 0; ?>
                <?php foreach ($tournament_events as $event_key => $event): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $event_count; ?>">
                            <button class="accordion-button <?php echo $event_count > 0 ? 'collapsed' : ''; ?>" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?php echo $event_count; ?>">
                                <strong><?php echo htmlspecialchars($event['location']); ?></strong>
                                <span class="ms-3 text-muted"><?php echo formatDate($event['date']); ?></span>
                                <span class="ms-3 badge bg-primary"><?php echo count($event['rounds']); ?> rounds</span>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $event_count; ?>" 
                             class="accordion-collapse collapse <?php echo $event_count == 0 ? 'show' : ''; ?>" 
                             data-bs-parent="#tournamentAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Round</th>
                                                <th>Bowler</th>
                                                <th>Game 1</th>
                                                <th>Game 2</th>
                                                <th>Game 3</th>
                                                <th>Total</th>
                                                <th>Average</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($event['rounds'] as $round): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?php echo getSeriesTypeColor($round['series_type']); ?>">
                                                            <?php echo htmlspecialchars($round['round_name']); ?>
                                                        </span>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($round['nickname']); ?></strong></td>
                                                    <td class="text-center"><?php echo formatScore($round['game1_score']); ?></td>
                                                    <td class="text-center"><?php echo formatScore($round['game2_score']); ?></td>
                                                    <td class="text-center"><?php echo formatScore($round['game3_score']); ?></td>
                                                    <td class="text-center"><strong><?php echo formatScore($round['total_score']); ?></strong></td>
                                                    <td class="text-center"><?php echo formatAverage($round['average_score']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $event_count++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tournament Performance Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Tournament Entries</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tournamentTable">
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
                        <?php foreach ($tournaments as $tournament): ?>
                            <tr>
                                <td><?php echo formatDate($tournament['event_date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($tournament['nickname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($tournament['location_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($tournament['series_type']); ?>">
                                        <?php echo htmlspecialchars($tournament['series_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($tournament['round_name']); ?></td>
                                <td class="text-center"><?php echo formatScore($tournament['game1_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($tournament['game2_score']); ?></td>
                                <td class="text-center"><?php echo formatScore($tournament['game3_score']); ?></td>
                                <td class="text-center"><strong><?php echo formatScore($tournament['total_score']); ?></strong></td>
                                <td class="text-center"><?php echo formatAverage($tournament['average_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Tournament Leaders -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tournament Leaders - Highest Totals</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php 
                        $top_totals = array_slice($tournaments, 0, 10);
                        usort($top_totals, function($a, $b) { return $b['total_score'] - $a['total_score']; });
                        foreach (array_slice($top_totals, 0, 5) as $tournament): 
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($tournament['nickname']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo formatDate($tournament['event_date']); ?> - 
                                        <?php echo htmlspecialchars($tournament['location_name'] ?? 'N/A'); ?>
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $tournament['total_score']; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tournament Leaders - Highest Averages</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php 
                        usort($tournaments, function($a, $b) { return $b['average_score'] - $a['average_score']; });
                        foreach (array_slice($tournaments, 0, 5) as $tournament): 
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($tournament['nickname']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo formatDate($tournament['event_date']); ?> - 
                                        <?php echo htmlspecialchars($tournament['location_name'] ?? 'N/A'); ?>
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    <?php echo number_format($tournament['average_score'], 1); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#tournamentTable').DataTable({
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
?>
