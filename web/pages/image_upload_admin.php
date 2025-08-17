<?php
// Simple test - no includes, no complex logic
echo "<!-- Starting image upload page -->";

// Simple database connection function (only when needed)
function getSimpleDBConnection() {
    $host = 'localhost';
    $dbname = 'bowling_db';
    $username = 'root';
    $password = 'MySecureP@ssw0rd2024!';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        return null; // Return null instead of die() to avoid blank page
    }
}

// File upload handling
$upload_message = '';
$csv_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!-- POST request detected -->";
    
    if (isset($_FILES['bowling_image'])) {
        echo "<!-- File upload detected -->";
        
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file = $_FILES['bowling_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        echo "<!-- File info: " . $file['name'] . " (type: " . $file['type'] . ", error: " . $file['error'] . ") -->";
        
        if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowed_types)) {
            $filename = 'bowling_' . date('Y-m-d_H-i-s') . '_' . basename($file['name']);
            $filepath = $upload_dir . $filename;
            
            echo "<!-- Attempting to move file to: " . $filepath . " -->";
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                echo "<!-- File moved successfully -->";
                
                // Process the image using Python script
                $output_prefix = 'web_upload_' . date('Y-m-d_H-i-s');
                
                $command = "cd ../../ai && python image_to_csv_extractor.py " . escapeshellarg($filepath) . " " . escapeshellarg($output_prefix) . " 2>&1";
                echo "<!-- Executing command: " . $command . " -->";
                
                $output = shell_exec($command);
                echo "<!-- Command output: " . htmlspecialchars($output) . " -->";
                
                // Read the generated CSV file
                $csv_file = "../../ai/{$output_prefix}_database_import.csv";
                if (file_exists($csv_file)) {
                    $csv_data = [];
                    if (($handle = fopen($csv_file, "r")) !== FALSE) {
                        $headers = fgetcsv($handle);
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            $csv_data[] = array_combine($headers, $data);
                        }
                        fclose($handle);
                    }
                    
                    $upload_message = '<div class="alert alert-success">Image uploaded and processed successfully! Found ' . count($csv_data) . ' bowlers.</div>';
                } else {
                    $upload_message = '<div class="alert alert-warning">Image uploaded but CSV processing failed. Output: ' . htmlspecialchars($output) . '</div>';
                }
            } else {
                $upload_message = '<div class="alert alert-danger">Failed to move uploaded file.</div>';
            }
        } else {
            $upload_message = '<div class="alert alert-danger">Invalid file type. Please upload a JPEG, PNG, or GIF image. Error code: ' . $file['error'] . '</div>';
        }
    } else {
        echo "<!-- No file uploaded -->";
        $upload_message = '<div class="alert alert-warning">No file was uploaded. Please select a file and try again.</div>';
    }
}

