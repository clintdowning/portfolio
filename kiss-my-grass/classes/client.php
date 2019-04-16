<?

class Client {
	
	public $client_id;

	public $name_first;
	public $name_last;
	public $name_first_last;

	public $email;
	public $password;
	
	public $email_username;
	
	public $name_or_username;
	
	public $phone;

	public $street_1;
	public $street_2;
	public $city;
	public $state;
	public $zip;
	
	public $frequency;
	
	public $services;  // Object
	public $reviews;  // Object
	
	public $account_status;
	public $interview_completed;  // Number of times interview completed.
	
	private $subscription_id;
	public $subscription_status;
	
	public $type;  // Basic or full. Basic is email only, Full is client completed full interview.
	
	public $ip_address;
	public $referrer;
	
	function __construct ( $client_id ) {
		$this->client_id = $client_id;
		$this->open();
		$this->checkForm();
		$this->services = new Services ( $this->client_id , "client" );
		$this->type = $this->getType();
		$this->updateSession();
	}
	
	public static function add ( $client_type = 'full' , $client_email = NULL ) {  // Add client to database.
		global $database;
		global $page;
		global $session;
		if ( $client_type == 'basic' ) {
			$sql  = "INSERT INTO ";
			$sql .= "clients ";
			$sql .= "( ";
			$sql .= "email, ";
			$sql .= "added, ";
			$sql .= "ip_address, ";
			$sql .= "referrer ";
			$sql .= ") ";
			$sql .= "VALUES ";
			$sql .= "( '";
			$sql .= $database->escape_value($client_email) . "', '";
			$sql .= $database->escape_value(General::mysql_time_stamp()) . "', '";
			$sql .= $database->escape_value(Server::getClientIP()) . "', '";
			$sql .= $database->escape_value($session->key_vals['referrer']) . "' ";
			$sql .= ") ";
		} elseif ( $client_type == 'full' ) {
			$injects = array();
			$escaped_email = $database->escape_value($_SESSION['interview']['email']);
			if ( self::emailExists( $escaped_email ) && $escaped_email != NULL ) {  // Verify email not already exists.
				$injects['email'] = $escaped_email;
				$page->setMessage ( "email_exists" , $injects );
				General::redirect_to("index.php?page=interview-client&action=edit");
			}
			$sql  = "INSERT INTO ";
			$sql .= "clients ";
			$sql .= "( ";
			$sql .= "name_first, ";
			$sql .= "name_last, ";
			$sql .= "email, ";
			$sql .= "password, ";
			$sql .= "phone, ";
			$sql .= "street_1, ";
			$sql .= "street_2, ";
			$sql .= "city, ";
			$sql .= "state, ";
			$sql .= "zip, ";
			$sql .= "added, ";
			$sql .= "ip_address, ";
			$sql .= "referrer ";
			$sql .= ") ";
			$sql .= "VALUES ";
			$sql .= "( '";
			$sql .= $database->escape_value($_SESSION['interview']['name_first']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['name_last']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['email']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['password']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['phone']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['street_1']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['street_2']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['city']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['state']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['zip']) . "', '";
			$sql .= $database->escape_value(General::mysql_time_stamp()) . "', '";
			$sql .= $database->escape_value(Server::getClientIP()) . "', '";
			$sql .= $database->escape_value($session->key_vals['referrer']) . "' ";
			$sql .= ") ";
		}
		$result = $database->query($sql);
		if ( $database->affected_rows() == 1 ) {
			$new_client_created = TRUE;
			$new_client_id = self::getID("new");
			Services::add( $new_client_id , "client" );
		} else {
			$new_client_created = FALSE;
		}
		return $new_client_id;
	}
	
