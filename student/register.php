<?php
session_start();
$pageTitle = "Register Student";
include '../header.php';
include '../functions.php';

// Redirects to login if user is not authenticated
if (empty($_SESSION['email'])) {
    header("Location: ../index.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Session handling and page guard functions
checkUserSessionIsActive();
guard();

// Initialize variables
$errors = [];
$student_data = ['student_id' => '', 'first_name' => '', 'last_name' => ''];

// Initialize session data if not set
if (!isset($_SESSION['student_data'])) {
    $_SESSION['student_data'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_data = [
        'student_id' => $_POST['student_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ];

    // Validate form input and check for duplicate student ID
    $errors = validateStudentData($student_data);
    if (empty($errors) && getSelectedStudentIndex($student_data['student_id']) !== null) {
        $errors[] = "Duplicate Student ID.";
    }

    // If no errors, add student data to session and reload page
    if (empty($errors)) {
        $_SESSION['student_data'][] = $student_data;
        header("Location: register.php");
        exit;
    }
}
?>

<style>
    .container {
        max-width: 800px;
    }
    .form-group label {
        font-weight: bold;
    }
    .form-control, .btn {
        border-radius: 8px;
    }
    .btn-primary {
        background-color: #0069d9;
        border-color: #0062cc;
    }
    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-weight: bold;
    }
    .breadcrumb-item a {
        color: #007bff;
    }
</style>

<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="text-center">Register a New Student</h2>
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Student</li>
            </ol>
        </nav>
        <hr>
        <br>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>System Errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="register.php" method="post" class="mt-3">
            <div class="form-group mb-3">
                <label for="student_id">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_data['student_id']); ?>" placeholder="Enter Student ID" required>
            </div>
            <div class="form-group mb-3">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_data['first_name']); ?>" placeholder="Enter First Name" required>
            </div>
            <div class="form-group mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_data['last_name']); ?>" placeholder="Enter Last Name" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Add Student</button>
        </form>
    </div>
    <hr class="my-5">

    <h3 class="text-center mt-5">Student List</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['student_data'])): ?>
                    <?php foreach ($_SESSION['student_data'] as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td>
                                <a href="edit.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-info btn-sm">Edit</a>
                                <a href="delete.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                <a href="attach-subject.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>
