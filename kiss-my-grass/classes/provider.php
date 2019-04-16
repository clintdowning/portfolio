<?

class Provider {
	
	public $provider_id;

	public $name_first;
	public $name_last;
	public $full_name;
	public $name_business;

	public $email;
	public $password;
	
	public $phone;

	public $street_1;
	public $street_2;
	public $city;
	public $state;
	public $zip;
	
	private $starting_city;
	private $starting_state;
	private $altered_city = FALSE;
	private $altered_state = FALSE;
	
	public $service_area_phrase;
	
	public $icon;
	private $icon_numeral;
	public $icon_image_src;
	
	public $services;  // Object
	public $reviews;  // Object
	
	public $quotes;
	
	public $account_status;
	public $interview_completed;  // Number of times interview completed.
	
	private $subscription_id;
	public $subscription_status;
	
	public $target_zip;
	public $target_zip_distance;
	
	private $user_type;
	
	function __construct ( $provider_id , $user_type , $target_zip = NULL /* For Clients searching for Providers. */ ) {
		$this->provider_id = $provider_id;
		$this->user_type = $user_type;
		if ( $this->user_type == 'client' ) {
			$this->target_zip = $target_zip;
		}
		$this->open();
		$this->calcMissingData();
		$this->service_area_phrase = $this->calcServiceAreaPhrase();
		$this->icon_numeral = $this->buildIconImageNumeral();
		$this->icon_image_src = $this->buildIconImageSrc();
		if ( $this->user_type == 'provider' ) {
			$this->checkForm();
		}
		$this->services = new Services ( $this->provider_id , "provider" );
		$this->target_zip_distance = $this->getTargetZipDistance();
		if ( $this->user_type == 'provider' ) {  // Quotes only needed if user is Client.
			$this->quotes = new Quotes('provider',$this->provider_id);
		}
		if ( $this->user_type == 'provider' ) {
			$this->updateSession();
		}
	}
	
	public static function add ( $provider_type = 'full' , $provider_email = NULL ) {  // Add provider to database.
		global $database;
		global $page;
		if ( $provider_type == 'full' ) {
			$injects = array();
			$escaped_email = $database->escape_value($_SESSION['interview']['email']);
			if ( self::emailExists( $escaped_email ) && $escaped_email != NULL ) {
				$injects['email'] = $escaped_email;
				$page->setMessage ( "email_exists" , $injects );
				General::redirect_to("index.php?page=interview-provider&action=edit");
			}
			$icon_assignment_number = self::assignIcon();
			$sql  = "INSERT INTO ";
			$sql .= "providers ";
			$sql .= "( ";
			$sql .= "name_first, ";
			$sql .= "name_last, ";
			$sql .= "name_business, ";
			$sql .= "email, ";
			$sql .= "password, ";
			$sql .= "phone, ";
			$sql .= "street_1, ";
			$sql .= "street_2, ";
			$sql .= "city, ";
			$sql .= "state, ";
			$sql .= "zip, ";
			$sql .= "icon, ";
			$sql .= "account_status, ";
			$sql .= "added ";
			$sql .= ") ";
			$sql .= "VALUES ";
			$sql .= "( '";
			$sql .= $database->escape_value($_SESSION['interview']['name_first']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['name_last']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['name_business']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['email']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['password']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['phone']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['street_1']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['street_2']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['city']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['state']) . "', '";
			$sql .= $database->escape_value($_SESSION['interview']['zip']) . "', ";
			$sql .= $database->escape_value(self::assignIcon()) . ", '";
			$sql .= "active" . "', '";
			$sql .= $database->escape_value(General::mysql_time_stamp()) . "' ";
			$sql .= ") ";
		}
		$result = $database->query($sql);
		if ( $database->affected_rows() == 1 ) {
			$new_provider_created = TRUE;
			$new_provider_id = self::getID("new");
			Services::add ( $new_provider_id , "provider" );
		} else {
			$new_provider_created = FALSE;
		}
		global $dev;
		$dev->debug();
		return $new_provider_id;
	}
	
