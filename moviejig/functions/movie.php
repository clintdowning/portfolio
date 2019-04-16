<?

function movieReleaseYear($movie_odid) {
	// Globals.
		global $db_con_od;
	// Arrays
		$firstMovieReleaseYear = array();
	// QUERY BUILD - First Lead Movie Release Year.
		$query  = "SELECT production_year ";
		$query .= "FROM movie ";
		$query .= "WHERE odid = " . $movie_odid . " ";
		$query .= "ORDER BY production_year ASC ";
		$query .= "LIMIT 1 ";
	// RUN QUERY
		$result = mysqli_query( $db_con_od, $query );
	// TEST QUERY
		testQuerySuccess ( $query , $db_con_od );
	// List of all lead movies for actor.
		while($firstMovieReleaseYearResult = mysqli_fetch_assoc($result)) {
			$firstMovieReleaseYear = $firstMovieReleaseYearResult['production_year'];
		}
	return $firstMovieReleaseYear;
}

function getMovieCharacterName ( $actor_odid , $movie_odid ) {
	// Globals.
		global $db_con_od;
	// QUERY - Get movie character name of actor.
		$query  = "SELECT character_name ";
		$query .= "FROM movie_acting_role ";
		$query .= "WHERE movie_odid = " . $movie_odid . " ";
		$query .= "AND person_odid = " . $actor_odid . " ";
		$query .= "LIMIT 1 ";
	// RUN QUERY
		$result = mysqli_query( $db_con_od, $query );
	// TEST QUERY
		testQuerySuccess ( $query , $db_con_od );
	// Capture Movie Character Name.
		while ( $character = mysqli_fetch_assoc($result) ) {
			$character_name = $character['character_name'];
		}
	return $character_name;
}

function getMovieImageData ( $movie ) {
	// Globals.
		global $db_con_od;
		global $db_con_pr;
	// INITIALIZE - Movie poster data collection array.
		$movie_poster_data = array();
	// GET LOCAL - Get from local amazon cached data.
		$query  = "SELECT ";
		$query .= "odid, ";
		$query .= "display_name, ";
		$query .= "asin, ";
		$query .= "image_width_size, ";
		$query .= "image_height_size, ";
		$query .= "local_image_html_path_name_ext ";
		$query .= "FROM ";
		$query .= "api_amazon ";
		$query .= "WHERE ";
		$query .= "odid = '" . $movie['odid'] . "' ";
		$movie_poster_data = runQuery ( $query , $db_con_pr );
		$movie_poster_data[0]['default_poster_class'] = NULL;
		if ( empty ( $movie_poster_data[0]['local_image_html_path_name_ext'] ) ) {
			$movie_poster_data[0]['image_width_size'] = "150";
			$movie_poster_data[0]['image_height_size'] = "150";
			$movie_poster_data[0]['local_image_html_path_name_ext'] = "/images/logo_150x150.png";
			$movie_poster_data[0]['default_poster_class'] = "default_poster_class";
		}
	return $movie_poster_data;
}

function getMovieFinancialSummary ( $movie_odid ) {
	// Globals.
		global $db_con_od;
	// Arrays
		$movie_financial_summary = array();
	// QUERY - Find movie poster image url.
		$query  = "SELECT ";
		$query .= "production_budget, ";
		$query .= "domestic_box_office, ";
		$query .= "international_box_office, ";
		$query .= "inflation_adjusted_domestic_box_office ";
		$query .= "FROM movie_financial_summary ";
		$query .= "WHERE movie_odid = " . $movie_odid . " ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// CAPTURE - Movie poster data.
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$movie_financial_summary['production_budget'] = $row['production_budget'];
			$movie_financial_summary['domestic_box_office'] = $row['domestic_box_office'];
			$movie_financial_summary['international_box_office'] = $row['international_box_office'];
			$movie_financial_summary['inflation_adjusted_domestic_box_office'] = $row['inflation_adjusted_domestic_box_office'];
		}
	return $movie_financial_summary;
}

