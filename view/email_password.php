<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        <?php include '../assets/css/forgotpass.css'; ?>
    </style>
</head>
<body>
	<div class="row">
		<h1>Forgot Password</h1>
		<h6 class="information-text">Enter your registered email to reset your password.</h6>
		<form action="" method="POST">
            <div class="form-group">
                <input type="email" name="email" id="user_email" required>
                <label for="user_email">Email</label>
                <button type="submit" onclick="showSpinner()">Reset Password</button>
            </div>
        </form>
		<div class="footer">
			<h5>New here? <a href="signup.php">Sign Up.</a></h5>
			<h5>Already have an account? <a href="login.php">Sign In.</a></h5>
			<p class="information-text"></a></p>
		</div>
	</div>
</body>