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



        <!--

            <div id="stats">
                <p>Games Played: <?= $row['games'] ?></p>
                <p>Points Per Game:<?= $row['ppg'] ?></p>
                <p>Rebounds Per Game:<?= $row['rpg'] ?></p>
                <p>Assists Per Game:<?= $row['apg'] ?></p>
                <p>3 Point Percentage:<?= $row['3_per'] ?></p>
                <p>Free Throw Percentage:<?= $row['ft_per'] ?></p>
            </div>
        -->
    </div>

</body>

</html>