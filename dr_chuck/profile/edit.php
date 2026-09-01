<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: error.php?type=access');
    exit;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit;
}

$profile_id = $_POST['profile_id'] ?? $_GET['profile_id'] ?? null;

if ($profile_id === null) {
    header('Location: error.php?type=missing');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :profile_id');
    $stmt->execute(['profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: error.php?type=database');
    exit;
}

if ($profile === false) {
    header('Location: error.php?type=not-found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $headline = $_POST['headline'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $positions = postedPositions();
    $education = postedEducation();
    $position_validation = validatePositions($positions);
    $education_validation = validateEducation($education);

    if (
        trim($first_name) === '' ||
        trim($last_name) === '' ||
        trim($email) === '' ||
        trim($headline) === '' ||
        trim($summary) === ''
    ) {
        $_SESSION['error_message'] = 'All fields are required';
    } else if (strpos($email, '@') === false) {
        $_SESSION['error_message'] = 'Email address must contain @';
    } else if ($position_validation !== true) {
        $_SESSION['error_message'] = $position_validation;
    } else if ($education_validation !== true) {
        $_SESSION['error_message'] = $education_validation;
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('UPDATE Profile
                SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    headline = :headline,
                    summary = :summary
                WHERE profile_id = :profile_id');
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'headline' => $headline,
                'summary' => $summary,
                'profile_id' => $profile_id
            ]);

            $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :profile_id');
            $stmt->execute(['profile_id' => $profile_id]);
            $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :profile_id');
            $stmt->execute(['profile_id' => $profile_id]);

            insertPositions($pdo, $profile_id, $positions);
            insertEducation($pdo, $profile_id, $education);

            $pdo->commit();
            $_SESSION['success_message'] = 'Profile updated';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to update profile';
        }
    }

    header('Location: edit.php?profile_id=' . rawurlencode((string) $profile_id));
    exit;
}

$first_name = $profile['first_name'];
$last_name = $profile['last_name'];
$email = $profile['email'];
$headline = $profile['headline'];
$summary = $profile['summary'];

try {
    $positions = loadPositions($pdo, $profile_id);
    $education = loadEducation($pdo, $profile_id);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: error.php?type=database');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <title>4070ffb0</title>
    <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>
    <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
        integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
        crossorigin="anonymous"></script>
</head>
<body>
<div class="page">
    <h1>Editing Profile</h1>
    <div class="notice">
        <?php error_message() ?>
    </div>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= htmlentities($profile_id) ?>">
        <div class="field">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlentities($first_name) ?>">
        </div>
        <div class="field">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlentities($last_name) ?>">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?= htmlentities($email) ?>">
        </div>
        <div class="field">
            <label for="headline">Headline</label>
            <input type="text" id="headline" name="headline" value="<?= htmlentities($headline) ?>">
        </div>
        <div class="field">
            <label for="summary">Summary</label>
            <textarea id="summary" name="summary" rows="8" cols="80"><?= htmlentities($summary) ?></textarea>
        </div>
        <div class="entry-group">
            <div class="entry-row">
                <span class="label">Education</span>
                <input id="addEducation" type="button" value="+">
            </div>
            <div id="educationFields">
                <?php foreach ($education as $index => $entry): ?>
                    <?php $education_number = $index + 1; ?>
                    <div id="education<?= $education_number ?>" class="entry-block">
                        <div class="entry-row">
                            Year:
                            <input type="text"
                                name="edu_year<?= $education_number ?>"
                                value="<?= htmlentities($entry['year']) ?>">
                            <input type="button"
                                class="removeEducation"
                                data-education-id="education<?= $education_number ?>"
                                value="-">
                        </div>
                        <div class="entry-row">
                            School:
                            <input type="text"
                                size="80"
                                name="edu_school<?= $education_number ?>"
                                class="school"
                                value="<?= htmlentities($entry['name']) ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="entry-group">
            <div class="entry-row">
                <span class="label">Position</span>
                <input id="addPosition" type="button" value="+">
            </div>
            <div id="positionFields">
                <?php foreach ($positions as $index => $position): ?>
                    <?php $position_number = $index + 1; ?>
                    <div id="position<?= $position_number ?>" class="entry-block">
                        <div class="entry-row">
                            Year:
                            <input type="text"
                                name="year<?= $position_number ?>"
                                value="<?= htmlentities($position['year']) ?>">
                            <input type="button"
                                class="removePosition"
                                data-position-id="position<?= $position_number ?>"
                                value="-">
                        </div>
                        <textarea
                            name="desc<?= $position_number ?>"
                            rows="8"
                            cols="80"><?= htmlentities($position['description']) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-actions">
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    var positionNext = <?= count($positions) ?>;
    var positionCount = positionNext;
    var educationNext = <?= count($education) ?>;
    var educationCount = educationNext;

    $('.school').autocomplete({
        source: 'school.php'
    })

    $('#positionFields').on('click', '.removePosition', function () {
        $('#' + $(this).data('position-id')).remove()
        positionCount--
    })

    $('#educationFields').on('click', '.removeEducation', function () {
        $('#' + $(this).data('education-id')).remove()
        educationCount--
    })

    $('#addPosition').click(function () {
        if (positionCount >= 9) {
            alert('Maximum of nine position entries exceeded')
            return
        }

        positionNext++
        positionCount++
        var positionId = 'position' + positionNext
        var position = $('<div>').attr('id', positionId).addClass('entry-block')
        var row = $('<div>').addClass('entry-row').text('Year: ')
        row.append($('<input>', {
            type: 'text',
            name: 'year' + positionNext
        }))
        row.append(' ')
        row.append($('<input>', {
            type: 'button',
            class: 'removePosition',
            'data-position-id': positionId,
            value: '-'
        }))
        position.append(row)
        position.append($('<textarea>', {
            name: 'desc' + positionNext,
            rows: 8,
            cols: 80
        }))
        $('#positionFields').append(position)
    })

    $('#addEducation').click(function () {
        if (educationCount >= 9) {
            alert('Maximum of nine education entries exceeded')
            return
        }

        educationNext++
        educationCount++
        var educationId = 'education' + educationNext
        var educationField = $('<div>').attr('id', educationId).addClass('entry-block')
        var yearRow = $('<div>').addClass('entry-row').text('Year: ')
        yearRow.append($('<input>', {
            type: 'text',
            name: 'edu_year' + educationNext
        }))
        yearRow.append(' ')
        yearRow.append($('<input>', {
            type: 'button',
            class: 'removeEducation',
            'data-education-id': educationId,
            value: '-'
        }))

        var schoolRow = $('<div>').addClass('entry-row').text('School: ')
        var schoolInput = $('<input>', {
            type: 'text',
            size: 80,
            name: 'edu_school' + educationNext,
            class: 'school'
        })
        schoolRow.append(schoolInput)
        educationField.append(yearRow)
        educationField.append(schoolRow)
        $('#educationFields').append(educationField)

        schoolInput.autocomplete({
            source: 'school.php'
        })
    })
})
</script>
</body>
</html>
