<?php
// DEBUG
// print_r($_REQUEST);
// DEBUG

if (!isset($_GET['name'])) {
    echo('name parameter missing');
    die();
}

if (isset($_POST['logout'])) {
    exit(header('location: index.php'));
}

$choices = ['rock', 'paper', 'scissors'];

function random_choice($choices) {
    return $choices[random_int(0, count($choices) - 1)];
}

function decide_winner($player_choice, $computer_choice) {
    if ($player_choice === $computer_choice) {
        return 'Tie';
    }

    $player_wins =
        ($player_choice === 'rock' && $computer_choice === 'scissors') ||
        ($player_choice === 'paper' && $computer_choice === 'rock') ||
        ($player_choice === 'scissors' && $computer_choice === 'paper');

    return $player_wins ? 'You Win' : 'You Lose';
}

function play_game($player_choice, $computer_choice) {
    $result = decide_winner($player_choice, $computer_choice);
    $human = ucfirst($player_choice);
    $computer = ucfirst($computer_choice);

    return "Human=$human Computer=$computer Result=$result";
}

function test_game($choices) {
    $results = [];

    foreach ($choices as $computer_choice) {
        foreach ($choices as $player_choice) {
            $results[] = play_game($player_choice, $computer_choice);
        }
    }

    return implode("\n", $results);
}

$name = $_GET['name'];
$player_choice = $_POST['human'] ?? null;
$game_result = '';

if (in_array($player_choice, $choices, true)) {
    $computer_choice = random_choice($choices);
    $game_result = play_game($player_choice, $computer_choice);
} elseif ($player_choice === 'test') {
    $game_result = test_game($choices);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>R . P . S</title>
</head>
<body>
    <p>play it</p>
    <p>welcome: <?= $name ?></p>
    <form method="post">
        <select name="human">
            <option value="0">-- select --</option>
            <option value="rock">rock</option>
            <option value="paper">paper</option>
            <option value="scissors">scissors</option>
            <option value="test">test</option>
        </select>
        <input type="submit" value="play">
        <input type="submit" name="logout" value="logout">
    </form>
    <div style="background-color: #f5f5f5">
        <pre><?= $game_result ?></pre>
    </div>
</body>
</html>
