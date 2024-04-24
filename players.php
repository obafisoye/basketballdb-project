<?php

require('connect.php');

$sort = "full_name";

if ($_POST) {
    if (isset($_POST['option']) && !empty($_POST['option'])) {
        $sort = htmlspecialchars($_POST['option']);
    }
}

$query = "SELECT * FROM player ORDER BY $sort ASC";

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
        <form method="post" id="form-sort" action="players.php">
            <select id="selectOption" name="option" onchange="this.form.submit();">
                <option value="" <?php if ($sort === '') echo 'selected'; ?>>Select</option>
                <option value="full_name" <?php if ($sort === 'full_name') echo 'selected'; ?>>Full Name</option>
                <option value="shoots" <?php if ($sort === 'shoots') echo 'selected'; ?>>Shoots</option>
                <option value="position" <?php if ($sort === 'position') echo 'selected'; ?>>Position</option>
            </select>
        </form>

    </div>
    <div id="container">
        <?php while ($row = $statement->fetch()) : ?>
            <div class="player">
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