	function assignIcon (  ) {
		global $database;
		global $providers_zip_radius;
		$new_provider_escaped_zip = $database->escape_value($_SESSION['interview']['zip']);
		$existing_local_providers = new Providers ( $new_provider_escaped_zip , $user_type , $providers_zip_radius );
		$total_num_icon_files = General::countNumDirectoryFiles ( $_SERVER['DOCUMENT_ROOT'] . "/assets/images/custom/icons" );
		$all_possible_icon_file_names = array();
		for ( $i = 0 ; $i < $total_num_icon_files ; $i++ ) {
			$all_possible_icon_file_names[] = $i + 1;
		}
		$usable_possible_icon_file_names = array();
		for ( $i = 0 ; $i < count ( $all_possible_icon_file_names ) ; $i++ ) {  // Create usuable icon list.
			$used = FALSE;
			for ( $j = 0 ; $j < $existing_local_providers->num_providers ; $j++ ) {
				if ( $all_possible_icon_file_names[$i] == $existing_local_providers->relevant_providers[$j]->icon ) {
					$used = TRUE;
				}
			}
			if ( $used == FALSE ) {
				$usable_possible_icon_file_names[] = $all_possible_icon_file_names[$i];
			}
		}
		if ( count ( $usable_possible_icon_file_names ) ) {  // Names Exist - Unused names exist.
			$new_icon_id = $usable_possible_icon_file_names[ rand ( 0 , count ( $usable_possible_icon_file_names ) - 1 ) ];
		} else {  // All Used - All names already being used
			$farthest_relevant_zip = $existing_local_providers->relevantZips [ count ( $existing_local_providers->relevantZips ) - 1 ]["zip"];
			for ( $i = 0 ; $i < $existing_local_providers->num_providers ; $i++ ) {  // Find Existing Provider with Farthest Zip Icon ID
				if ( $existing_local_providers->relevant_providers[$i]->zip == $farthest_relevant_zip ) {
					$new_icon_id = $existing_local_providers->relevant_providers[$i]->icon;
				}
			}
		}
		return $new_icon_id;
	}
	
	function buildIconImageHTML (  ) {
		?><img class="icon" alt="logo_<? echo $icon_num; ?>" src="<? echo $this->icon_image_src; ?>" /><?
	}
	
	function buildIconImageNumeral (  ) {
		$icon_numeral = ( $this->icon <= 9 ) ? "0" . $this->icon : $this->icon;
		return $icon_numeral;
	}
	
	function buildIconImageSrc (  ) {
		$src_directory = "/assets/images/custom/icons/";
		$image_name = "icon_" . $this->icon_numeral . ".png";
		$image_icon_src = $src_directory . $image_name;
		return $image_icon_src;
	}
	
	function buildProviderHeader (  ) { ?>
		<div class="provider_header clearfix" >
			<div class="provider_area checkbox" >
				<input type="checkbox" name="provider[]" value="<? echo $this->provider_id; ?>" checked >
			</div>
			<div class="provider_area icon_area">
				<? $this->buildIconImageHTML(); ?>
			</div>
			<div class="provider_area text_area"><?
				if ( strlen ( $this->name_business ) > 0 ) {
					?><div class="name_business"><? echo $this->name_business ?></div><?
				}
				if ( strlen ( $this->full_name ) > 0 ) {
					?><div class="name_person"><? echo $this->full_name; ?></div><?
				}
				if ( strlen ( $this->zip ) > 0 ) {
					?><div class="zip"><span>Provider Service Area:</span> <? echo $this->service_area_phrase; ?></div><?
				}
				if ( $this->target_zip_distance >= 0 ) {
					?><div class="distance"><span>Distance:</span> <? echo $this->target_zip_distance; ?> Miles</div><?
				}
				if ( $this->services->count_services > 0 ) {
					echo $this->buildServices(); 
				} ?>
			</div>
		</div><?
	}
	
	function buildServices (  ) {
		$services_list = "";
		for ( $i = 0 ; $i < $this->services->count_services ; $i++ ) {
			$services_list .= $this->services->services[$i]->description;
			$services_list .= ( $i < ( $this->services->count_services - 1 ) ) ? ", " : NULL;
		} ?>
		<div class="services">
			<p class="services_lead">Services Offered:</p>
			<p class="services_list"><? echo $services_list; ?></p>
		</div><?
	}
	
