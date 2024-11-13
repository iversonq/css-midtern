<?php
session_start();
$pageTitle = "Edit Student";
include '../header.php';
include '../functions.php';

if (empty($_SESSION['email'])) {
    header("Location: ../index.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

checkUserSessionIsActive();
guard();

$errors = [];
$studentToEdit = null;
$studentIndex = null;

if (isset($_REQUEST['student_id'])) {
    $student_id = $_REQUEST['student_id'];

    foreach ($_SESSION['student_data'] as $key => $student) {
        if ($student['student_id'] === $student_id) {
            $studentToEdit = $student;
            $studentIndex = $key;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $updatedData = [
        'student_id' => $_POST['student_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ];

    if (empty($updatedData['first_name'])) {
        $errors[] = "First Name is required";
    }

    if (empty($updatedData['last_name'])) {
        $errors[] = "Last Name is required";
    }

    if (empty($errors)) {
        $_SESSION['student_data'][$studentIndex] = $updatedData;
        header("Location: register.php");
        exit;
    }
}
?>

<style>
    .container {
        max-width: 600px;
    }
    .card {
        border: 1px solid #e3e3e3;
        border-radius: 8px;
    }
    .btn-primary {
        width: 100%;
        font-weight: bold;
    }
</style>

<div class="container mt-5">
    <h2 class="text-center text-primary">Edit Student Details</h2>
    <nav aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

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

    <?php if ($studentToEdit): ?>
        <div class="card shadow-sm p-4">
            <div class="card-body">
                <form action="edit.php?student_id=<?= urlencode($studentToEdit['student_id']) ?>" method="post">
                    <div class="form-group mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($studentToEdit['student_id']) ?>" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($studentToEdit['first_name']) ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($studentToEdit['last_name']) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Student</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="text-danger text-center">Student record not found.</p>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>