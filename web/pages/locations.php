<?php
// Handle search and filtering
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'location_name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Build query
$query = "SELECT * FROM location_statistics";
$params = [];

if (!empty($search)) {
    $query .= " WHERE location_name LIKE ? OR city LIKE ? OR state LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
}

$query .= " ORDER BY $sort $order";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $locations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $locations = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-map-marker-alt me-2"></i>Locations</h2>
    <div class="text-muted"><?php echo count($locations); ?> locations found</div>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="locations">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by location name, city, or state..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sort">
                    <option value="location_name" <?php echo $sort == 'location_name' ? 'selected' : ''; ?>>Sort by Name</option>
                    <option value="avg_series_score" <?php echo $sort == 'avg_series_score' ? 'selected' : ''; ?>>Sort by Average Score</option>
                    <option value="total_series" <?php echo $sort == 'total_series' ? 'selected' : ''; ?>>Sort by Total Series</option>
                    <option value="unique_bowlers" <?php echo $sort == 'unique_bowlers' ? 'selected' : ''; ?>>Sort by Unique Bowlers</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="order">
                    <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
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

<!-- Locations Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($locations)): ?>
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No locations found</h4>
            <p class="text-muted">No bowling locations have been added to the database yet.</p>
            <a href="?page=dashboard" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>City/State</th>
                        <th>Bowlers</th>
                        <th>Series</th>
                        <th>Avg Score</th>
                        <th>Best Series</th>
                        <th>Milestones</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($locations as $location): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-building fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($location['location_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?php echo $location['location_id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($location['city'] || $location['state']): ?>
                                <div>
                                    <?php if($location['city']): ?>
                                        <span class="badge bg-info me-1"><?php echo htmlspecialchars($location['city']); ?></span>
                                    <?php endif; ?>
                                    <?php if($location['state']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($location['state']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?php echo $location['unique_bowlers']; ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success"><?php echo $location['total_series']; ?></span>
                        </td>
                        <td>
                            <?php if($location['avg_series_score']): ?>
                                <span class="badge bg-warning fs-6"><?php echo number_format($location['avg_series_score'], 1); ?></span>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($location['highest_series']): ?>
                                <span class="badge bg-danger"><?php echo $location['highest_series']; ?></span>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($location['series_700_plus'] > 0): ?>
                                <span class="badge bg-success me-1">700+: <?php echo $location['series_700_plus']; ?></span>
                            <?php endif; ?>
                            <?php if($location['series_800_plus'] > 0): ?>
                                <span class="badge bg-danger">800+: <?php echo $location['series_800_plus']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="viewLocationDetails(<?php echo $location['location_id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewLocationHistory(<?php echo $location['location_id']; ?>)">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics Summary -->
<?php if (!empty($locations)): ?>
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?php echo count($locations); ?></h4>
                <p class="mb-0">Total Locations</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?php echo array_sum(array_column($locations, 'unique_bowlers')); ?></h4>
                <p class="mb-0">Total Bowlers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?php echo array_sum(array_column($locations, 'total_series')); ?></h4>
                <p class="mb-0">Total Series</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?php echo number_format(array_sum(array_column($locations, 'avg_series_score')) / count($locations), 1); ?></h4>
                <p class="mb-0">Avg Score</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function viewLocationDetails(locationId) {
    // You can implement a modal or redirect to a detailed view
    alert('View details for location ID: ' + locationId);
}

function viewLocationHistory(locationId) {
    // You can implement a modal or redirect to a history view
    alert('View history for location ID: ' + locationId);
}
</script>
