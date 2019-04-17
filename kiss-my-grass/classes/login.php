<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/main_tag_includes/html_pre.php';

$stored_username = "vitalpilot";
$stored_password = "10pilots";

$title = "Login - Admin - Kiss-My-Grass";

$logged_in = ( isset ( $_SESSION['logged_in'] ) ) ? $_SESSION['logged_in'] : FALSE;

if ( $logged_in ) {
	General::redirect_to('index.php');
}

$submitted = ( isset ( $_POST['submit'] ) ) ? TRUE : FALSE;

if ( $submitted ) {
	$entered_username = ( isset ( $_POST['username'] ) ) ? $_POST['username'] : NULL;
	$entered_password = ( isset ( $_POST['password'] ) ) ? $_POST['password'] : NULL;
	if ( $entered_username == $stored_username && $entered_password == $stored_password ) {
		$_SESSION['logged_in'] = TRUE;
		General::redirect_to('index.php');
	} else {
		$message = "Incorrect username or password.";
	}
}
	
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo $title; ?></title>
</head>
<body>
	<h1><? echo $title; ?></h1>
	<h2>Login:</h2>
	<h3><? echo $message; ?></h3>
	<form action="login.php" method="post" >
		Username:<br>
		<input type="text" name="username"><br>
		Password:<br>
		<input type="password" name="password"><br>
		<input type="submit" name="submit" value="Submit">
	</form>
</body>
</html>
