<?php

require('connect.php');

$order = $_POST['option'];
echo $order;
$query = "SELECT * FROM player ORDER BY full_name ASC";

$statement = $db->prepare($query);

$statement->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Basketball Player Database</title>
</head>

<body>
    <header>
        <p>Basketball Player Database</p>
        <nav>
            <ul class="nav_links">
                <li><a href="index.php">Home</a></li>
                <li><a href="players.php" class="active">Players</a></li>
            </ul>
        </nav>
        <a class="cta" href="admin.php"><button>Admin</button></a>
    </header>
    <div id="sort">
        <p>Sorting</p>
        <form method="post" id="form-sort">
            <select id="selectOption" name="option">
                <option value="">Select</option>
                <option value=" full_name">Full Name</option>
                <option value="shoots">Shoots</option>
                <option value="position">Position</option>
            </select>
        </form>

    </div>
    <div id="container">
        <?php while ($row = $statement->fetch()) : ?>
            <div id="player">
                <h2>
                    <a href="view.php?id=<?= $row['player_id'] ?>"><?= $row['full_name'] ?></a>
                </h2>
                <p>Position: <?= $row['position'] ?></p>
                <p>Shoots: <?= $row['shoots'] ?></p>
                <p>Playstyle: <?= $row['position'] ?></p>
            </div>
        <?php endwhile; ?>
    </div>

</body>

</html>