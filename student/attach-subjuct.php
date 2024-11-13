<?php
session_start();
$pageTitle = "Attach Subject to Student";

include '../header.php';
include '../functions.php';

if (empty($_SESSION['email'])) {
    header("Location: ../index.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$studentToAttach = null;
$errors = [];

checkUserSessionIsActive();
guard();

// Fetch student ID from GET and locate student data in session
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $studentToAttach = getStudentDataById($student_id);
    if (!$studentToAttach) {
        $errors[] = "Student not found.";
    }
} else {
    $errors[] = "No student selected.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $studentToAttach) {
    $subject_codes = $_POST['subject_codes'] ?? [];
    if ($subject_codes) {
        $_SESSION['attached_subjects'][$student_id] = array_unique(array_merge(
            $_SESSION['attached_subjects'][$student_id] ?? [],
            $subject_codes
        ));
    } else {
        $errors[] = 'At least one subject should be selected.';
    }
}

// Helper function to get student data by ID
function getStudentDataById($student_id) {
    foreach ($_SESSION['student_data'] ?? [] as $student) {
        if ($student['student_id'] === $student_id) {
            return $student;
        }
    }
    return null;
}

?>

<div class="container mt-5">
    <h2>Attach Subject to Student</h2>
    <br>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
        </ol>
    </nav>
    <hr>
    <br>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong>System Errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($studentToAttach): ?>
        <h3>Selected Student Information</h3>
        <ul>
            <li><strong>Student ID:</strong> <?= htmlspecialchars($studentToAttach['student_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($studentToAttach['first_name'] . ' ' . $studentToAttach['last_name']) ?></li>
        </ul>
        
        <hr>

        <form method="post">
            <?php 
            $attached_subjects = $_SESSION['attached_subjects'][$student_id] ?? [];
            $available_subjects = array_filter($_SESSION['subject_data'] ?? [], function($subject) use ($attached_subjects) {
                return !in_array($subject['subject_code'], $attached_subjects);
            });

            if ($available_subjects): ?>
                <h3>Select Subjects to Attach</h3>
                <?php foreach ($available_subjects as $subject): ?>
                    <div>
                        <input type="checkbox" name="subject_codes[]" value="<?= htmlspecialchars($subject['subject_code']) ?>">
                        <?= htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']) ?>
                    </div>
                <?php endforeach; ?>
                <br>
                <button type="submit" class="btn btn-primary">Attach Subjects</button>
            <?php else: ?>
                <p>No subjects available to attach.</p>
            <?php endif; ?>
        </form>

        <hr>
        <h3 class="mt-5">Attached Subjects for Student</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['attached_subjects'][$student_id])): ?>
                    <?php foreach ($_SESSION['attached_subjects'][$student_id] as $attached_code): ?>
                        <?php 
                        foreach ($_SESSION['subject_data'] as $subject):
                            if ($subject['subject_code'] === $attached_code): ?>
                                <tr>
                                    <td><?= htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>
                                        <a href="detach-subject.php?student_id=<?= urlencode($student_id) ?>&subject_code=<?= urlencode($attached_code) ?>" class="btn btn-danger btn-sm">Detach Subject</a>
                                    </td>
                                </tr>
                            <?php endif; 
                        endforeach; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No subjects attached.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>