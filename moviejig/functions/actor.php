<?

function getAndSaveActorID ( $actor_od_display_name ) { // Find actor ID in OpusData, then save into Private DB.
	// Establish Globals
		global $db_con_od,$db_con_pr;
	// Get ID.
		$query = "SELECT odid ";
		$query = "FROM person ";
		$query = "WHERE display_name = '" . $actor_od_display_name . "'";
		$result = mysqli_query( $db_con_od , $query );
		testQuerySuccess( $query , $result );
		while ( $row = mysqli_fetch_assoc($result) ) {
			$id = $row['odid'];
		}
	// Save ID.
		$query 	= "UPDATE top_actors ";
		$query .= "SET od_odid = '" . $id . "' ";
		$query .= "WHERE od_display_name = '" . $actor_od_display_name . "'";
		$result = mysqli_query( $db_con_pr , $query );
		testQuerySuccess( $query , $result );
}

function getActorODID ( $actor_od_display_name ) { // Get stats for one actor.
	// Globals.
		global $db_con_od;
	// Actor data array.
		$actor_data = array();
	// QUERY - Actor Data.
		$query  = "SELECT ";
		$query .= "odid ";
		$query .= "FROM person ";
		$query .= "WHERE display_name = '" . $actor_od_display_name . "' ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// Get actor data.
		while($actor = mysqli_fetch_assoc($result)) {
			$actor_odid = $actor['odid'];
		}
	// Return actor's data array.
		return $actor_odid;
}

function getActorName ( $actor_odid ) {
	// Globals.
		global $db_con_od;
	// INITIALIZE ARRAYS
		$actor_name = array();
	// QUERY - Query actor display name.
		$query  = "SELECT ";
		$query .= "display_name, ";
		$query .= "professional_first_names, ";
		$query .= "professional_last_names ";
		$query .= "FROM person ";
		$query .= "WHERE odid = '" . $actor_odid . "' ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// Get actor data.
		while( $row = mysqli_fetch_assoc($result) ) {
			$actor_name['display_name'] = $row['display_name'];
			$actor_name['professional_first_name'] = $row['professional_first_names'];
			$actor_name['professional_last_name'] = $row['professional_last_names'];
		}
	// Return actor's data array.
		return $actor_name;
}

function getActorImageURL ( $od_person_display_name ) {
	$image_html_path_name_ext = "/images/stars/" . cleanForServer ( $od_person_display_name ) . "_01.jpg";
	$local_image_absolute_path_name_ext = "/var/chroot/home/content/09/11011709/html" . $image_html_path_name_ext;
	$image_html_path_name_ext = (file_exists($local_image_absolute_path_name_ext)) ? $image_html_path_name_ext : "/images/logo_150x150.png";
	return $image_html_path_name_ext;
}

function getActorGender ( $actor_odid ) {
	// Globals.
		global $db_con_od;
	// QUERY - Find actor gender.
		$query  = "SELECT sex_odid ";
		$query .= "FROM person ";
		$query .= "WHERE odid = '" . $actor_odid . "' ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// Get actor data.
		while($sex = mysqli_fetch_assoc($result)) {
			$actor_sex = $sex['sex_odid'];
		}
	// Return actor's data array.
		return $actor_sex;
}

function convertGenderHeSheHisHer ( $actor_sex_odid , $conversion_set ) {
	switch ( $actor_sex_odid ) {
		case 20400:
			$he_she = 'he';
			$his_her = 'his';
			break;
		case 10400:
			$he_she = 'she';
			$his_her = 'her';
			break;
		default:
			$he_she = NULL;
			$his_her = NULL;
			break;
	}
	switch ( $conversion_set ) {
		case 'he_she':
			return $he_she;
			break;
		case 'his_her':
			return $his_her;
			break;
		default:
			return NULL;
			break;
	}
}

