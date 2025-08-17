<?php
require_once '../config.php';

// Simple test - no includes, no complex logic
echo "<!-- Starting image upload page -->";

// Basic file upload handling
$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!-- POST request detected -->";
    
    if (isset($_FILES['bowling_image'])) {
        echo "<!-- File upload detected -->";
        $upload_message = '<div class="alert alert-success">File upload detected! Processing will be added next.</div>';
    } else {
        echo "<!-- No file uploaded -->";
        $upload_message = '<div class="alert alert-warning">No file was uploaded. Please select a file and try again.</div>';
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
                    <h1>Image Upload Admin Page</h1>
                    <p>This page is working!</p>
                    <p>Current time: <?php echo date('Y-m-d H:i:s'); ?></p>
                    
                    <!-- Debug info -->
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong> 
                        Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?> | 
                        Files Count: <?php echo count($_FILES); ?> | 
                        POST Count: <?php echo count($_POST); ?>
                    </div>
                    
                    <?php echo $upload_message; ?>
                    
                    <!-- Simple upload form -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Upload Bowling Sheet Image</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="bowling_image" class="form-label">Select Image File</label>
                                            <input type="file" class="form-control" id="bowling_image" name="bowling_image" accept="image/*" required>
                                            <div class="form-text">Supported formats: JPEG, PNG, GIF. Max size: 10MB</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
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
                    
                    <!-- Test message -->
                    <div class="alert alert-info mt-3">
                        <strong>Test Message:</strong> If you can see this, the page is loading correctly!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo "<!-- Finished image upload page -->";
?>