	public function buildClientData ( $version = 'public' ) {
		global $pages;
		if ( $version == 'public' ) {
			$class = 'public';
			$h1 = 'Customer Profile';
			$h2 = $pages['view-client'];
		} elseif ( $version == 'admin' ) {
			$class = 'admin';
			$h1 = 'Your Profile';
			$h2 = 'Contact Information';
		} else {
			?><h1>Invalid buildClientData version variable.</h1><?
			die();
		}
		
		?>
			<div class="client_data" >
				<h1><? echo $h1; ?>
					<? if ( $version == 'admin' ) { ?>
						<a class="edit" title="Edit Profile" href="/index.php?page=interview-client&step=1_contact" >
							<img width="20" height="20" alt="Edit" src="/assets/images/custom/pencil_white.png" />
						</a>
					<? } ?>
				</h1>
				<div class="contact clearfix">
					<h2><? echo $h2; ?>
						<? if ( $version == 'admin' ) { ?>
							<a class="edit" title="Edit Contact Information" href="/index.php?page=interview-client&step=1_contact" >
								<img width="16" height="16" alt="Edit" src="/assets/images/custom/pencil_white.png" />
							</a>
						<? } ?>
					</h2>
					<div class="data">
						<div class="name">
							<p><span>Name: </span><span><? echo $this->showValueOrMessage($this->name_first) . " " . $this->name_last; ?></span></p>
						</div>
						<div class="email_phone">
							<p><span>Email: </span><span><? echo $this->showValueOrMessage($this->email); ?></span></p>
							<p><span>Phone: </span><span><? echo $this->showValueOrMessage($this->phone); ?></span></p>
						</div>
						<div class="address">
							<p><span>Street 1: </span><span><? echo $this->showValueOrMessage($this->street_1); ?></span></p>
							<? if ( $this->street_2 ) { ?>
								<p><span>Street 2: </span><span><? echo $this->street_2; ?></span></p>
							<? } ?>
							<p><span>City: </span><span><? echo $this->showValueOrMessage($this->city); ?></span></p>
							<p><span>State: </span><span><? echo $this->showValueOrMessage($this->state); ?></span></p>
							<p><span>Zip: </span><span><? echo $this->showValueOrMessage($this->zip); ?></span></p>
						</div>
					</div>
				</div>
				<div class="frequency">
					<h2>Service Frequency
						<? if ( $version == 'admin' ) { ?>
							<a class="edit" title="Edit Service Frequency Information" href="/index.php?page=interview-client&step=2_frequency" >
								<img width="16" height="16" alt="Edit" src="/assets/images/custom/pencil_white.png" />
							</a>
						<? } ?>
					</h2>
					<div class="data">
						<ul>
							<li><? echo $this->outputFrequencyDescription($version); ?></li>
						</ul>
					</div>
				</div>
				<div class="services">
					<h2>Services Desired
						<? if ( $version == 'admin' ) { ?>
							<a class="edit" title="Edit Services Desired Information" href="/index.php?page=interview-client&step=3_services" >
								<img width="16" height="16" alt="Edit" src="/assets/images/custom/pencil_white.png" />
							</a>
						<? } ?>
					</h2>
					<div class="data">
						<ul>
							<?
								if ( count ( $this->services->services ) == 0 ) {
									?><li>No services selected yet. 
										<? if ( $version == 'admin' ) { ?>
											Please <a href="/index.php?page=interview-client&step=3_services" >Click Here</a> to add services.
										<? } ?>
									</li><?
								} else {
									for ( $i = 0 ; $i < count ( $this->services->services ) ; $i++ ) {
										?><li><? echo $this->services->services[$i]->description; ?></li><?
									}
								}
							?>
						</ul>
					</div>
				</div>
				<div class="reviews">
					<h2>Reviews</h2>
					<div class="data">
						<? if ( count ( $this->reviews ) ) { ?>
							<ul>
								<? for ( $i = 0 ; $i < count ( $this->reviews ) ; $i++ ) { ?>
									<li><? echo ""; ?></li>
								<? } ?>
							</ul>
						<?
							} else {
								echo "<p>No reviews... Yet!</p>";
							}
						?>
					</div>
				</div>
			</div>
		<?
	}

	private function checkForm (  ) { // Check form object for form data from last page.
		global $form;
		if ( $form->form_vars_size ) {
			$this->updateExisting();
			if ( Services::postDataExists() ) {
				$this->updateServices();
			}
		}
	}

	function emailExists ( $escaped_email ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= "clients ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($escaped_email) . "' ";
		$result = $database->query($sql);
		return ( $result ) ? TRUE : FALSE;
	}
	
	private function findEmailUsername (  ) {
		$atsign_pos = strpos ( $this->email , "@" );
		$username_length = $atsign_pos;
		$email_username = substr ( $this->email , 0 , $username_length );
		return $email_username;
	}

	public static function foreignEmailExists ( $email , $client_id ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= "clients ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($email) . "' ";
		$sql .= "AND ";
		$sql .= "client_id <> '" . $database->escape_value($client_id) . "' ";
		$result = $database->query($sql);
		return ( $result ) ? TRUE : FALSE;
	}
	
	public static function getID ( $type = "existing" , $email_in = NULL ) {
		global $database;
		switch ( $type ) {
			case 'existing':
				$email = $this->email;
				break;
			case 'basic':
				$email = $email_in;
				break;
			case 'new':
			default:
				$email = $_SESSION['interview']['email'];
		}
		$sql  = "SELECT ";
		$sql .= "client_id ";
		$sql .= "FROM ";
		$sql .= "clients ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($email) . "' ";
		$sql .= "ORDER BY ";
		$sql .= "client_id DESC ";
		$sql .= "LIMIT 1 ";
		$result = $database->query($sql);
		$id = $result[0]['client_id'];
		return $id;
	}
	
	private function getType (  ) {  // Determines client type as Basic or full. Basic is email only, Full is client completed full interview.
		$client_type = ( $this->street_1 == NULL ) ? 'basic' : 'full';
		return $client_type;
	}
	