function firstLeadMovieRoleYear ( $od_person_odid ) {
	// Globals.
		global $db_con_od;
	// Actor data array.
		$person_movies = array();
		$actor_movie_release_years = array();
	// QUERY - Find all lead movies of actor.
		$query  = "SELECT ";
		$query .= "movie_odid ";
		$query .= "FROM movie_acting_role ";
		$query .= "WHERE person_odid = '" . $od_person_odid . "' ";
		$query .= "AND movie_acting_role_type_odid = '10111'"; // Lead role.
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// List of all lead movies for actor.
		while($movies = mysqli_fetch_assoc($result)) {
			array_push($person_movies,$movies);
		}
	// Find Years Each Movie was Released
		for ( $i=0 ; $i < count($person_movies) ; $i++ ) {
			$actor_movie_release_years[$i] = movieReleaseYear($person_movies[$i]['movie_odid']);
		}
	// Find First Year Actor Produced a Lead Movie
		sort($actor_movie_release_years);
		$first_lead_role_year = $actor_movie_release_years[0];
	// Return actor's data array.
		return $first_lead_role_year;
}

function firstLeadMovieRoleMovie ( $actor_movies ) {
	$movie_name = $actor_movies[0]['display_name'];
	return $movie_name;
}

function getFirstMovieData ( $actor_movies ) {
	$movies_year_sorted = sortMultiArray ( $actor_movies , 'num,production_year' , 'asc' );
	$first_movie_data = $movies_year_sorted[0];
	return $first_movie_data;
}

function getBestSellingMovieData ( $actor_movies ) {
	$best_selling_movie_data;
	for ( $i = 0 ; $i < count ( $actor_movies ) ; $i++ ) {
		if ( $actor_movies[$i]['financial_data']['domestic_box_office'] > $best_selling_movie_data['financial_data']['domestic_box_office'] ) {
			$best_selling_movie_data = $actor_movies[$i];
		}
	}
	return $best_selling_movie_data;
}

function getActorMovieODIDs ( $od_person_odid ) {
	// Globals.
		global $db_con_od;
	// Actor data array.
		$person_movie_data = array();
		$movie_odids = array();
	// QUERY - Actor Data.
		$query  = "SELECT ";
		$query .= "movie_odid ";
		$query .= "FROM movie_acting_role ";
		$query .= "WHERE person_odid = '" . $od_person_odid . "' ";  // Sample: Will Smith 770401
		$query .= "AND movie_acting_role_type_odid = '10111' "; // Lead role.
		// $query .= "AND movie_odid = '186210100' "; // Won't Load.
		// $query .= "AND movie_odid = '11530100' "; // Loads ok.
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// Get actor data.
		while ( $row = mysqli_fetch_assoc($result) ) {
			array_push ( $movie_odids , $row ); // Movie array with movie_odid and movie_display_name.
		}
	// Return actor's data array.
		return $movie_odids;
}

function getActorMovieData ( $actor_odid , $depth = 'shallow' , $sort_by = "best_selling" ) {
	// Globals.
		global $db_con_od;
	// Arrays.
		$actor_movie_odids = array();
		$actor_movies = array();
	// MOVIE ODIDs - Get all for actor.
		$actor_movie_odids = getActorMovieODIDs ( $actor_odid );
	// MOVIE DATA - Get actor movie data.
	for ( $i=0 ; $i < count($actor_movie_odids) ; $i++ ) {
		$actor_movies[$i] = getMovieData ( $actor_movie_odids[$i]['movie_odid'] , $actor_odid , $depth );
	}
	// Sort Actor Movies Array by Production Year
		// Sort By
			// Best Selling - Path = 'num,production_year'
			// Release Year - Path = 'num,financial_data,domestic_box_office'
			if ( $sort_by == "best_selling" ) {
				$actor_movies = sortMultiArray ( $actor_movies , 'num,financial_data,domestic_box_office' , 'desc' );
			} elseif ( $sort_by == "year" ) {
				$actor_movies = sortMultiArray ( $actor_movies , 'num,production_year' , 'desc' );
			}
	return $actor_movies;
}

