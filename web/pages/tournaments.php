<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $bowler_filter = isset($_GET['bowler']) ? $_GET['bowler'] : '';
    $series_type_filter = isset($_GET['series_type']) ? $_GET['series_type'] : '';
    
    // Build the query with filters using tournament_performance view
    $query = "
        SELECT * FROM tournament_performance
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($bowler_filter) {
        $query .= " AND nickname LIKE ?";
        $params[] = "%$bowler_filter%";
    }
    
    if ($series_type_filter) {
        $query .= " AND series_type = ?";
        $params[] = $series_type_filter;
    }
    
    $query .= " ORDER BY nickname, series_type";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tournaments = $stmt->fetchAll();
    
    // Get unique values for filters
    $bowlers_stmt = $pdo->query("SELECT DISTINCT nickname FROM tournament_performance ORDER BY nickname")->fetchAll();
    $series_types_stmt = $pdo->query("SELECT DISTINCT series_type FROM tournament_performance ORDER BY series_type")->fetchAll();
    
    // Calculate statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_records,
            SUM(tournaments_played) as total_tournaments,
            AVG(avg_tournament_score) as overall_avg_score,
            MAX(best_tournament_series) as highest_series,
            SUM(series_800_plus) as total_800_plus,
            SUM(series_700_plus) as total_700_plus
        FROM tournament_performance
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-trophy me-2"></i>Tournaments</h2>
    <div class="text-muted"><?php echo count($tournaments); ?> tournament records found</div>
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
                    <h5 class="card-title text-success">Total Tournaments</h5>
                    <h3><?php echo number_format($stats['total_tournaments']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">Avg Tournament Score</h5>
                    <h3><?php echo number_format($stats['overall_avg_score'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Highest Series</h5>
                    <h3><?php echo number_format($stats['highest_series']); ?></h3>
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="?page=tournaments" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="?page=tournaments" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tournaments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tournamentsTable">
                    <thead>
                        <tr>
                            <th>Bowler</th>
                            <th>Series Type</th>
                            <th>Tournaments Played</th>
                            <th>Avg Tournament Score</th>
                            <th>Best Tournament Series</th>
                            <th>Worst Tournament Series</th>
                            <th>800+ Series</th>
                            <th>700+ Series</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournaments as $tournament): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($tournament['nickname']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getSeriesTypeColor($tournament['series_type']); ?>">
                                        <?php echo htmlspecialchars($tournament['series_type']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo number_format($tournament['tournaments_played']); ?></span>
                                </td>
                                <td class="text-center">
                                    <strong class="<?php echo getAverageColor($tournament['avg_tournament_score']); ?>">
                                        <?php echo number_format($tournament['avg_tournament_score'], 1); ?>
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <strong class="<?php echo getTotalScoreClass($tournament['best_tournament_series']); ?>">
                                        <?php echo number_format($tournament['best_tournament_series']); ?>
                                    </strong>
                                </td>
                                <td class="text-center"><?php echo number_format($tournament['worst_tournament_series']); ?></td>
                                <td class="text-center">
                                    <?php if ($tournament['series_800_plus'] > 0): ?>
                                        <span class="badge bg-danger"><?php echo $tournament['series_800_plus']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($tournament['series_700_plus'] > 0): ?>
                                        <span class="badge bg-warning"><?php echo $tournament['series_700_plus']; ?></span>
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
    $('#tournamentsTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc'], [1, 'asc']], // Sort by bowler name, then series type
        columnDefs: [
            { targets: [2,3,4,5,6,7], className: 'text-center' }
        ]
    });
});
</script>
