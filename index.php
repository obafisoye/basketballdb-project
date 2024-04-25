<?php

require('connect.php');

$statement = null;

if ($_GET) {
    if (isset($_GET['playername'])) {
        $searchQuery = htmlspecialchars($_GET['playername'], ENT_QUOTES, 'UTF-8');

        $query = "SELECT * FROM player WHERE full_name LIKE :searchQuery";
        $statement = $db->prepare($query);

        $searchParam = '%' . $searchQuery . '%';
        $statement->bindParam(':searchQuery', $searchParam);
        $statement->execute();
    }
}

$query2 = "SELECT * FROM player";
$statement2 = $db->prepare($query2);
$statement2->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title>Basketball Player Database</title>
</head>

<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">Basketball Player Database</button>
            <div class="dropdown-content">
                <?php while ($player = $statement2->fetch()) : ?>
                    <a href="view.php?id=<?= $player['player_id'] ?>"><?= $player['full_name'] ?></a>
                <?php endwhile; ?>
            </div>
        </div>
        <nav>
            <ul class="nav_links">
                <li><a href="index.php" class="active">Home</a></li>
            </ul>
        </nav>
        <a class="cta" href="admin.php"><button>Admin</button></a>
    </header>
    <form method="get">
        <div class="search">
            <button class="search-icon material-symbols-outlined" type="submit">search</button>
            <input type="text" class="search-input" placeholder="Search for a player by name" name="playername" required>
        </div>
    </form>
    <?php if ($statement && $statement->rowCount() > 0) : ?>
        <?php while ($row = $statement->fetch()) : ?>
            <div id="player-searched">
                <h2><a href="view.php?id=<?= $row['player_id'] ?>"><?= $row['full_name'] ?></a></h2>
            </div>

        <?php endwhile; ?>
    <?php else : ?>
        <br><br>
        <p>No players found.</p>
    <?php endif ?>
</body>

</html>