	function buildServicesFields ( $user_object = NULL ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "service_id, ";
		$sql .= "description, ";
		$sql .= "field_value, ";
		$sql .= "display, ";
		$sql .= "sort_order ";
		$sql .= "FROM ";
		$sql .= "services ";
		$sql .= "WHERE ";
		$sql .= "display = 1 ";
		$sql .= "ORDER BY ";
		$sql .= "sort_order ";
		$services = $database->query($sql);
		for ( $i = 0 ; $i < count ( $services ) ; $i++ ) {
			if ( $user_object ) {
				if ( $user_object->services->serviceExists ( $services[$i]['service_id'] ) ) {
					$checked[$i] = "checked ";
				}
			}
			?>
				<div>
					<input 
						id="service_<? echo $i; ?>" 
						type="checkbox" 
						value="<? echo $services[$i]['service_id']; ?>" 
						name="service_<? echo $i; ?>" <? echo $checked[$i]; ?> 
					>
					<label for="service_<? echo $i; ?>">
						<? echo $services[$i]['description']; ?>
					</label>
				</div>
			<?
		}
		return $result;
	}
	
	private function calcMissingData (  ) {
		$this->starting_city = $this->city;
		$this->starting_state = $this->state;
		if ( isset ( $this->city ) ) {
			if ( ! strlen ( $this->city ) > 0 ) {
				$this->city = $this->getCityStateFromZip('city');
				$this->altered_city = TRUE;
			}
		}
		if ( isset ( $this->state ) ) {
			if ( ! strlen ( $this->state ) > 0 ) {
				$this->state = $this->getCityStateFromZip('state');
				$this->altered_state = TRUE;
			}
		}
	}
	
	private function calcServiceAreaPhrase (  ) {
		$phrase = NULL;
		
		if ( isset ( $this->city ) ) {
			$show_city = ( strlen ( $this->city ) > 0 ) ? TRUE : FALSE ;
		}
		if ( isset ( $this->state ) ) {
			$show_state = ( strlen ( $this->state ) > 0 ) ? TRUE : FALSE ;
		}
		
		if ( $show_city && $show_state ) {
			$phrase = $this->city . ", " . $this->state . " " . $this->zip;
		} elseif ( $show_city && ! $show_state ) {
			$phrase = $this->city . " " . $this->zip;
		} elseif ( ! $show_city && $show_state ) {
			$phrase = $this->state . " " . $this->zip;
		} elseif ( ! $show_city && ! $show_state ) {
			$phrase = $this->zip;
		}
		
		return $phrase;
	}
	
	private function checkForm (  ) {
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
		$sql .= "providers ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($escaped_email) . "' ";
		$result = $database->query($sql);
		return ( $result ) ? TRUE : FALSE;
	}
	
	public static function foreignEmailExists ( $email , $provider_id ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= "providers ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($email) . "' ";
		$sql .= "AND ";
		$sql .= "provider_id <> '" . $database->escape_value($provider_id) . "' ";
		$result = $database->query($sql);
		return ( $result ) ? TRUE : FALSE;
	}
	
	private function getCityStateFromZip ( $type = 'city' ) {
		global $database;
		$city_state = [];
		$sql  = "SELECT ";
		$sql .= $type . " ";
		$sql .= "FROM ";
		$sql .= "geo ";
		$sql .= "WHERE ";
		$sql .= "zip = '" . $database->escape_value($this->zip) . "' ";
		$result = $database->query($sql);
		$city_state = $result[0][$type];
		return $city_state;
	}
	
	function getID ( $type = "existing" ) {
		global $database;
		$email = ( $type == "existing" ) ? $this->email : $_SESSION['interview']['email'];
		$sql  = "SELECT ";
		$sql .= "provider_id ";
		$sql .= "FROM ";
		$sql .= "providers ";
		$sql .= "WHERE ";
		$sql .= "email = '" . $database->escape_value($email) . "' ";
		$sql .= "ORDER BY ";
		$sql .= "provider_id DESC ";
		$sql .= "LIMIT 1 ";
		$result = $database->query($sql);
		$id = $result[0]['provider_id'];
		return $id;
	}
	
