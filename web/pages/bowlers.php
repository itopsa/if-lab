<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Get filter parameters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $dexterity = isset($_GET['dexterity']) ? $_GET['dexterity'] : '';
    $style = isset($_GET['style']) ? $_GET['style'] : '';
    $min_avg = isset($_GET['min_avg']) ? (float)$_GET['min_avg'] : '';
    $max_avg = isset($_GET['max_avg']) ? (float)$_GET['max_avg'] : '';
    
    // Build the query with filters using bowler_performance_summary view
    $query = "
        SELECT 
            bps.*,
            b.dexterity,
            b.style,
            b.uba_id,
            b.usbc_id
        FROM bowler_performance_summary bps
        JOIN bowlers b ON bps.bowler_id = b.bowler_id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($search) {
        $query .= " AND (bps.nickname LIKE ? OR b.uba_id LIKE ? OR b.usbc_id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($dexterity) {
        $query .= " AND b.dexterity = ?";
        $params[] = $dexterity;
    }
    
    if ($style) {
        $query .= " AND b.style = ?";
        $params[] = $style;
    }
    
    if ($min_avg !== '') {
        $query .= " AND bps.overall_average >= ?";
        $params[] = $min_avg;
    }
    
    if ($max_avg !== '') {
        $query .= " AND bps.overall_average <= ?";
        $params[] = $max_avg;
    }
    
    $query .= " ORDER BY bps.overall_average DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bowlers = $stmt->fetchAll();
    
    // Get unique values for filters
    $dexterity_options = $pdo->query("SELECT DISTINCT dexterity FROM bowlers WHERE dexterity IS NOT NULL ORDER BY dexterity")->fetchAll();
    $style_options = $pdo->query("SELECT DISTINCT style FROM bowlers WHERE style IS NOT NULL ORDER BY style")->fetchAll();
    
    // Calculate overall statistics using bowler_performance_summary view
    $stats_query = "
        SELECT 
            COUNT(*) as total_bowlers,
            AVG(overall_average) as avg_overall_average,
            MAX(overall_average) as highest_average,
            SUM(total_series) as total_series,
            SUM(series_800_plus) as total_800_plus,
            SUM(series_700_plus) as total_700_plus
        FROM bowler_performance_summary
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Bowlers</h2>
    <div class="text-muted"><?php echo count($bowlers); ?> bowlers found</div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Bowlers</h5>
                    <h3><?php echo number_format($stats['total_bowlers']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Avg Overall</h5>
                    <h3><?php echo number_format($stats['avg_overall_average'], 1); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Highest Avg</h5>
                    <h3><?php echo number_format($stats['highest_average'], 1); ?></h3>
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
            <form method="GET" action="?page=bowlers" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Name, UBA ID, USBC ID" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label for="dexterity" class="form-label">Dexterity</label>
                    <select name="dexterity" id="dexterity" class="form-select">
                        <option value="">All</option>
                        <?php foreach ($dexterity_options as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['dexterity']); ?>" 
                                    <?php echo ($dexterity === $option['dexterity']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option['dexterity']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="style" class="form-label">Style</label>
                    <select name="style" id="style" class="form-select">
                        <option value="">All</option>
                        <?php foreach ($style_options as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['style']); ?>" 
                                    <?php echo ($style === $option['style']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option['style']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="min_avg" class="form-label">Min Average</label>
                    <input type="number" name="min_avg" id="min_avg" class="form-control" 
                           step="0.1" min="0" max="300" value="<?php echo htmlspecialchars($min_avg); ?>">
                </div>
                <div class="col-md-2">
                    <label for="max_avg" class="form-label">Max Average</label>
                    <input type="number" name="max_avg" id="max_avg" class="form-control" 
                           step="0.1" min="0" max="300" value="<?php echo htmlspecialchars($max_avg); ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                </div>
            </form>
            <div class="mt-2">
                <a href="?page=bowlers" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
            </div>
        </div>
    </div>

    <!-- Bowlers Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="bowlersTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Bowler</th>
                            <th>Home House</th>
                            <th>Dexterity</th>
                            <th>Style</th>
                            <th>Series</th>
                            <th>Average</th>
                            <th>Best Series</th>
                            <th>800+</th>
                            <th>700+</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bowlers as $index => $bowler): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?php echo getRankColor($index + 1); ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bowler['nickname']); ?></strong>
                                    <?php if ($bowler['uba_id']): ?>
                                        <br><small class="text-muted">UBA: <?php echo htmlspecialchars($bowler['uba_id']); ?></small>
                                    <?php endif; ?>
                                    <?php if ($bowler['usbc_id']): ?>
                                        <br><small class="text-muted">USBC: <?php echo htmlspecialchars($bowler['usbc_id']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($bowler['home_house'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getDexterityColor($bowler['dexterity']); ?>">
                                        <?php echo htmlspecialchars($bowler['dexterity'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getStyleColor($bowler['style']); ?>">
                                        <?php echo htmlspecialchars($bowler['style'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo number_format($bowler['total_series']); ?></td>
                                <td class="text-center">
                                    <strong class="<?php echo getAverageColor($bowler['overall_average']); ?>">
                                        <?php echo number_format($bowler['overall_average'], 1); ?>
                                    </strong>
                                </td>
                                <td class="text-center"><?php echo number_format($bowler['highest_series']); ?></td>
                                <td class="text-center">
                                    <?php if ($bowler['series_800_plus'] > 0): ?>
                                        <span class="badge bg-danger"><?php echo $bowler['series_800_plus']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($bowler['series_700_plus'] > 0): ?>
                                        <span class="badge bg-warning"><?php echo $bowler['series_700_plus']; ?></span>
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
    $('#bowlersTable').DataTable({
        pageLength: 25,
        order: [[6, 'desc']], // Sort by average descending
        columnDefs: [
            { targets: [5,6,7,8,9], className: 'text-center' }
        ]
    });
});
</script>
