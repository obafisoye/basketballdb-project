<?php
require('connect.php');
require('authenticate.php');

$validated = true;


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM player WHERE player_id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);

$statement->execute();
$player = $statement->fetch();


if ($_POST) {
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

                    header("Location: players.php");
                    exit;
                }
            }
        }
    }
}

// if delete is clicked
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM posts WHERE player_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);

    $statement->execute();

    header("Location: players.php");
    exit;
}

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
            <form method="post">
                <input type="hidden" name="id" value="<?= $player['player_id'] ?>">

                <label for="fullname">Full Name:</label>
                <input name="fullname" id="fullname" value="<?= $player['full_name'] ?>" required>

                <label for="position">Position:</label>
                <input name="position" id="position" required value="<?= $player['position'] ?>">

                <label for="shoots">Shoots/Dominant Hand:</label>
                <input name="shoots" id="shoots" required value="<?= $player['shoots'] ?>">

                <label for="playstyle">Playstyle:</label>
                <input name="playstyle" id="playstyle" required value="<?= $player['playstyle'] ?>">

                <input id="create" type="submit" name="update" value="Update">
                <input id="create" type="submit" name="delete" value="Delete">
                </ul>
            </form>
        </div>
    <?php else : ?>
        <h1>An error occured while processing your post</h1>
        <a class="backhome" href="edit.php?id=<?= $_GET['id'] ?>">Retry</a>
    <?php endif ?>
</body>
`

</html>