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

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM player WHERE player_id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);

$statement->execute();
$player = $statement->fetch();


if ($_POST) {
    // image check
    if (isset($_POST['update']) && isset($_POST['id'])) {
        if (isset($_FILES['image']) && ($_FILES['image']['error'] > 0)) {
            $validated = false;
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

                $player_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

                $query = "INSERT INTO image (filename, player_id) VALUES (:filename, :id)";
                $statement = $db->prepare($query);

                $statement->bindValue(':filename', $new_path);
                $statement->bindValue(':id', $player_id);
                $statement->execute();

                header("Location: view.php?id={$id}");
                exit;
            } else {
                $validated = false;
            }
        }
    }

    // check if update is clicked and id are present
    if (isset($_POST['update']) && isset($_POST['id'])) {
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
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

                if ($fullname == false || $position == false || $shoots == false || $playstyle == false || $id == false) {
                    $validated = false;
                } else {
                    //insert
                    $query = "UPDATE player SET full_name = :fullname, position = :position, shoots = :shoots, playstyle = :playstyle WHERE player_id = :id";
                    $statement = $db->prepare($query);

                    $statement->bindValue(':fullname', $fullname);
                    $statement->bindValue(':position', $position);
                    $statement->bindValue(':shoots', $shoots);
                    $statement->bindValue(':playstyle', $playstyle);
                    $statement->bindValue(':id', $id);
                    $statement->execute();

                    header("Location: view.php?id={$id}");
                    exit;
                }
            }
        }
    }
}

// if delete is clicked
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM player WHERE player_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);

    $statement->execute();

    header("Location: players.php");
    exit;
}


$queryx = "SELECT filename FROM image WHERE player_id = :id1";
$statementx = $db->prepare($queryx);
$statementx->bindValue(':id1', $id);
$statementx->execute();
$row = $statementx->fetch();


// if delete-img is clicked
if (isset($_POST['delete-img']) && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    // get filename
    if (file_exists($row['filename'])) {
        unlink($row['filename']);
    }

    // delete filename
    $query2 = "DELETE FROM image WHERE player_id = :id";
    $statement2 = $db->prepare($query2);
    $statement2->bindValue(':id', $id);

    $statement2->execute();

    header("Location: players.php");
    exit;
}

$queryc = "SELECT * FROM comment WHERE player_id = :idc";
$statementc = $db->prepare($queryc);
$statementc->bindValue(':idc', $id);
$statementc->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit</title>
</head>

<body>
    <script>
        function deleteComment(commentId) {
            if (confirm("Are you sure you want to delete this comment?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_comment.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText.trim() == "success") {
                            var commentElement = document.getElementById("comment_" + commentId);
                            if (commentElement) {
                                commentElement.parentNode.removeChild(commentElement);
                            }
                        } else {
                            alert("Failed to delete comment.");
                        }
                    }
                };
                xhr.send("comment_id=" + commentId);
            }
        }
    </script>

    <?php if ($validated == true) : ?>
        <header>
            <p>Basketball Player Database</p>
            <nav>
                <ul class="nav_links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="players.php">Players</a></li>
                </ul>
            </nav>
        </header>
        <div id="wrapper">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $player['player_id'] ?>">

                <label for="fullname">Full Name:</label>
                <input name="fullname" id="fullname" value="<?= $player['full_name'] ?>" required>

                <label for="position">Position:</label>
                <input name="position" id="position" required value="<?= $player['position'] ?>">

                <label for="shoots">Shoots/Dominant Hand:</label>
                <input name="shoots" id="shoots" required value="<?= $player['shoots'] ?>">

                <label for="playstyle">Playstyle:</label>
                <input name="playstyle" id="playstyle" required value="<?= $player['playstyle'] ?>">

                <label for="image">Filename:</label>
                <input type="file" name="image" id="image">

                <input id="create" type="submit" name="update" value="Update">
                <input id="create" type="submit" name="delete" value="Delete">

                <?php if ($row) : ?>
                    <input id="create" type="submit" name="delete-img" value="Delete Image">
                <?php endif ?>
            </form>
        </div>
        <div id="wrapper-comment-edit">
            <?php while ($comment = $statementc->fetch()) : ?>
                <div id="comment_<?= $comment['comment_id'] ?>">
                    <span><?= $comment['name'] ?></span>
                    <p><?= $comment['comment'] ?></p>
                    <button onclick="deleteComment(<?= $comment['comment_id'] ?>)" id="create">Delete</button>
                </div>

            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <h1>An error occured while processing your post</h1>
        <a class="backhome" href="edit.php?id=<?= $_GET['id'] ?>">Retry</a>
    <?php endif ?>
</body>
`

</html>