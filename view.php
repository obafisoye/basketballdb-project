<?php

require('connect.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM player WHERE player_id = :id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id);
$statement->execute();
$player = $statement->fetch();

$query2 = "SELECT h.accolades, h.team, h.season, s.games, s.ppg, s.rpg, s.apg, s.fg_per, s.3_per, s.ft_per
FROM history h JOIN stats s ON h.stats_id = s.stats_id WHERE player_id = :id2";

$statement2 = $db->prepare($query2);
$statement2->bindValue(':id2', $id);
$statement2->execute();

$statement3 = $db->prepare($query2);
$statement3->bindValue(':id2', $id);
$statement3->execute();

$query4 = "SELECT * FROM comment WHERE player_id = :id4 ORDER BY created_at DESC";
$statement5 = $db->prepare($query4);
$statement5->bindValue(':id4', $id);
$statement5->execute();


$validated = true;

if ($_POST) {
    if (empty($_POST['name']) || empty($_POST['comment']) || empty($_POST['id'])) {
        $validated = false;
    } else {
        $name = htmlspecialchars($_POST['name']);
        $comment = htmlspecialchars($_POST['comment']);
        $playerid = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($name == false || $comment == false || $playerid == false) {
            $validated = false;
        } else {
            $query3 = "INSERT INTO comment (name, comment, player_id) VALUES (:name, :comment, :id3)";
            $statement4 = $db->prepare($query3);

            $statement4->bindValue(':name', $name);
            $statement4->bindValue(':comment', $comment);
            $statement4->bindValue(':id3', $playerid);
            $statement4->execute();

            header("Location: view.php?id={$playerid}");
            exit;
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
        <a class="cta" href="edit.php?id=<?= $player['player_id'] ?>"><button>Edit</button></a>
    </header>
    <?php if ($validated == true) : ?>
        <div id="container-view">
            <div id="player-view">
                <h2>
                    <a><?= $player['full_name'] ?></a>
                </h2>
                <p>Position: <?= $player['position'] ?></p>
                <p>Shoots: <?= $player['shoots'] ?></p>
                <p>Playstyle: <?= $player['position'] ?></p>
            </div>
            <div id="history">
                <?php while ($accolades = $statement3->fetch()) : ?>
                    <p>In the <?= $accolades['season'] ?> season, <?= $player['full_name'] ?> was awarded with <?= $accolades['accolades'] ?>.</p>
                <?php endwhile; ?>
            </div>
            <div id="stats">
                <table id="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Team</th>
                            <th>Games</th>
                            <th>PPG</th>
                            <th>RPG</th>
                            <th>APG</th>
                            <th>FG%</th>
                            <th>3PT%</th>
                            <th>FT%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $statement2->fetch()) : ?>
                            <tr>
                                <td><?= $row['season'] ?></td>
                                <td><?= $row['team'] ?></td>
                                <td><?= $row['games'] ?></td>
                                <td><?= $row['ppg'] ?></td>
                                <td><?= $row['rpg'] ?></td>
                                <td><?= $row['apg'] ?></td>
                                <td><?= $row['fg_per'] ?></td>
                                <td><?= $row['3_per'] ?></td>
                                <td><?= $row['ft_per'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div id="comment-section">
                <?php if ($statement5->rowCount() > 0) : ?>
                    <?php while ($comment = $statement5->fetch()) : ?>
                        <div class="comment-block">
                            <span><?= $comment['name'] ?></span>
                            <p><?= $comment['comment'] ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php endif ?>

                <div id="comment-form-div">
                    <p>Join the discussion</p>
                    <form action="view.php" id="comment-form" method="post">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <input name="name" id="name" type="text" required placeholder="Username">

                        <textarea id="comment" name="comment" rows="3" cols="50"></textarea>

                        <button type="submit" id="create" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else : ?>
        <br><br>
        <h1>An error occured while processing your post</h1>
        <a class="backhome" href="players.php">Retry</a>
    <?php endif ?>
</body>

</html>