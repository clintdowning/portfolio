<?

class User {
	
	public $id;
	
	public $greeting;
	
	private $facebook_id;
	private $email;
	private $password_hashed;
	
	public static $logged_in = FALSE;
	public $found = NULL;
	
	public $collection;  // Collection object.
	
	function __construct ( $id ) {
		global $url;
		if ( $url->action == 'sign_out' ) {
			self::signOut();
		} else {
			if ( $id ) {
				$this->id = $id;
				$this->found = TRUE;
				$this->setSession();
				$this->open();
				$this->greeting = $this->setGreeting();
				$this->login();
				$this->collection = new Collection($this->id);
			} else {
				$this->found = FALSE;
			}
		}
	}
	
	public static function emailExists ( $email ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "id ";
		$sql .= "FROM ";
		$sql .= "user ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $email . "' ";
		$result = $database->query($sql);
		$user_id = $result[0]['id'];
		$email_exists = ( $user_id ) ? 'true' : 'false' ;
		return $email_exists;
	}
	
	private static function calcHashedPassword ( $password ) {
		$hashed_password = password_hash ( $password , PASSWORD_BCRYPT , [ 'cost' => 10 ] );
		return $hashed_password;
	}
	
	private function login (  ) {
		self::$logged_in = TRUE;
	}
	
	private function open (  ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "facebook_id, ";
		$sql .= "email, ";
		$sql .= "password_hashed ";
		$sql .= "FROM ";
		$sql .= "user ";
		$sql .= "WHERE ";
		$sql .= "id = " . $this->id . " ";
		$result = $database->query($sql);
		$this->facebook_id = $result[0]['facebook_id'];
		$this->email = $result[0]['email'];
		$this->password_hashed = $result[0]['password_hashed'];
	}

	private static function getIP (  ) {
		$ip = ( isset ( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : NULL ;
		return $ip;
	}
	
	public static function getOrSet ( $type ) {
		global $database;
		global $form;
		if ( $type == 'native' ) {
			if ( $form->fields['submit'] == 'sign_up' ) {
				$sql  = "SELECT ";
				$sql .= "id ";
				$sql .= "FROM ";
				$sql .= "user ";
				$sql .= "WHERE ";
				$sql .= "email = '" . $database->escape_value($form->fields['email']) . "' ";
				$result = $database->query($sql);
				if ( ! $result ) {
					$id = self::save($type);
				}
			} elseif ( $form->fields['submit'] == 'sign_in' ) {
				$sql  = "SELECT ";
				$sql .= "id, ";
				$sql .= "password_hashed ";
				$sql .= "FROM ";
				$sql .= "user ";
				$sql .= "WHERE ";
				$sql .= "email = '" . $database->escape_value($form->fields['email']) . "' ";
				$result = $database->query($sql);
				if ( $result ) {  // User found.
					$id = $result[0]['id'];
					$password_hashed = $result[0]['password_hashed'];
					if ( password_verify ( $form->fields['password'] , $password_hashed ) ) {
						
					} else {
						
					}
				} else {  // No User Found
					
				}
			}
		} elseif ( $type == 'facebook' ) {
			global $facebook;
			$sql  = "SELECT ";
			$sql .= "id ";
			$sql .= "FROM ";
			$sql .= "user ";
			$sql .= "WHERE ";
			$sql .= "facebook_id = " . $database->escape_value($facebook->profile['id']) . " ";
			$result = $database->query($sql);
			if ( $result ) {
				$id = $result[0]['id'];
			} else {
				$id = self::save($type);
			}
		}
		$user = new User($id);
		if ( $user->found === FALSE ) {
			// LEFTOFF - 180322_2144 - Build modal class with post executing js for modal display.
		}
		return $user;
	}
	
	private static function found ( $is ) {
		$_SESSION['user_found'] = $is;
	}
	
	private static function save ( $type ) {
		global $database;
		if ( $type == 'native' ) {
			global $form;
			$sql  = "INSERT INTO ";
			$sql .= "user ";
			$sql .= "( ";
			$sql .= "email, ";
			$sql .= "password_hashed, ";
			$sql .= "created_datetime, ";
			$sql .= "created_ip ";
			$sql .= ") ";
			$sql .= "VALUES ";
			$sql .= "( ";
			$sql .= "'" . $database->escape_value($form->fields['email']) . "', ";
			$sql .= "'" . $database->escape_value( User::calcHashedPassword ( $form->fields['password'] ) ) . "', ";
			$sql .= "'" . $database->escape_value(DateTimeMod::formattedDateTime( 'mysql' , NULL )) . "', ";
			$sql .= "'" . $database->escape_value(self::getIP()) . "' ";
			$sql .= ") ";
			$result = $database->query($sql);
		} elseif ( $type == 'facebook' ) {
			global $facebook;	
			$sql  = "INSERT INTO ";
			$sql .= "user ";
			$sql .= "( ";
			$sql .= "facebook_id, ";
			$sql .= "created_datetime, ";
			$sql .= "created_ip ";
			$sql .= ") ";
			$sql .= "VALUES ";
			$sql .= "( ";
			$sql .= $database->escape_value($facebook->profile['id']) . ", ";
			$sql .= "'" . $database->escape_value(DateTimeMod::formattedDateTime( 'mysql' , NULL )) . "', ";
			$sql .= "'" . $database->escape_value(self::getIP()) . "' ";
			$sql .= ") ";
			$result = $database->query($sql);
		}
		$id = ( $result ) ? $database->insert_id() : NULL ;
		return $id;
	}
	
	private function setGreeting (  ) {
		global $facebook;
		$greeting;
		$greeting = $facebook->first_name ? $facebook->first_name . "'s" : "Your" ;
		return $greeting;
	}
	
	private function setSession (  ) {
		$_SESSION['user_id'] = $this->id;
	}
	
	private static function signOut (  ) {
		self::$logged_in = FALSE;
		unset ( $_SESSION['user_id'] );
	}

}

?>
