<?php

require('connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM comment WHERE comment_id = :comment_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':comment_id', $commentId);
    $statement->execute();

    echo "success";
}
