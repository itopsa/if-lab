<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
    $score_min = isset($_GET['score_min']) ? (int)$_GET['score_min'] : '';
    $score_max = isset($_GET['score_max']) ? (int)$_GET['score_max'] : '';
    $game_number = isset($_GET['game_number']) ? (int)$_GET['game_number'] : '';
    
    // Build the query with filters
    $query = "
        SELECT 
            g.game_id,
            g.series_id,
            g.game_number,
            g.score,
            b.nickname,
            l.name as location_name,
            gs.series_type,
            gs.event_date,
            gs.round_name,
            gs.total_score,
            gs.average_score
        FROM games g
        JOIN game_series gs ON g.series_id = gs.series_id
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
    
    if ($score_min !== '') {
        $query .= " AND g.score >= ?";
        $params[] = $score_min;
    }
    
    if ($score_max !== '') {
        $query .= " AND g.score <= ?";
        $params[] = $score_max;
    }
    
    if ($game_number) {
        $query .= " AND g.game_number = ?";
        $params[] = $game_number;
    }
    
    $query .= " ORDER BY g.score DESC, gs.event_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $games = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT nickname FROM bowlers ORDER BY nickname");
    $bowlers = $bowlers_stmt->fetchAll();
    
    $locations_stmt = $pdo->query("SELECT DISTINCT name FROM locations ORDER BY name");
    $locations = $locations_stmt->fetchAll();
    
    // Calculate statistics
    $total_games = count($games);
    $perfect_games = count(array_filter($games, function($g) { return $g['score'] == 300; }));
    $high_games = count(array_filter($games, function($g) { return $g['score'] >= 250; }));
    $avg_score = $total_games > 0 ? array_sum(array_column($games, 'score')) / $total_games : 0;
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Game Details</h2>
    <div class="text-muted"><?php echo count($games); ?> games found</div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Games</h5>
                <h3 class="text-primary"><?php echo $total_games; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Perfect Games</h5>
                <h3 class="text-warning"><?php echo $perfect_games; ?></h3>
                <p class="text-muted">300 Games</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">High Games</h5>
                <h3 class="text-success"><?php echo $high_games; ?></h3>
                <p class="text-muted">250+ Games</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Average Score</h5>
                <h3 class="text-info"><?php echo number_format($avg_score, 1); ?></h3>
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
        <form method="GET" action="?page=games" class="row g-3">
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
                <label for="game_number" class="form-label">Game #</label>
                <select name="game_number" id="game_number" class="form-select">
                    <option value="">All Games</option>
                    <option value="1" <?php echo ($game_number == 1) ? 'selected' : ''; ?>>Game 1</option>
                    <option value="2" <?php echo ($game_number == 2) ? 'selected' : ''; ?>>Game 2</option>
                    <option value="3" <?php echo ($game_number == 3) ? 'selected' : ''; ?>>Game 3</option>
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="?page=games" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php elseif (empty($games)): ?>
    <div class="alert alert-info">No games found matching your criteria.</div>
<?php else: ?>
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
                            <th>Round</th>
                            <th>Game #</th>
                            <th>Score</th>
                            <th>Series Total</th>
                            <th>Series Avg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td><?php echo formatDate($game['event_date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($game['nickname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($game['location_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($game['series_type']); ?>">
                                        <?php echo htmlspecialchars($game['series_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($game['round_name']); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo $game['game_number']; ?></span>
                                </td>
                                <td class="text-center">
                                    <strong class="<?php echo getScoreClass($game['score']); ?>">
                                        <?php echo formatScore($game['score']); ?>
                                    </strong>
                                </td>
                                <td class="text-center"><?php echo formatScore($game['total_score']); ?></td>
                                <td class="text-center"><?php echo formatAverage($game['average_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Score Distribution Chart -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Score Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="scoreDistribution"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Performances</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php 
                        $top_games = array_slice($games, 0, 10);
                        foreach ($top_games as $game): 
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($game['nickname']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo formatDate($game['event_date']); ?> - 
                                        <?php echo htmlspecialchars($game['location_name'] ?? 'N/A'); ?>
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $game['score']; ?>
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
    $('#gamesTable').DataTable({
        pageLength: 25,
        order: [[6, 'desc']],
        columnDefs: [
            { targets: [5,6,7,8], className: 'text-center' }
        ]
    });
    
    // Score distribution chart
    if (typeof Chart !== 'undefined' && document.getElementById('scoreDistribution')) {
        const ctx = document.getElementById('scoreDistribution').getContext('2d');
        
        // Group scores into ranges
        const scoreRanges = {
            '300': 0,
            '250-299': 0,
            '200-249': 0,
            '150-199': 0,
            '100-149': 0,
            '0-99': 0
        };
        
        <?php foreach ($games as $game): ?>
            const score = <?php echo $game['score']; ?>;
            if (score === 300) scoreRanges['300']++;
            else if (score >= 250) scoreRanges['250-299']++;
            else if (score >= 200) scoreRanges['200-249']++;
            else if (score >= 150) scoreRanges['150-199']++;
            else if (score >= 100) scoreRanges['100-149']++;
            else scoreRanges['0-99']++;
        <?php endforeach; ?>
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(scoreRanges),
                datasets: [{
                    label: 'Number of Games',
                    data: Object.values(scoreRanges),
                    backgroundColor: [
                        '#ffc107', // 300 - warning
                        '#dc3545', // 250-299 - danger
                        '#fd7e14', // 200-249 - orange
                        '#20c997', // 150-199 - teal
                        '#6f42c1', // 100-149 - purple
                        '#6c757d'  // 0-99 - secondary
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});

function getScoreClass(score) {
    if (score === 300) return 'text-warning';
    if (score >= 250) return 'text-danger';
    if (score >= 200) return 'text-success';
    if (score >= 150) return 'text-info';
    return 'text-muted';
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

function getScoreClass($score) {
    if ($score === 300) return 'text-warning';
    if ($score >= 250) return 'text-danger';
    if ($score >= 200) return 'text-success';
    if ($score >= 150) return 'text-info';
    return 'text-muted';
}
?>
