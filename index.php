<?php
session_start(); 
$pageTitle = "Log In";

if (!empty($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit;
}

include 'functions.php'; 
include 'header.php';

$errors = [];
$notification = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['txtEmail'] ?? '';
    $password = $_POST['txtPassword'] ?? '';

    $errors = validateLoginCredentials($email, $password);

    if (empty($errors)) {
        $users = getUsers();
        if (checkLoginCredentials($email, $password, $users)) {
            $_SESSION['email'] = $email;
            $_SESSION['current_page'] = 'dashboard.php'; 
            header("Location: dashboard.php");
            exit;
        } else {
            // Updated notification message
            $notification = "<li>Invalid username or password.</li>";
        }
    } else {
        $notification = displayErrors($errors);
    }
}
?>

<?php include('header.php'); ?>

    <br>
    <div class="container" style="width: 500px; background-color: whitesmoke; padding: 20px; border-radius:20px;">
        <?php if (!empty($notification)): ?>
            <div class="col-md-4 mb-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>System Errors</strong>
                    <?php echo $notification; ?>
                    <button type="button" class="btn-close " data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?> 
        <form method="post">
            <div class="text-center">
                <h1>Login</h1> <br><hr>
            </div>
            <div class="mb-3">
                <label for="txtEmail" class="form-label">Email address</label>
                <input type="text" class="form-control" id="txtEmail" name="txtEmail">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" id="txtPassword" name="txtPassword"><br>
            </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Submit</button>
            </div>
        </form>
    </div>
        
<?php include('footer.php'); ?>