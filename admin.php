<?php

require('connect.php');
require('authenticate.php');

function file_upload_path($original_filename, $upload_subfolder_name = 'uploads')
{
    $current_folder = dirname(__FILE__);

    // Build an array of paths segment names to be joins using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // The DIRECTORY_SEPARATOR constant is OS specific.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function file_is_an_image($temporary_path, $new_path)
{
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

$validated = true;


if ($_POST) {
    // check if empty
    if (empty($_POST['fullname']) || empty($_POST['position']) || empty($_POST['shoots']) || empty($_POST['playstyle'])) {
        $validated = false;
    } else {
        // check if numeric
        if (is_numeric($_POST['fullname']) || is_numeric($_POST['position']) || is_numeric($_POST['shoots']) || is_numeric($_POST['playstyle'])) {
            $validated = false;
        } else {
            // filter inputs
            $fullname = htmlspecialchars($_POST['fullname']);
            $position = htmlspecialchars($_POST['position']);
            $shoots = htmlspecialchars($_POST['shoots']);
            $playstyle = htmlspecialchars($_POST['playstyle']);

            if ($fullname == false || $position == false || $shoots == false || $playstyle == false) {
                $validated = false;
            } else {
                //insert
                $query = "INSERT INTO player (full_name, position, shoots, playstyle) VALUES (:fullname, :position, :shoots, :playstyle);";
                $statement = $db->prepare($query);

                $statement->bindValue(':fullname', $fullname);
                $statement->bindValue(':position', $position);
                $statement->bindValue(':shoots', $shoots);
                $statement->bindValue(':playstyle', $playstyle);
                $statement->execute();

                $player_id = $db->lastInsertId();

                if (isset($_FILES['image']) && ($_FILES['image']['error'] > 0)) {
                    $validated = false;
                    echo "<p>Error Number: {$_FILES['image']['error']} </p>";
                } else if (isset($_FILES['image']) && ($_FILES['image']['error'] === 0)) {
                    $image_filename = $_FILES['image']['name'];
                    $temp_image_path = $_FILES['image']['tmp_name'];
                    $new_path = file_upload_path($image_filename);
                    if (file_is_an_image($temp_image_path, $new_path)) {
                        $image_info = getimagesize($temp_image_path);
                        $image_type = $image_info[2];

                        if ($image_type === IMAGETYPE_JPEG) {
                            $original_image = imagecreatefromjpeg($temp_image_path);
                        } else if ($image_type === IMAGETYPE_PNG) {
                            $original_image = imagecreatefrompng($temp_image_path);
                        } else if ($image_type === IMAGETYPE_GIF) {
                            $original_image = imagecreatefromgif($temp_image_path);
                        }

                        $newWidth = 120;
                        $newHeight = 180;
                        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resizedImage, $original_image, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($original_image), imagesy($original_image));

                        if ($image_type === IMAGETYPE_JPEG) {
                            imagejpeg($resizedImage, $new_path);
                        } else if ($image_type === IMAGETYPE_PNG) {
                            imagepng($resizedImage, $new_path);
                        } else if ($image_type === IMAGETYPE_GIF) {
                            imagegif($resizedImage, $new_path);
                        }

                        imagedestroy($original_image);
                        imagedestroy($resizedImage);


                        $query2 = "INSERT INTO image (filename, player_id) VALUES (:filename, :id)";
                        $statement2 = $db->prepare($query2);

                        $statement2->bindValue(':filename', $new_path);
                        $statement2->bindValue(':id', $player_id);
                        $statement2->execute();
                    } else {
                        $validated = false;
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Admin</title>
</head>

<body>
    <header>
        <p>Basketball Player Database</p>
        <nav>
            <ul class="nav_links">
                <li><a href="index.php">Home</a></li>
                <li><a href="players.php">Players</a></li>
            </ul>
        </nav>
    </header>
    <?php if ($validated == true) : ?>
        <div id="wrapper">
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <label for="fullname">Full Name:</label>
                <input name="fullname" id="fullname" required>

                <label for="position">Position:</label>
                <input name="position" id="position" required placeholder="eg. Point Guard, Center">

                <label for="shoots">Shoots/Dominant Hand:</label>
                <input name="shoots" id="shoots" required placeholder="Right or Left">

                <label for="playstyle">Playstyle:</label>
                <input name="playstyle" id="playstyle" required placeholder="eg. Smooth, Fundamental ">

                <label for="image">Filename:</label>
                <input type="file" name="image" id="image">

                <input id="create" type="submit" name="command" value="Create">
            </form>
        </div>
    <?php else : ?>
        <br><br>
        <h1>An error occured while processing your post</h1>
        <a class="backhome" href="admin.php">Retry</a>
    <?php endif ?>
</body>
`

</html>