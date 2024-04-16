<?php

require('connect.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM player WHERE player_id = :id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id);
$statement->execute();
$row = $statement->fetch();

$query2 = "SELECT * FROM history WHERE player_id = :id2";
$statement2 = $db->prepare($query2);
$statement2->bindValue(':id2', $id);
$statement2->execute();
$history = $statement2->fetch();

$query3 = "SELECT * FROM stats WHERE stats_id = :id3";
$statement3 = $db->prepare($query3);
$statement3->bindValue(':id3', $history['stats_id']);
$statement3->execute();
$stats = $statement3->fetch();
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
                <li><a href="players.php">Players</a></li>
            </ul>
        </nav>
        <a class="cta" href="admin.php"><button>Admin</button></a>
        <a class="cta" href="edit.php?id=<?= $row['player_id'] ?>"><button>Edit</button></a>
    </header>
    <div id="container">
        <div id="player">
            <h2>
                <a><?= $row['full_name'] ?></a>
            </h2>
            <p>Position: <?= $row['position'] ?></p>
            <p>Shoots: <?= $row['shoots'] ?></p>
            <p>Playstyle: <?= $row['position'] ?></p>
        </div>
        <div id="history">
            <p><?= $history['accolades'] ?></p>
        </div>
        <div id="stats">
            <p><?= $stats['games'] ?></p>
        </div>
    </div>

</body>

</html>