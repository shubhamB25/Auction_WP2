<?php
session_start();
include('templates/header.php');
require 'templates/db.php';
$sql = "SELECT * FROM users WHERE email= ? ";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
  header("Location: http://localhost/WP2_Auction/Auction/edit_acoount.php?error=sqlerror");
  exit();
}
else {
  mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);
}
$fname = explode(" ",$row['name'])[0];
$lname = explode(" ",$row['name'])[1];
$email = $row['email'];
$password = $confirm_password = '';
$errors = array('fname' => '', 'lname' => '', 'password' => '', 'confirm_password' => '', 'profile_pic' => '');

if(isset($_POST['submit'])){

	if(empty($_POST['fname'])){
		$errors['fname'] = 'First Name is required';
	} else{
		$fname = $_POST['fname'];
		if(!preg_match("/^([a-zA-Z']+)$/",$_POST['fname'])){
      $errors['fname'] = 'First Name must be valid';
    }
	}

  if(empty($_POST['lname'])){
		$errors['lname'] = 'Last Name is required';
	} else{
		$lname = $_POST['lname'];
		if(!preg_match("/^([a-zA-Z']+)$/",$_POST['lname'])){
      $errors['lname'] = 'Last Name must be valid';
    }
	}

  if (empty($_POST["password"])){
    $errors['password'] = 'Password is required';
  }
  else {
    $password=  $_POST['password'];
  }

  if (empty($_POST["confirm_password"])){
    $errors['confirm_password'] = 'Confirm password is required';
  }
  else {
    $confirm_password=  $_POST['confirm_password'];
    if($_POST['password']!=$_POST['confirm_password']){
      $errors['confirm_password'] = 'Confirm password should match Password';
    }
  }

	do {
		if(isset($_FILES['profile_pic'])){
      $file_name = $_FILES['profile_pic']['name'];
      $file_size = $_FILES['profile_pic']['size'];
      $file_tmp = $_FILES['profile_pic']['tmp_name'];
      $file_type = $_FILES['profile_pic']['type'];
      $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
      $expensions= array("jpeg","jpg","png");

			if($file_name==''){
				break;
			}

      if(in_array($file_ext,$expensions)=== false){
         $errors['profile_pic']="extension not allowed, please choose a JPEG or PNG file.";
      }

      if($file_size > 2097152) {
         $errors['profile_pic']='File size must be excately 2 MB';
      }
			break;
   	}
	 }while(1);

  if(!array_filter($errors)) {

		$name = $fname . " " . $lname;
		if($file_name=="") {
			$img_dir = "images/default.png";
		}
		else {
			$img_dir = "images/$email.$file_name";
		}
		$sql = "UPDATE users SET name = ?, email = ?, password = ?, profile_pic = ? WHERE email = ?";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: http://localhost/WP2_Auction/Auction/edit_account.php?error=sqlerror");
			exit();
		}
		else {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);

			mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed_password, $img_dir, $_SESSION['email']);
			mysqli_stmt_execute($stmt);
			if ($file_name != "") {
				move_uploaded_file($file_tmp, $img_dir);	
			}
		}
		$sql = "UPDATE bids SET name = ? WHERE name = ?";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: http://localhost/WP2_Auction/Auction/edit_account.php?error=sqlerror");
			exit();
		}
		else {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);

			mysqli_stmt_bind_param($stmt, "ss", $name, $_SESSION['name']);
			mysqli_stmt_execute($stmt);
		}
		$_SESSION['email'] = $email ; 
		$_SESSION['user_image'] = $img_dir;
		$_SESSION['name'] = $name; 
		$_SESSION['profile_pic'] = $img_dir;
		mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header('Location: home.php');
	}
}

?>

<!DOCTYPE html>
<html>
	<style media="screen">
	.custom-file-input::-webkit-file-upload-button {
		visibility: hidden;
		}
	.custom-file-input::before {
		content: 'Upload pic';
		display: inline-block;
		background: linear-gradient(top, #f9f9f9, #e3e3e3);
		border: 1px solid #999;
		border-radius: 3px;
		padding: 5px 8px;
		outline: none;
		white-space: nowrap;
		-webkit-user-select: none;
		cursor: pointer;
		text-shadow: 1px 1px #fff;
		font-weight: 700;
		font-size: 10pt;
		}
	.custom-file-input:hover::before {
		border-color: black;
		}
	.custom-file-input:active::before {
		background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
		}
	</style>

	<section class="container grey-text">
		<h4 class="center">Enter your details</h4>
		<form class="white" action="edit_account.php" method="POST" enctype="multipart/form-data">
			<label>First Name</label>
			<input type="text" name="fname" value="<?php echo htmlspecialchars($fname) ?>">
      <div class="red-text"><?php echo $errors['fname']; ?></div>
			<label>Last Name</label>
			<input type="text" name="lname" value="<?php echo htmlspecialchars($lname) ?>">
      <div class="red-text"><?php echo $errors['lname']; ?></div>
			<label>Email</label>
			<input type="email" name="email" disabled value="<?php echo htmlspecialchars($email) ?>">
      <label>Password</label>
			<input type="password" name="password" value="<?php echo htmlspecialchars($password) ?>">
      <div class="red-text"><?php echo $errors['password']; ?></div>
			<label>Confirm Password</label>
			<input type="password" name="confirm_password" value="<?php echo htmlspecialchars($confirm_password) ?>">
      <div class="red-text"><?php echo $errors['confirm_password']; ?></div>
			<label>Profile Pic</label>
			<input type="file" class="custom-file-input" name="profile_pic", id="profile_pic">
			<div class="red-text"><?php echo $errors['profile_pic']; ?></div><br>
			<div class="center">
				<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0"><br><br>
	    </div>
		</form>
	</section>

	<?php include('templates/footer.php'); ?>

</html>
