<?

class Login {
	
	public $logged_in = FALSE;
	public $user_id;
	public $email = "";
	public $user_type = "";
	public $error;
	public $link;
	
	function __construct ( ) {
		global $dev;
		$dev->path['login'] .= "1";
		global $database;
		if ( $_POST['submit'] == 'Sign In' ) {
			$dev->path['login'] .= "2";
			$this->signIn();
		} elseif ( isset ( $_SESSION['login'] ) ) {
			$dev->path['login'] .= "3";
			if ( $_SESSION['login']['logged_in'] == TRUE ) {  // Logged in.
				$dev->path['login'] .= "4";
				$this->user_id = $_SESSION['login']['user_id'];
				$this->user_type = $_SESSION['login']['user_type'];
				$this->logged_in = TRUE;
				$this->email = $_SESSION['login']['email'];
			} else {  // Not logged in.
				$dev->path['login'] .= "5";
				$this->user_id = "";
				$this->logged_in = FALSE;
				$this->email = "";
				$this->user_type = "";
			}
		} else {
			$dev->path['login'] .= "6";
			$this->user_id = "";
			$this->user_type = "";
			$this->logged_in = FALSE;
			$this->email = "";
		}
		$dev->path['login'] .= "7";
		$this->buildLoginLink();
	}
	
	function buildLoginLink (  ) {
		$this->link = "<a href=\"";
		if ( $this->logged_in ) {
			$this->link .= "/index.php?page=login&action=logout\">Sign Out</a>";
		} elseif ( ! $this->logged_in ) {
			$this->link .= "/index.php?page=login\">Sign In</a>";
		}
	}
	
	function signInAfterSignUp ( $user_id , $user_type ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= $database->escape_value($user_type) . "s ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($_SESSION['interview']['email']) . "' ";
		$sql .= "AND ";
		$sql .= "password = '" . $database->escape_value($_SESSION['interview']['password']) . "' ";
		$sql .= "ORDER BY ";
		$sql .= "added ";
		$sql .= "DESC ";
		$sql .= "LIMIT 1 ";
		$result = $database->query($sql);
		$this->activateSessionLogin ( $result[0][$user_type . "_id"] , $user_type , $result[0]['email'] );
	}
	
	function signIn (  ) {
		global $database;
		$user_types = array( "provider" , "client" );
		$posted = $_POST;
		for ( $i = 0 ; $i < count ( $user_types ) ; $i++ ) {
			$sql  = "SELECT ";
			$sql .= "* ";
			$sql .= "FROM ";
			$sql .= $database->escape_value($user_types[$i]) . "s ";
			$sql .= "WHERE ";
			$sql .= "email = '" . $database->escape_value($posted['email']) . "' ";
			$sql .= "AND ";
			$sql .= "password = '" . $database->escape_value($posted['password']) . "' ";
			$sql .= "ORDER BY ";
			$sql .= "added ";
			$sql .= "DESC ";
			$sql .= "LIMIT 1 ";
			$result = $database->query($sql);
			if ( count ( $result ) == 1 ) {
				$this->activateSessionLogin ( $result[0][$user_types[$i] . "_id"] , $user_types[$i] , $result[0]['email'] );
				break;
			}
		}
	}
	
	function activateSessionLogin ( $user_id , $user_type , $user_email ) {
		$_SESSION['login']['user_id'] = $this->user_id = $user_id;
		$_SESSION['login']['user_type'] = $this->user_type = $user_type;
		$_SESSION['login']['logged_in'] = $this->logged_in = TRUE;
		$_SESSION['login']['email'] = $this->email = $user_email;
	}
	
	function signOut (  ) {
		global $page;
		$_SESSION['login'] = NULL;
		$page->message = "You have been logged out!";
		General::redirect_to( "/index.php?page=login" );
	}
	
	public static function requireLogin (  ) {
		if ( $login->logged_in == FALSE ) {
			General::redirect_to("/index.php?page=login");
		}
	}
	
}

?>