function getMovieTableData ( $movie_odid , $actor_odid ) {
	// Globals.
		global $db_con_od;
	// Arrays.
		$movie = array();
	// QUERY - Find all lead movies of actor.
		$query  = "SELECT ";
		$query .= "display_name, ";
		$query .= "production_year, ";
		$query .= "movie_creative_type_display_name, ";
		$query .= "movie_source_display_name, ";
		$query .= "movie_genre_display_name, ";
		$query .= "sequel, ";
		$query .= "running_time ";
		$query .= "FROM movie ";
		$query .= "WHERE odid = '" . $movie_odid . "' ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// List of all lead movies for actor.
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$movie['odid'] = $movie_odid;
			$movie['display_name'] = $row['display_name'];
			$movie['production_year'] = $row['production_year'];
			$movie['movie_creative_type_display_name'] = $row['movie_creative_type_display_name'];
			$movie['movie_source_display_name'] = $row['movie_source_display_name'];
			$movie['movie_genre_display_name'] = $row['movie_genre_display_name'];
			$movie['sequel'] = $row['sequel'];
			$movie['running_time'] = $row['running_time'];
			$movie['character_name'] = getMovieCharacterName ( $actor_odid , $movie_odid );
		}
	return $movie;
}

function getMovieSynopsis ( $movie_odid ) {
	// Globals.
		global $db_con_od;
	// QUERY - Find movie poster image url.
		$query  = "SELECT ";
		$query .= "synopsis ";
		$query .= "FROM movie_synopsis ";
		$query .= "WHERE movie_odid = " . $movie_odid . " ";
		$result = mysqli_query( $db_con_od, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_od );
	// CAPTURE - Movie poster data.
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$movie_synopsis = $row['synopsis'];
		}
	return $movie_synopsis;
}

function getMovieTrailer ( $movie_odid ) {
	// Globals.
		global $db_con_pr;
	// QUERY - Find movie poster image url.
		$query  = "SELECT ";
		$query .= "trailer_key ";
		$query .= "FROM api_tmdb ";
		$query .= "WHERE od_id = " . $movie_odid . " ";
		$result = mysqli_query( $db_con_pr, $query );
		// Test if there was a query error
		testQuerySuccess ( $query , $db_con_pr );
	// CAPTURE - Movie poster data.
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$movie_trailer_key = $row['trailer_key'];
		}
	return $movie_trailer_key;
}

function getMovieActors ( $movie_odid ) {
	global $database_opusdata;
	global $database_private;
	$sql  = "SELECT ";
	$sql .= "display_name, ";
	$sql .= "person_odid, ";
	$sql .= "character_name, ";
	$sql .= "movie_acting_role_type_display_name, ";
	$sql .= "billing ";
	$sql .= "FROM ";
	$sql .= "movie_acting_role ";
	$sql .= "WHERE ";
	$sql .= "movie_odid = ";
	$sql .= $movie_odid . " ";
	$sql .= "AND ";
	$sql .= "billing <= 4 ";
	$sql .= "ORDER BY ";
	$sql .= "billing ";
	$sql .= "ASC ";
	$actors = $database_opusdata->query ( $sql );
	for ( $i = 0 ; $i < count ( $actors ) ; $i++ ) {
		$actors[$i]['image'] = getActorImageURL ( $actors[$i]['display_name'] );
	}
	return $actors;
}

function getMovieDirectors ( $movie_odid ) {
	global $database_opusdata;
	global $database_private;
	$sql  = "SELECT ";
	$sql .= "display_name, ";
	$sql .= "person_odid ";
	$sql .= "FROM ";
	$sql .= "movie_technical_role ";
	$sql .= "WHERE ";
	$sql .= "movie_odid = ";
	$sql .= $movie_odid . " ";
	$sql .= "AND ";
	$sql .= "movie_technical_role_type_odid = 10113 ";  // Director (10113)
	$sql .= "ORDER BY ";
	$sql .= "billing ";
	$sql .= "ASC ";
	$directors = $database_opusdata->query ( $sql );
	return $directors;
}

function punctuateMovieBoardReason ( $string_to_punctuate ) {
	$string_to_punctuate = ucfirst ( $string_to_punctuate );  // Uppercase reason first letter.
	$last_period_pos = strripos ( $string_to_punctuate , "." );  // Find last period.
	if ( 
		( $string_to_punctuate != "" ) &&
		( ! ( $last_period_pos == ( strlen ( $string_to_punctuate ) - 1 ) ) )
	) {  // Add period if none.
		$string_to_punctuate .= ".";
	}
	return $string_to_punctuate;
}