// Handle CSV import to database
$import_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_to_database']) && $csv_data) {
    $pdo = getSimpleDBConnection();
    if ($pdo) {
        try {
            $pdo->beginTransaction();
            
            $imported_count = 0;
            $errors = [];
            
            foreach ($csv_data as $row) {
                try {
                    // First, ensure the bowler exists
                    $bowler_stmt = $pdo->prepare("SELECT bowler_id FROM bowlers WHERE nickname = ?");
                    $bowler_stmt->execute([$row['bowler_nickname']]);
                    $bowler = $bowler_stmt->fetch();
                    
                    if (!$bowler) {
                        // Insert new bowler
                        $insert_bowler = $pdo->prepare("INSERT INTO bowlers (nickname, dexterity, style) VALUES (?, 'Right', '1 Handed')");
                        $insert_bowler->execute([$row['bowler_nickname']]);
                        $bowler_id = $pdo->lastInsertId();
                    } else {
                        $bowler_id = $bowler['bowler_id'];
                    }
                    
                    // Ensure location exists
                    $location_stmt = $pdo->prepare("SELECT location_id FROM locations WHERE name = ?");
                    $location_stmt->execute([$row['location_name']]);
                    $location = $location_stmt->fetch();
                    
                    if (!$location) {
                        // Insert new location
                        $insert_location = $pdo->prepare("INSERT INTO locations (name, city, state) VALUES (?, 'Unknown', 'Unknown')");
                        $insert_location->execute([$row['location_name']]);
                        $location_id = $pdo->lastInsertId();
                    } else {
                        $location_id = $location['location_id'];
                    }
                    
                    // Insert game series
                    $insert_series = $pdo->prepare("
                        INSERT INTO game_series (bowler_id, location_id, event_date, game1_score, game2_score, game3_score, series_type) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $insert_series->execute([
                        $bowler_id,
                        $location_id,
                        $row['event_date'],
                        $row['game1_score'],
                        $row['game2_score'],
                        $row['game3_score'],
                        $row['series_type']
                    ]);
                    
                    $imported_count++;
                    
                } catch (Exception $e) {
                    $errors[] = "Error importing {$row['bowler_nickname']}: " . $e->getMessage();
                }
            }
            
            $pdo->commit();
            
            if (empty($errors)) {
                $import_message = '<div class="alert alert-success">Successfully imported ' . $imported_count . ' game series to database!</div>';
            } else {
                $import_message = '<div class="alert alert-warning">Imported ' . $imported_count . ' records with some errors: ' . implode(', ', $errors) . '</div>';
            }
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $import_message = '<div class="alert alert-danger">Database import failed: ' . $e->getMessage() . '</div>';
        }
    } else {
        $import_message = '<div class="alert alert-danger">Database connection failed. Please check your database configuration.</div>';
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>Image Upload & Data Extraction
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Debug info -->
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong> 
                        Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?> | 
                        Files Count: <?php echo count($_FILES); ?> | 
                        POST Count: <?php echo count($_POST); ?>
                    </div>
                    
                    <?php echo $upload_message; ?>
                    <?php echo $import_message; ?>
                    
                    <!-- Image Upload Form -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Upload Bowling Sheet Image</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                        <div class="mb-3">
                                            <label for="bowling_image" class="form-label">Select Image File</label>
                                            <input type="file" class="form-control" id="bowling_image" name="bowling_image" accept="image/*" required>
                                            <div class="form-text">Supported formats: JPEG, PNG, GIF. Max size: 10MB</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                                            <i class="fas fa-upload me-2"></i>Upload & Extract Data
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Instructions</h6>
                                </div>
                                <div class="card-body">
                                    <ol class="mb-0">
                                        <li>Upload a clear image of a bowling score sheet</li>
                                        <li>The system will extract bowler names and scores</li>
                                        <li>Review the extracted data below</li>
                                        <li>Import the data to your database</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Extracted Data Display -->
                    <?php if ($csv_data): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Extracted Bowling Data</h6>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="import_to_database" value="1">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-database me-2"></i>Import to Database
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover" id="extractedDataTable">
                                            <thead>
                                                <tr>
                                                    <th>Bowler Name</th>
                                                    <th>Location</th>
                                                    <th>Event Date</th>
                                                    <th>Game 1</th>
                                                    <th>Game 2</th>
                                                    <th>Game 3</th>
                                                    <th>Series Type</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($csv_data as $row): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($row['bowler_nickname']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $row['game1_score']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $row['game2_score']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $row['game3_score']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo htmlspecialchars($row['series_type']); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <?php echo $row['game1_score'] + $row['game2_score'] + $row['game3_score']; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Statistics -->
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <h4><?php echo count($csv_data); ?></h4>
                                                    <small>Total Bowlers</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h4><?php 
                                                        $total_games = 0;
                                                        foreach ($csv_data as $row) {
                                                            $total_games += $row['game1_score'] + $row['game2_score'] + $row['game3_score'];
                                                        }
                                                        echo $total_games;
                                                    ?></h4>
                                                    <small>Total Pins</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <h4><?php 
                                                        $avg_score = count($csv_data) > 0 ? round($total_games / (count($csv_data) * 3), 1) : 0;
                                                        echo $avg_score;
                                                    ?></h4>
                                                    <small>Average Score</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <h4><?php 
                                                        $high_score = 0;
                                                        foreach ($csv_data as $row) {
                                                            $series_total = $row['game1_score'] + $row['game2_score'] + $row['game3_score'];
                                                            if ($series_total > $high_score) {
                                                                $high_score = $series_total;
                                                            }
                                                        }
                                                        echo $high_score;
                                                    ?></h4>
                                                    <small>High Series</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add form submission feedback
    $('#uploadForm').on('submit', function() {
        $('#uploadBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        $('#uploadBtn').prop('disabled', true);
    });
    
    if ($('#extractedDataTable').length) {
        $('#extractedDataTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            responsive: true
        });
    }
});
</script>