	private function hasAttribute ( $attribute ) {
		$object_attribute_value_pairs = get_object_vars ( $this );
		$key_exists = array_key_exists ( $attribute , $object_attribute_value_pairs );
		return $key_exists;
	}

	private function buildNameOrUsername (  ) {
		$name_or_username = ( $this->name_first == NULL && $this->name_last == NULL ) ? $this->email_username : $this->name_first_last;
		return $name_or_username;
	}

	private function open (  ) {  // Open client object from database record.
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= "clients ";
		$sql .= "WHERE ";
		$sql .= "client_id = " . $database->escape_value($this->client_id) . " ";
		$sql .= "LIMIT 1 ";
		$result = $database->query($sql);
		$this->name_first = $result[0]['name_first'];
		$this->name_last = $result[0]['name_last'];
		$this->name_first_last = $this->name_first . " " . $this->name_last;
		$this->email = $result[0]['email'];
		$this->password = $result[0]['password'];
		$this->email_username = $this->findEmailUsername();
		$this->name_or_username = $this->buildNameOrUsername();
		$this->phone = $result[0]['phone'];
		$this->street_1 = $result[0]['street_1'];
		$this->street_2 = $result[0]['street_2'];
		$this->city = $result[0]['city'];
		$this->state = $result[0]['state'];
		$this->zip = $result[0]['zip'];
		$this->frequency = $result[0]['frequency'];
		$this->interview_completed = $result[0]['interview_completed'];
		$this->referrer = $result[0]['referrer'];
		$this->services = new Services ( $this->client_id , 'client' );
	}

	public function outputFrequencyDescription ( $version ) {
		global $frequency_descriptions;
		if ( $this->frequency ) {
			$phrase_description = $frequency_descriptions[$version . "_" . $this->frequency];
		} else {
			$phrase_description = "No service frequency completed yet.";
		}
		return $phrase_description;
	}

	private function showValueOrMessage ( $property_value ) {
		$value_or_message = ( $property_value ) ? $property_value : "<span class=\"empty\">Not yet completed.</span>";
		return $value_or_message;
	}

	public function update (  ) {  // Old update method for client object.
		global $database;
		$sql  = "UPDATE ";
		$sql .= "clients ";
		$sql .= "SET ";
		$sql .= "name_first = '" . $database->escape_value($_SESSION['interview']['name_first']) . "', ";
		$sql .= "name_last = '" . $database->escape_value($_SESSION['interview']['name_last']) . "', ";
		$sql .= "email = '" . $database->escape_value($_SESSION['interview']['email']) . "', ";
		$sql .= "password = '" . $database->escape_value($_SESSION['interview']['password']) . "', ";
		$sql .= "phone = '" . $database->escape_value($_SESSION['interview']['phone']) . "', ";
		$sql .= "street_1 = '" . $database->escape_value($_SESSION['interview']['street_1']) . "', ";
		$sql .= "street_2 = '" . $database->escape_value($_SESSION['interview']['street_2']) . "', ";
		$sql .= "city = '" . $database->escape_value($_SESSION['interview']['city']) . "', ";
		$sql .= "state = '" . $database->escape_value($_SESSION['interview']['state']) . "', ";
		$sql .= "zip = '" . $database->escape_value($_SESSION['interview']['zip']) . "' ";
		$sql .= "WHERE ";
		$sql .= "client_id = " . $database->escape_value($this->client_id) . " ";
		$updated = $database->query($sql);
		if ( $database->affected_rows() == 1 ) {
			//Services::update($this->client_id);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function updateExisting (  ) {  // Update existing client data from form data if exists.
		global $form;
		foreach ( $form->form_vars as $attribute => $value ) {
			if ( $this->hasAttribute ( $attribute ) && ( $value != NULL ) ) {
				if ( $attribute == 'interview_completed' ) {
					$this->interview_completed++;
				} else {
					$this->$attribute = $value;
				}
				$this->updateField( $attribute );
			}
		}
	}
	
	private function updateField ( $attribute_name ) {
		global $database;
		$sql  = "UPDATE ";
		$sql .= "clients ";
		$sql .= "SET ";
		$sql .= $database->escape_value($attribute_name) . " = '" . $database->escape_value($this->$attribute_name) . "' ";
		$sql .= "WHERE ";
		$sql .= "client_id = '" . $this->client_id . "' ";
		$result = $database->query($sql);
		$successfully_updated = $result;
		return $successfully_updated;
	}
	
	private function updateServices (  ) {
		Services::delete( $this->client_id , 'client' );
		unset ( $this->services );
		Services::add( $this->client_id , 'client' );
	}

	private function updateSession (  ) {
		global $session;
		$session->saveClientID($this->client_id);
	}
	
}

?>
