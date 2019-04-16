<?php require_once('includes/pre-processing.php'); ?>
<?php require_once('login/auth.php'); ?>
<?php require_once('includes/head.php'); ?>
	
<?php

	///////////////////////////////////////////////////////////////////////////////
	
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["file"]["name"]);
		$extension = end($temp);
		
		if
		(
			(
				($_FILES["file"]["type"] == "image/gif")	||
				($_FILES["file"]["type"] == "image/jpeg")	||
				($_FILES["file"]["type"] == "image/jpg")	||
				($_FILES["file"]["type"] == "image/pjpeg")	||
				($_FILES["file"]["type"] == "image/x-png")	||
				($_FILES["file"]["type"] == "image/png")
			)
			&&
			($_FILES["file"]["size"] < 20000)
			&&
			in_array($extension, $allowedExts)
		)
		{
			if ($_FILES["file"]["error"] > 0)
			{
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				echo "Upload: " . $_FILES["file"]["name"] . "<br>";
				echo "Type: " . $_FILES["file"]["type"] . "<br>";
				echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
				echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
		
				if (file_exists("images_model/" . $_FILES["file"]["name"]))
				{
					echo $_FILES["file"]["name"] . " already exists. ";
				}
				else
				{
					move_uploaded_file($_FILES["file"]["tmp_name"],
					"upload/" . $_FILES["file"]["name"]);
					echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
				}
			}
		}
		else
		{
			echo "Invalid file";
		}
	
	///////////////////////////////////////////////////////////////////////////////

	if ( isset ( $_POST['submit'] ) )
	{
		// UPLOADS ERROR ARRAY
		
			$upload_errors = array
			(							// http://www.php.net/manual/en/features.file-upload.errors.php
				UPLOAD_ERR_OK			=> "No errors.",
				UPLOAD_ERR_INI_SIZE		=> "Larger than upload_max_filesize.",
				UPLOAD_ERR_FORM_SIZE	=> "Larger than form MAX_FILE_SIZE.",
				UPLOAD_ERR_PARTIAL		=> "Partial upload.",
				UPLOAD_ERR_NO_FILE		=> "No file was selected.",
				UPLOAD_ERR_NO_TMP_DIR	=> "No temporary directory.",
				UPLOAD_ERR_CANT_WRITE	=> "Can't write to disk.",
				UPLOAD_ERR_EXTENSION	=> "File upload stopped by extension."
			);

		// PROCESS FORM DATA
		
			$tmp_file = $_FILES['file_upload']['tmp_name'];					// Save file into temp var.
			$target_file_orig = basename($_FILES['file_upload']['name']);	// "basename()" prevents hack, returns just filename and extension.
			$file_ext = substr(strrchr($target_file_orig,'.'),1);
			$upload_dir = "images_model";									// Upload location.
			
		// GET LAST ID_Picture FROM ALL PICTURES FROM ALL MODELS
		
			$query =
			"
				SELECT 
					ID_Picture 
				FROM 
					tbl_photos_reference 
				ORDER BY 
					ID_Picture DESC 
				LIMIT 
					1 
			";
			
			$result = mysql_query ( $query , $con );
			$row = mysql_fetch_array ( $result );
			
			if ( ! $row )
			{
				// tbl_photos_reference TABLE EMPTY, INITIALIZE TABLE PLACEHOLDER
					$All_Model_Last_Pic_ID = 100000000000;
			}
			else
			{
				$All_Model_Last_Pic_ID = $row [ 'ID_Picture' ];
			}

		// GET TOTAL NUMBER OF PHOTOS STORED BY MODEL
		
			$query =
			"
				SELECT
					*
				FROM
					tbl_photos_reference
				WHERE
					ID_Model=" . $_SESSION['SESS_MEMBER_ID'] . "
				ORDER BY 
					Model_Pic_Num DESC
				LIMIT 
					1
			";
			
			$result = mysql_query ( $query , $con );
			$row = mysql_fetch_array ( $result );
			
			if ( ! $row )
			{
				// MODEL HAS NO PICTURES
					$This_Model_Last_Pic_ID = 100000;
			}
			else
			{
				$This_Model_Last_Pic_ID = $row [ 'Model_Pic_Num' ];
			}
			
		// CREATE NEW FILE NAME FOR PHOTO

			$Scrambler = rand ( 100000 , 999999 );
			$This_Model_New_Pic_ID = $This_Model_Last_Pic_ID + 1;
			$All_Model_New_Pic_ID = $All_Model_Last_Pic_ID + 1;
			
			$target_file_new = 
				$_SESSION['SESS_MEMBER_ID']	. "-" .
				$This_Model_New_Pic_ID 		. "-" .
				$All_Model_New_Pic_ID 		. "-" .
				$Scrambler 					. "-" .
				$_SESSION['SESS_LOGIN'] 	. 
				"." 						. 
				$file_ext;
			;
			
		// UPLOAD AND REPORT SUCCESS OR ERROR
		
			if ( move_uploaded_file ( $tmp_file , $upload_dir . '/' . $target_file_new ) )
			{
				$_SESSION["message"] = "\"" . $target_file_orig . "\" file uploaded successfully!";
				$file_upload_success = TRUE;
			}
			else
			{
				$error = $_FILES['file_upload']['error'];
				$_SESSION["message"] = $upload_errors[$error];
				$file_upload_success = FALSE;
			}
			
		// LOG NEW PHOTO IN PHOTOS REFERENCE DATABASE
		
			if ( $file_upload_success == TRUE )
			{
				$query = 
				"
					INSERT INTO 
						tbl_photos_reference 
							(
								ID_Picture,
								ID_Model,
								Model_Pic_Num,
								Scrambler_Num,
								Model_Name,
								Downloads,
								Impressions_Self,
								Impressions_Competition,
								Picture_Original_Name,
								New_Pic_Name,
								Rating,
								Score
							)
					VALUES 
							(
								" . $All_Model_New_Pic_ID . ",
								" . $_SESSION['SESS_MEMBER_ID'] . ",
								" . $This_Model_New_Pic_ID . ",
								" . $Scrambler . ",
								'" . $_SESSION['SESS_LOGIN'] . "',
								0,
								0,
								0,
								'" . $target_file_orig . "',
								'" . $target_file_new . "',
								0,
								0
							)
				";
				
				$result = mysql_query ( $query , $con );
				
				if ( ! $result )
				{
					echo "ERROR: Photo data database insert failed.<br/>";
				}
			}
			
	}
	else
	{
		$_SESSION["message"] = "File not permitted.";
	}
	
	// RETURN TO UPLOAD FORM
	
		header("Location: index-1-upload-form.php");
		exit();
		
?>

<?php require_once("includes/post-processing.php"); ?>
