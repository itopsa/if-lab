<?php
// Handle search and filtering
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'overall_average';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Build query
$query = "SELECT * FROM bowler_performance_summary";
$params = [];

if (!empty($search)) {
    $query .= " WHERE nickname LIKE ? OR uba_id LIKE ? OR usbc_id LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
}

$query .= " ORDER BY $sort $order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bowlers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Bowlers</h2>
    <div class="text-muted"><?php echo count($bowlers); ?> bowlers found</div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="bowlers">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by name, UBA ID, or USBC ID..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sort">
                    <option value="overall_average" <?php echo $sort == 'overall_average' ? 'selected' : ''; ?>>Sort by Average</option>
                    <option value="total_series" <?php echo $sort == 'total_series' ? 'selected' : ''; ?>>Sort by Series Count</option>
                    <option value="highest_series" <?php echo $sort == 'highest_series' ? 'selected' : ''; ?>>Sort by Best Series</option>
                    <option value="nickname" <?php echo $sort == 'nickname' ? 'selected' : ''; ?>>Sort by Name</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="order">
                    <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bowlers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Bowler</th>
                        <th>IDs</th>
                        <th>Style</th>
                        <th>Home House</th>
                        <th>Average</th>
                        <th>Series</th>
                        <th>Best Series</th>
                        <th>Milestones</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bowlers as $bowler): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($bowler['nickname']); ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?php echo $bowler['bowler_id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($bowler['uba_id']): ?>
                                <span class="badge bg-info me-1">UBA: <?php echo htmlspecialchars($bowler['uba_id']); ?></span>
                            <?php endif; ?>
                            <?php if($bowler['usbc_id']): ?>
                                <span class="badge bg-secondary">USBC: <?php echo htmlspecialchars($bowler['usbc_id']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div>
                                <span class="badge bg-primary me-1"><?php echo $bowler['dexterity']; ?></span>
                                <span class="badge bg-success"><?php echo $bowler['style']; ?></span>
                            </div>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($bowler['home_house'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <span class="badge bg-success fs-6"><?php echo number_format($bowler['overall_average'], 1); ?></span>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?php echo $bowler['total_series']; ?></span>
                        </td>
                        <td>
                            <span class="badge bg-warning"><?php echo $bowler['highest_series']; ?></span>
                        </td>
                        <td>
                            <?php if($bowler['series_700_plus'] > 0): ?>
                                <span class="badge bg-success me-1">700+: <?php echo $bowler['series_700_plus']; ?></span>
                            <?php endif; ?>
                            <?php if($bowler['series_800_plus'] > 0): ?>
                                <span class="badge bg-danger">800+: <?php echo $bowler['series_800_plus']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="viewBowlerDetails(<?php echo $bowler['bowler_id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewBowlerHistory(<?php echo $bowler['bowler_id']; ?>)">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Statistics Summary -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?php echo count(array_filter($bowlers, fn($b) => $b['overall_average'] >= 200)); ?></h4>
                <p class="mb-0">200+ Average</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?php echo count(array_filter($bowlers, fn($b) => $b['series_700_plus'] > 0)); ?></h4>
                <p class="mb-0">700+ Series</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?php echo count(array_filter($bowlers, fn($b) => $b['series_800_plus'] > 0)); ?></h4>
                <p class="mb-0">800+ Series</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?php echo count(array_filter($bowlers, fn($b) => $b['total_series'] >= 10)); ?></h4>
                <p class="mb-0">10+ Series</p>
            </div>
        </div>
    </div>
</div>

<script>
function viewBowlerDetails(bowlerId) {
    // You can implement a modal or redirect to a detailed view
    alert('View details for bowler ID: ' + bowlerId);
}

function viewBowlerHistory(bowlerId) {
    // You can implement a modal or redirect to a history view
    alert('View history for bowler ID: ' + bowlerId);
}
</script>