function getActorRank ( $actor_odid ) {
	// Establish Globals
		global $db_con_od,$db_con_pr;
	// QUERY - Get Actor rank.
		$query  = "SELECT actor_rank ";
		$query .= "FROM top_actors ";
		$query .= "WHERE od_odid = '" . $actor_odid . "'";
		$result = mysqli_query( $db_con_pr , $query );
		testQuerySuccess( $query , $result );
		while ( $row = mysqli_fetch_assoc($result) ) {
			$actor['actor_rank'] = $row['actor_rank'];
		}
	// Return
		return $actor['actor_rank'];
}

function getActorMovieCharacterData ( $actor_odid , $movie_odid ) {
	/*global $database_opusdata;
	$sql  = "SELECT ";
	$sql .= "character_name, ";
	$sql .= "movie_acting_role_type_display_name, ";
	$sql .= "billing ";
	$sql .= "FROM ";
	$sql .= "movie_acting_role ";
	$sql .= "WHERE ";
	$sql .= "movie_odid = " . $movie_odid . " ";
	$sql .= "AND ";
	$sql .= "person_odid = " . $actor_odid . " ";
	
	$actor_movie_character_data = $database_opusdata->query ( $sql );*/
}

function getActorData ( $actor_odid , /*$movie_odid = NULL , */$depth = 'basic' ) {
	// ESTABLISH GLOBALS - Establish globals.
		global $db_con_od;
	// ESTABLISH ARRAYS
		$actor_movie_odids = array();
	// MOVIE SORT BY - Establish movie sort by method.
		if ( isset ( $_GET['movie_sort_by'] ) ) {
			$movie_sort_by = $_GET['movie_sort_by'];
		} else {
			$movie_sort_by = "best_selling";
		}
	// GET ACTOR BASIC DATA:
		// ACTOR ODID - Save actor odid data.
			$actor['odid'] = $actor_odid;
		// ACTOR NAME DATA - Get actor name data.
			$actor['name'] = getActorName ( $actor['odid'] );
		// ACTOR RANK DATA - Get actor rank data.
			$actor['rank'] = getActorRank ( $actor['odid'] );
		// ACTOR IMAGE - Get actor image url.
			$actor['image'] = getActorImageURL ( $actor['name']['display_name'] );
	// GET ACTOR MOVIE DATA:
		/*if ( $movie_odid ) {
			getActorMovieCharacterData ( $actor_odid , $movie_odid );
		}*/
	// GET ACTOR FULL DATA:
		if ( $depth == 'full' ) {
			// ACTOR PERSON DATA
				$actor['sex_odid'] = getActorGender ( $actor['odid'] );
			// ACTOR GENDER DATA
				$actor['he_she'] = convertGenderHeSheHisHer ( $actor['sex_odid'] , 'he_she' );
				$actor['his_her'] = convertGenderHeSheHisHer ( $actor['sex_odid'] , 'his_her' );
			// ACTOR MOVIES - Gather movie data.
				$actor['movies'] = getActorMovieData ( $actor['odid'] , $depth , $movie_sort_by );
			
		}
	return $actor;
}

function buildActorWiki ( $actor_odid ) {
	
}

