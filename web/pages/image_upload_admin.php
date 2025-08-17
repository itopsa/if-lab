<?php
// Very simple test version
echo "<!-- Image Upload Admin Page Loading -->";
echo "<!-- PHP is executing -->";
echo "<!-- Current time: " . date('Y-m-d H:i:s') . " -->";
?>

<h1>Image Upload Admin Page</h1>
<p>This page is working!</p>
<p>Current time: <?php echo date('Y-m-d H:i:s'); ?></p>

<div style="background: white; padding: 20px; border-radius: 10px; margin: 20px;">
    <h2>Upload Form</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="bowling_image">Select Image File:</label><br>
        <input type="file" id="bowling_image" name="bowling_image" accept="image/*" required><br><br>
        <button type="submit" style="background: blue; color: white; padding: 10px 20px; border: none; border-radius: 5px;">
            Upload & Extract Data
        </button>
    </form>
</div>

<div style="background: lightblue; padding: 20px; border-radius: 10px; margin: 20px;">
    <h2>Instructions</h2>
    <ol>
        <li>Upload a clear image of a bowling score sheet</li>
        <li>The system will extract bowler names and scores</li>
        <li>Review the extracted data below</li>
        <li>Import the data to your database</li>
    </ol>
</div>

<?php
echo "<!-- Image Upload Admin Page Finished -->";
?>