	function getTargetZipDistance (  ) {
		if ( $this->target_zip ) {
			$distance = new Distance ( $this->target_zip , $this->zip );
			$zip_to_zip_distance = $distance->distance;
		} else {
			$zip_to_zip_distance = NULL;
		}
		return $zip_to_zip_distance;
	}
	
	private function hasAttribute ( $attribute ) {
		$object_attribute_value_pairs = get_object_vars ( $this );
		$key_exists = array_key_exists ( $attribute , $object_attribute_value_pairs );
		return $key_exists;
	}

	private function open (  ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= "providers ";
		$sql .= "WHERE ";
		$sql .= "provider_id = " . $database->escape_value($this->provider_id) . " ";
		$sql .= "LIMIT 1 ";
		$result = $database->query($sql);
		$this->name_first = $result[0]['name_first'];
		$this->name_last = $result[0]['name_last'];
		$this->full_name = $this->name_first . " " . $this->name_last;
		$this->name_business = $result[0]['name_business'];
		$this->email = $result[0]['email'];
		$this->password = $result[0]['password'];
		$this->phone = $result[0]['phone'];
		$this->street_1 = $result[0]['street_1'];
		$this->street_2 = $result[0]['street_2'];
		$this->city = $result[0]['city'];
		$this->state = $result[0]['state'];
		$this->zip = $result[0]['zip'];
		$this->interview_completed = $result[0]['interview_completed'];
		$this->icon = $result[0]['icon'];
	}

	public static function quoteRequest ( $client_id , $provider_id ) {
		$token = new Token ( 'quote_request' );
		$quote = Quote::add( $client_id , $provider_id , $token->token_key );
	}
	
	public function update (  ) {
		global $database;
		$sql  = "UPDATE ";
		$sql .= "providers ";
		$sql .= "SET ";
		$sql .= "name_first = '" . $database->escape_value($_SESSION['interview']['name_first']) . "', ";
		$sql .= "name_last = '" . $database->escape_value($_SESSION['interview']['name_last']) . "', ";
		$sql .= "name_business = '" . $database->escape_value($_SESSION['interview']['name_business']) . "', ";
		$sql .= "email = '" . $database->escape_value($_SESSION['interview']['email']) . "', ";
		$sql .= "password = '" . $database->escape_value($_SESSION['interview']['password']) . "', ";
		$sql .= "phone = '" . $database->escape_value($_SESSION['interview']['phone']) . "', ";
		$sql .= "street_1 = '" . $database->escape_value($_SESSION['interview']['street_1']) . "', ";
		$sql .= "street_2 = '" . $database->escape_value($_SESSION['interview']['street_2']) . "', ";
		$sql .= "city = '" . $database->escape_value($_SESSION['interview']['city']) . "', ";
		$sql .= "state = '" . $database->escape_value($_SESSION['interview']['state']) . "', ";
		$sql .= "zip = '" . $database->escape_value($_SESSION['interview']['zip']) . "' ";
		$sql .= "WHERE ";
		$sql .= "provider_id = " . $database->escape_value($this->provider_id) . " ";
		$updated = $database->query($sql);
		if ( $database->affected_rows() == 1 ) {
			//Services::update($this->provider_id);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function updateExisting (  ) {
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
		$sql .= "providers ";
		$sql .= "SET ";
		$sql .= $database->escape_value($attribute_name) . " = '" . $database->escape_value($this->$attribute_name) . "' ";
		$sql .= "WHERE ";
		$sql .= "provider_id = '" . $this->provider_id . "' ";
		$result = $database->query($sql);
		$successfully_updated = $result;
		return $successfully_updated;
	}
	
	private function updateServices (  ) {
		Services::delete( $this->provider_id , 'provider' );
		unset ( $this->services );
		Services::add( $this->provider_id , 'provider' );
	}

	private function updateSession (  ) {
		global $session;
		$session->saveProviderID($this->provider_id);
	}
	
}

?>