function getMovieRating ( $movie_odid ) {
	global $database_opusdata;
	global $database_private;
	$sql  = "SELECT ";
	$sql .= "movie_board_rating_odid, ";
	$sql .= "movie_board_rating_display_name, ";
	$sql .= "movie_board_reason ";
	$sql .= "FROM ";
	$sql .= "movie_movie_board_rating ";
	$sql .= "WHERE ";
	$sql .= "movie_odid = ";
	$sql .= $movie_odid . " ";
	$rating = $database_opusdata->query ( $sql );
	$rating[0]['movie_board_reason'] = punctuateMovieBoardReason ( $rating[0]['movie_board_reason'] );
	$rating[0]['image_url'] = "/images/ratings/" . $rating[0]['movie_board_rating_odid'] . ".png";
	return $rating;
}

function getMovieData ( $movie_odid , $actor_odid , $depth = 'shallow' ) {
	// Arrays.
		$movie = array();
	// Set movie odid.
		$movie['movie_odid'] = $movie_odid;
	// Get Movie table data.
		$movie = getMovieTableData ( $movie_odid , $actor_odid );
	// ACTORS - Get movie actors.
		$movie['actors'] = getMovieActors ( $movie_odid );
	// DIRECTORS - Get movie directors.
		$movie['directors'] = getMovieDirectors ( $movie_odid );
	// RATING - Get movie rating.
		$movie['rating'] = getMovieRating ( $movie_odid );
	// Get movie syopsis.
		$movie['synopsis'] = getMovieSynopsis ( $movie_odid );
	// Get movie poster data.
		if ( $depth != 'shallow' ) {
			$movie['movie_poster_data'] = getMovieImageData ( $movie );
		}
	// GET MOVIE FINANCIAL DATA - Get from financial tables.
		$movie['financial_data'] = getMovieFinancialSummary ( $movie_odid );
	// GET MOVIE TRAILER DATA - Get from tmdb table.
		$movie['trailer_key'] = getMovieTrailer ( $movie_odid );
	return $movie;
}

function buildRunningTime ( $running_time ) {
	if ( $running_time ) {
		?>
			<p class="time">
				<span class="descriptor">Running Time: </span>
				<span><? echo $running_time; ?> minutes</span>
			</p>
		<?	
	}
}

function buildStarring ( $movie_actors ) {
	if ($movie_actors) {
		?>
			<div class="starring">
				<div class="descriptor">Starring: </div>
				<div class="nodes">
					<?
						for ( $i = 0 ; $i < count ( $movie_actors ) ; $i++ ) {
							$actor_data = getActorData ( $movie_actors[$i]['person_odid'] , /*$movie_odid , */'basic' );
							buildActorImageRegion ( $actor_data , $movie_actors[$i] , "basic" , "secondary" );
						}
					?>
				</div>
			</div>
		<?
	}
}

function buildDirectedBy ( $director_display_name ) {
	if ( $director_display_name ) {
		?>
			<p class="director">
				<span class="descriptor">Directed by: </span>
				<span><? echo $director_display_name; ?></span>
			</p>
		<?
	}
}

function buildBoxOfficeSales ( $domestic_box_office ) {
	if ( $domestic_box_office ) {
		?>
			<p class="box_office p10">
				<span class="descriptor">Box Office Sales: </span>
				<span><? echo "$" . number_format($domestic_box_office); ?></span>
			</p>
		<?
	}
}

function buildSynopsis ( $synopsis ) {
	if ( $synopsis ) {
	?>
		<p class="synopsis p10">
			<span class="descriptor">Synopsis: </span>
			<span><? echo $synopsis; ?></span>
		</p>
	<?
	}
}

