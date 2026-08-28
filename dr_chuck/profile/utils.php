<?php
function error_message() {
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        echo '<p style="color: red;">' . htmlentities($error_message) . '</p>';
    }
}

function success_message() {
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        echo '<p style="color: green;">' . htmlentities($success_message) . '</p>';
    }
}

function postedEntries($year_prefix, $detail_prefix, $detail_name) {
    $indices = [];
    $year_pattern = '/^' . preg_quote($year_prefix, '/') . '(\d+)$/';
    $detail_pattern = '/^' . preg_quote($detail_prefix, '/') . '(\d+)$/';

    foreach ($_POST as $key => $value) {
        if (preg_match($year_pattern, $key, $matches) ||
            preg_match($detail_pattern, $key, $matches)) {
            $indices[(int) $matches[1]] = true;
        }
    }

    ksort($indices, SORT_NUMERIC);
    $entries = [];
    foreach (array_keys($indices) as $index) {
        $entries[] = [
            'year' => $_POST[$year_prefix . $index] ?? null,
            $detail_name => $_POST[$detail_prefix . $index] ?? null
        ];
    }

    return $entries;
}

function postedPositions() {
    return postedEntries('year', 'desc', 'description');
}

function postedEducation() {
    return postedEntries('edu_year', 'edu_school', 'name');
}

function validateEntries($entries, $detail_name) {
    foreach ($entries as $entry) {
        if ($entry['year'] === null || $entry[$detail_name] === null ||
            trim($entry['year']) === '' || trim($entry[$detail_name]) === '') {
            return 'All fields are required';
        }

        if (!is_numeric($entry['year'])) {
            return 'Year must be numeric';
        }
    }

    return true;
}

function validatePositions($positions) {
    return validateEntries($positions, 'description');
}

function validateEducation($education) {
    return validateEntries($education, 'name');
}

function loadPositions($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT year, description
        FROM Position
        WHERE profile_id = :profile_id
        ORDER BY rank');
    $stmt->execute(['profile_id' => $profile_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loadEducation($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT Education.year, Institution.name
        FROM Education JOIN Institution
        ON Education.institution_id = Institution.institution_id
        WHERE Education.profile_id = :profile_id
        ORDER BY Education.rank');
    $stmt->execute(['profile_id' => $profile_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertPositions($pdo, $profile_id, $positions) {
    $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES (:profile_id, :rank, :year, :description)');

    foreach ($positions as $index => $position) {
        $stmt->execute([
            'profile_id' => $profile_id,
            'rank' => $index + 1,
            'year' => trim($position['year']),
            'description' => trim($position['description'])
        ]);
    }
}

function insertEducation($pdo, $profile_id, $education) {
    $find_institution = $pdo->prepare(
        'SELECT institution_id FROM Institution WHERE name = :name'
    );
    $add_institution = $pdo->prepare(
        'INSERT INTO Institution (name) VALUES (:name)'
    );
    $add_education = $pdo->prepare('INSERT INTO Education
        (profile_id, institution_id, rank, year)
        VALUES (:profile_id, :institution_id, :rank, :year)');

    foreach ($education as $index => $entry) {
        $name = trim($entry['name']);
        $find_institution->execute(['name' => $name]);
        $institution_id = $find_institution->fetchColumn();

        if ($institution_id === false) {
            $add_institution->execute(['name' => $name]);
            $institution_id = $pdo->lastInsertId();
        }

        $add_education->execute([
            'profile_id' => $profile_id,
            'institution_id' => $institution_id,
            'rank' => $index + 1,
            'year' => trim($entry['year'])
        ]);
    }
}
