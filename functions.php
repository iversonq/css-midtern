<?php

// Dummy user data retrieval function
function getUsers() {
    return [
        ["email" => "user1@email.com", "password" => "password"],
        ["email" => "user2@email.com", "password" => "password"],
        ["email" => "user3@email.com", "password" => "password"],
        ["email" => "user4@email.com", "password" => "password"],
        ["email" => "user5@email.com", "password" => "password"]
    ];
}

// Validates login credentials
function validateLoginCredentials($email, $password) {
    $errors = [];

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email.";
    } elseif (!emailExists($email)) {
        $errors[] = "Invalid email or password.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    return $errors;
}

// Helper to check if email exists in user data
function emailExists($email) {
    foreach (getUsers() as $user) {
        if ($user['email'] === $email) {
            return true;
        }
    }
    return false;
}

// Checks if login credentials are correct
function checkLoginCredentials($email, $password) {
    foreach (getUsers() as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            return true;
        }
    }
    return false;
}

// Redirects active session users to the dashboard
function checkUserSessionIsActive() {
    if (isset($_SESSION['email']) && basename($_SERVER['PHP_SELF']) == 'index.php') {
        header("Location: dashboard.php");
        exit;
    }
}

// Redirects unauthorized users to login page
function guard() {
    if (empty($_SESSION['email']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
        header("Location: index.php");
        exit;
    }
}

// Formats error messages as an HTML list
function displayErrors($errors) {
    return "<ul>" . implode('', array_map(fn($error) => "<li>" . htmlspecialchars($error) . "</li>", $errors)) . "</ul>";
}

// Renders a single error message as a dismissible alert
function renderErrorsToView($error) {
    return empty($error) ? null : "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                " . htmlspecialchars($error) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
}

// Validates student data to ensure required fields are filled
function validateStudentData($student_data) {
    $errors = [];
    if (empty($student_data['student_id'])) $errors[] = "Student ID is required.";
    if (empty($student_data['first_name'])) $errors[] = "First Name is required.";
    if (empty($student_data['last_name'])) $errors[] = "Last Name is required.";
    return $errors;
}

// Checks for duplicate student data in session
function checkDuplicateStudentData($student_data) {
    return findDataByField($_SESSION['student_data'] ?? [], 'student_id', $student_data['student_id']);
}

// Gets a student's index in session data by student ID
function getSelectedStudentIndex($student_id) {
    return findDataIndexByField($_SESSION['student_data'] ?? [], 'student_id', $student_id);
}

// Gets a student record by index
function getSelectedStudentData($index) {
    return $_SESSION['student_data'][$index] ?? false;
}

// Gets base URL for the site
function getBaseURL() {
    return 'http://' . $_SERVER['HTTP_HOST'] . '/midterms';
}

// Validates subject data to ensure required fields are filled
function validateSubjectData($subject_data) {
    $errors = [];
    if (empty($subject_data['subject_code'])) $errors[] = "Subject Code is required.";
    if (empty($subject_data['subject_name'])) $errors[] = "Subject Name is required.";
    return $errors;
}

// Checks for duplicate subject data in session
function checkDuplicateSubjectData($subject_data) {
    return findDataByField($_SESSION['subject_data'] ?? [], 'subject_code', $subject_data['subject_code']);
}

// Gets a subject's index in session data by subject code
function getSelectedSubjectIndex($subject_code) {
    return findDataIndexByField($_SESSION['subject_data'] ?? [], 'subject_code', $subject_code);
}

// Gets a subject record by index
function getSelectedSubjectData($index) {
    return $_SESSION['subject_data'][$index] ?? false;
}

// Validates attached subject data
function validateAttachedSubject($subject_data) {
    return empty($subject_data['subject_code']) ? ["Subject Code is required for attachment."] : [];
}

// Helper function to find a data item by a specific field
function findDataByField($dataArray, $field, $value) {
    foreach ($dataArray as $item) {
        if ($item[$field] === $value) {
            return $item;
        }
    }
    return null;
}

// Helper function to find a data index by a specific field
function findDataIndexByField($dataArray, $field, $value) {
    foreach ($dataArray as $index => $item) {
        if ($item[$field] === $value) {
            return $index;
        }
    }
    return null;
}
?>