function buildMovieProfile ( $movie , $actor , $profile_detail = 'basic' ) {
	$actor_movie_id = "a" . $actor['odid'] . "_" . "m" . $movie['odid'];
	?>
	<div class="<? echo $actor_movie_id . " " . $profile_detail; ?> movie_basic page2-box3" >
		<div class="top_section clearfix" >
			<article class="poster grid_2 alpha">
				<figure class="page2-img1">
					<img 
						class="movie_image <? echo $movie['movie_poster_data'][0]['default_poster_class']; ?>" 
						alt="<? echo $movie['movie_poster_data'][0]['display_name'] . " Poster"; ?>" 
						src="<? echo $movie['movie_poster_data'][0]['local_image_html_path_name_ext']; ?>" 
					>
				</figure>
			</article>
			<article class="movie_stats text grid_4">
				<h2 class="movie"><? echo $movie['display_name']; ?></h2>
				<!-- <p class="released"><span>Released</span>: <span><? // echo $movie['production_year']; ?></span></p> -->
				<p class="genre">
					<span class="descriptor">Movie Genre: </span>
					<span><? echo $movie['movie_genre_display_name']; ?></span>
				</p>
				
				<? buildRunningTime ( $movie['running_time'] ); ?>

				<? buildStarring ( $movie['actors'] ); ?>

				<? buildDirectedBy ( $movie['directors'][0]['display_name'] ); ?>

				<p class="character">
					<span class="descriptor"><? echo $actor['name']['display_name']; ?>'s Character Name: </span>
					<span><? echo $movie['character_name']; ?></span>
				</p>
				
				<? buildBoxOfficeSales ( $movie['financial_data']['domestic_box_office'] ); ?>
				
				<p class="rated p10 clearfix">  <!-- LEFTOFF - 150807_0254 - Build Rated Function to hide if empty. -->
					<span class="descriptor">Rated: </span>
					<span class="injected" >
						<img 
							class="rated_image" 
							src="<? echo $movie['rating'][0]['image_url']; ?>" 
							alt="Rated <? echo $movie['rating'][0]['movie_board_rating_display_name']; ?>" 
						/>
						<span class="rated_reason" ><? echo $movie['rating'][0]['movie_board_reason']; ?></span>
					</span>
				</p>
				
				<? buildSynopsis ( $movie['synopsis'] ); ?>
			</article>
			<article class="rating grid_2 omega">
				<div class="inner">
					Year<br/>
					Released
					<span><? echo $movie['production_year']; ?></span>
				</div>
			</article>
		</div>
		<div class="trailer_image_container not_hidden" >
			<div 
				class="trailer_opener" 
				onclick="movieFocus('<? echo $actor_movie_id; ?>','<? echo $movie['trailer_key']; ?>','<? echo $movie['movie_poster_data'][0]['asin'] ?>');" 
			>
				<img 
					class="trailer_image" 
					src="https://img.youtube.com/vi/<? echo $movie['trailer_key']; ?>/mqdefault.jpg" 
					alt="<? echo $movie['movie_poster_data'][0]['display_name']; ?> Trailer Image" 
				/>
				<img class="play_icon" src="/images/mcclint/play_icon_youtube_small_39x27.png" alt="Play Trailer Icon" />
			</div>
			<img class="youtube_powered_small" alt="Powered by YouTube" src="/images/third-parties/youtube_powered_white_small.png" />
		</div>
		<article class="extra" >
			<div class="trailer hidden" >
				<div class="frame" >
					<div class="video" >
					</div>
				</div>
			</div>
			<div class="amazon amazon_<? echo $movie['movie_poster_data'][0]['asin']; ?> hidden" >
				<div class="text top">
					<p>Watch Now!</p>
				</div>
				<div class="ajax">
					<!-- Amazon AJAX injection point. -->
				</div>
				<div class="text bottom">
					<p>on</p>
					<p>Amazon!</p>
				</div>
			</div>
			<img class="youtube_powered_large hidden" alt="Powered by YouTube" src="/images/third-parties/youtube_powered_white_large.png" />
		</article>
		<!-- <div class="more_less" >
			<a class="more button-2" onclick="movieFocus('<? // echo $actor_movie_id; ?>','<? // echo $movie['trailer_key']; ?>');" ><span><span>More...</span></span></a>
		</div> -->
		<? if ( $profile_detail == 'full' ) { ?>
			<div class="load_initial_trailer_script" >
				<script>
					$(document).ready(function() {
						movieFocus( '<? echo $actor_movie_id; ?>' , '<? echo $movie['trailer_key']; ?>' , '<? echo $movie['movie_poster_data'][0]['asin'] ?>' );
					});
				</script>
			</div>
		<? } ?>
	</div>
	<?
}

function buildMovieList ( $actor , $sort_by = 'production_year' , $order_by = 'asc' ) {
	// Sort Movie List Array
		// $actor['movies'] = sortMultiArray ( $actor['movies'] , 'production_year' , $order_by );
		// $actor['movies'] = sortMultiArray ( $actor_movies , 'num,financial_data,domestic_box_office' , 'asc' );
	for ( $i = 0 ; $i < count($actor['movies']) ; $i++ ) {
		if ( $i == 0 ) {
			buildMovieProfile ( $actor['movies'][$i] , $actor , 'full' );
		} else {
			buildMovieProfile ( $actor['movies'][$i] , $actor , 'basic' );
		}
	}
}

?>