function buildActorProfile ( $actor , $profile_detail = 'basic' ) {
	// Globals
		global $url;
	// Actor Main Profile
		?>
		<div id="<? echo "a" . $actor['odid']; ?>">
			<h5 class="p9 margin-bot1 main_h5" >
				<? echo $actor['name']['display_name'] . "'s Best Movies"; ?>
			</h5>
			<div class="actor_box page2-box3">
				<!-- Actor Image -->
					<? buildActorImageRegion ( $actor , NULL , "full" , "primary" ); ?>
				<!-- Actor Details -->
					<article class="grid_8">
						<!-- Actor Movie Summary  -->
							<?
								$first_movie_data = getFirstMovieData ( $actor['movies'] );
								$best_selling_movie_data = getBestSellingMovieData ( $actor['movies'] );
							?>
							<div class="actor_summary" >
								<p class="first_movie" >
									<span class="actor_name"><? echo $actor['name']['display_name']; ?></span> has made 
									<span class="number_movies_made"><? echo count($actor['movies']); ?></span> movies in which 
									<span class="actor_he_she"><? echo $actor['he_she']; ?></span> starred in a leading role since 
									<span class="actor_his_her"><? echo $actor['his_her']; ?></span> first movie 
									<span class="movie_years_ago"><? echo date("Y") - $first_movie_data['production_year']; ?></span> years ago called 
									<span class="movie_name">"<? echo $first_movie_data['display_name']; ?>"</span>
									(<span class="movie_year"><? echo $first_movie_data['production_year']; ?></span>) in which 
									<span class="actor_he_she"><? echo $actor['he_she']; ?></span> portrayed the character 
									<span class="movie_character"><? echo $first_movie_data['character_name']; ?></span>.
								</p>
								<p class="best_movie" >
									The highest grossing movie of 
									<span class="actor_name"><? echo $actor['name']['display_name']; ?></span>'s career, 
									<span class="movie_name">"<? echo $best_selling_movie_data['display_name']; ?>"</span>, sold nearly 
									<span class="box_office">
										$<? echo number_format ( $best_selling_movie_data['financial_data']['domestic_box_office']); ?>
									</span> at the box office in 
									<span class="movie_year"><? echo $best_selling_movie_data['production_year']; ?></span>.
								</p>
							</div>
						<!-- Actor Wiki Entry -->
							<div class="actor_wiki">
								<? buildActorWiki ( $actor['odid'] ); ?>
							</div>
					</article>
				<!-- Actor Rank -->
					<? if ( $profile_detail == 'full' ) { ?>
						<article class="rank grid_2 omega">
							<div class="inner">
								Ranked
								<span>#<? echo $actor['rank']; ?></span>
								of All Actors
							</div>
						</article>
					<? } ?>
				<!-- Actor Movies -->
					<? if ( $profile_detail == 'full' ) { ?>
						<article class="actor_movies">
						<h6 class="main_h5"><? echo $actor['name']['display_name']; ?>'s Movies</h6>
						<?
							$names = array ( "Popularity" , "Year" );
							$target_urls = array ( 
								"/index.php?page=actor&actor=" . $url->queries['actor'] . "&movie_sort_by=best_selling" ,
								"/index.php?page=actor&actor=" . $url->queries['actor'] . "&movie_sort_by=year"
							);
							if ( $url->queries['movie_sort_by'] == 'best_selling' ) {
								$down_button = 0;
							} elseif ( $url->queries['movie_sort_by'] == 'year' ) {
								$down_button = 1;
							} else {
								$down_button = 0;
							}
							Controls::sortByGroup( "Sort Movies by" , $names , $target_urls , $down_button );
						?>
						<? buildMovieList ( $actor , 'production_year' , 'asc' ); ?>
						</article>
					<? } ?>
			</div>
		</div>
		<?
}

function buildActorImageRegion ( $actor , $actor_movie_character_data = NULL , $profile = "basic" , $hierarchy = "primary" ) {
	$scale = ( $hierarchy == "primary" ) ? NULL : "scale";
	?>
	<div class="actor actor_image_region <? echo $scale; ?> <? echo $profile; ?> actor_<? echo $actor['odid']; ?>" >
		<a 
			class="clickable_actor_region" 
			data-actor-id="<? echo $actor['odid']; ?>" 
			href="/index.php?page=actor&actor=<? echo $actor['odid']; ?>" 
		>
			<div class="actor_name first_name" >
				<p><? echo $actor['name']['professional_first_name']; ?></p>
			</div>
			<div class="img_container" >
				<img class="actor_image" alt="<? echo $actor['name']['display_name']; ?>'s Portrait" src="<? echo $actor['image']; ?>">
			</div>
			<div class="actor_name last_name" >
				<p><? echo $actor['name']['professional_last_name']; ?></p>
			</div>
			<? if ( $hierarchy == "secondary" ) { ?>
				<div class="character_info" >
					<div class="character_name" >
						<p>Plays as</p>
						<p><? echo $actor_movie_character_data['character_name']; ?></p>
					</div>
					<div class="movie_acting_role_type_display_name" >
						<p>(<? echo $actor_movie_character_data['movie_acting_role_type_display_name']; ?> Role)</p>
					</div>
				</div>
			<? } ?>
		</a>
		<script>
			$(document).ready(function() {
				// adjustHeights(".actor_<? // echo $actor['odid']; ?> .last_name p"); // Fit Child Element Text Inside Parent.
			});
		</script>
	</div>
	<?
}

