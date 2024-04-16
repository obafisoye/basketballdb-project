<?php

require('connect.php');
require('authenticate.php');

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

                header("Location: admin.php");
                exit;
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
    <?php if ($validated == true) : ?>
        <header>
            <p>Basketball Player Database</p>
            <nav>
                <ul class="nav_links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Players</a></li>
                </ul>
            </nav>
        </header>
        <div id="wrapper">
            <form action="admin.php" method="post">
                <label for="fullname">Full Name:</label>
                <input name="fullname" id="fullname" required>

                <label for="position">Position:</label>
                <input name="position" id="position" required placeholder="eg. Point Guard, Center">

                <label for="shoots">Shoots/Dominant Hand:</label>
                <input name="shoots" id="shoots" required placeholder="Right or Left">

                <label for="playstyle">Playstyle:</label>
                <input name="playstyle" id="playstyle" required placeholder="eg. Smooth, Fundamental ">

                <input id="create" type="submit" name="command" value="Create">
                </ul>
            </form>
        </div>
    <?php else : ?>
        <h1>An error occured while processing your post</h1>
        <a class="backhome" href="admin.php">Retry</a>
    <?php endif ?>
</body>
`

</html>