function buildActorProfileCompact ( $actor ) {
	buildActorImageRegion ( $actor , NULL , "basic" , "primary" );
}

function sortActorMoviesByBoxOffice ( $actor , $sort_direction = 'asc' ) {
	$array_to_sort = $actor['movies'];
	$sorted_flag = FALSE;
	while ( $sorted_flag != TRUE ) {  // Keep passing through array until sorted.
		$sorted_flag = TRUE;
		for ( $i = 0 ; $i < count ( $array_to_sort ) ; $i++ ) {   // One pass through array.
			$at_array_end = ( $i == ( count ( $array_to_sort ) - 1 ) ) ? TRUE : FALSE;
			if ( ! $at_array_end ) {
				// Set position values.
					$cur_pos_key = $i;
					$adj_pos_key = $i + 1;
					$cur_pos_val = $array_to_sort[$cur_pos_key]['financial_data']['domestic_box_office'];
					$adj_pos_val = $array_to_sort[$adj_pos_key]['financial_data']['domestic_box_office'];
				// Determine priority of sorting.
					if ( $order_by == 'asc' ) {
						$primary = $cur_pos_val;
						$secondary = $adj_pos_val;
					} else if ( $order_by == 'desc' ) {
						$primary = $adj_pos_val;
						$secondary = $cur_pos_val;
					}
				// Swap values.
					if ( $primary > $secondary ) {
						$temp_holder = $array_to_sort[$cur_pos_key];  // Reserve spot to be replaced.
						$array_to_sort[$cur_pos_key] = $array_to_sort[$adj_pos_key];  // Save new lowest value.
						$array_to_sort[$adj_pos_key] = $temp_holder;  // Save new highest value.
						$sorted_flag = FALSE;
					}
			}
		}
	}
	$actor['movies'] = $array_to_sort;
	return $actor;
}

function getTopSellingMovieIDs ( $actor_odid ) {
	// Establish Globals
		global $db_con_od , $db_con_pr;
	// QUERY - Get Actor rank.
		$query  = "SELECT actor_rank ";
		$query .= "FROM top_actors ";
		$query .= "WHERE od_odid = '" . $actor_odid . "'";
		$result = mysqli_query( $db_con_pr , $query );
		testQuerySuccess( $query , $result );
		while ( $row = mysqli_fetch_assoc($result) ) {
			$actor['actor_rank'] = $row['actor_rank'];
		}
	// Return
		return $actor['actor_rank'];
}

function buildActorSlider ( $actor ) {
	sortActorMoviesByBoxOffice($actor);
	?>
	
	<div id="slide">			  	
			<!-- slider -->
			<div class="slider">
				<ul class="items">
					<? for ( $i = 0 ; $i < 6 ; $i++ ) { ?>
					<li>
						<img src="images/slide-1.jpg" alt="" />
						<div class="banner">
							<span>
								<strong><i>collection of movies</i> for the entertainment lovers</strong> 
								<a class="button3" href="more.php">Shop Now!</a>
							</span>
						</div>
					</li>
					<? } ?>
				</ul>
			</div>
			<!-- slider end -->	
			<div class="pag">
				<div class="img-pags">
				  <ul>
					<li><a href="more.php"><img src="images/slide-1-thumb.jpg" alt="" width="80" height="60" /></a></li>
					<li><a href="more.php"><img src="images/slide-2-thumb.jpg" alt="" /></a></li>
					<li><a href="more.php"><img src="images/slide-3-thumb.jpg" alt="" /></a></li>
					<li><a href="more.php"><img src="images/slide-4-thumb.jpg" alt="" /></a></li>
					<li><a href="more.php"><img src="images/slide-5-thumb.jpg" alt="" /></a></li>
					<li><a href="more.php"><img src="images/slide-6-thumb.jpg" alt="" /></a></li>
				  </ul>  
				</div>								
			</div>
		</div>		

	<?
}

?>
