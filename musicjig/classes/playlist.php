<?

class Playlist {
	
	private $song_count_max = 50;		// Max number of songs to display on page. Number or FALSE for all possible songs.
	
	public $name;						// Week Name (for Site), or Playlist Name (for User).
	public $id;							// Playlist ID
	
	protected $sort_by = 'default';		// Song sort order.
	private $sort_by_values = [
		'default',
		'shuffle',
		'peak',
		'date'
	];
	
	public $songs = array();			// Array of Song Objects.
	public $songs_count;				// Number of songs in Songs array.
	
	public $songs_incomplete = array();	// Array of Incomplete Song Objects.
	public $songs_incomplete_count;		// Number of Incomplete Songs in Songs array.
	
	public $name_exists;				// Name already exists in database.
	public $is_complete;				// All songs in playlist have valid YouTube IDs.
	
	public $deleted = array();			// Deleted songs data for when playlist is reset.
	public $errors;						// Errors created.
	
	public static $weeks = array();		// Weeks: All, Done, & Do
	
	private $types = array (
		'artist',	// All artist songs
		'ones',		// All number one hits.
		'user',		// User playlists.
		'week'		// Top 100 of week.
	);
	
	protected static $type;
	
	public $playlist_full;
	public $playlist_spaced;
	public $playlist_full_youtube_player;
	public $playlist_full_titles;
	public $playlist_full_artists;
	public $first_song_rank;
	public $first_song_index;
	public $first_song_youtube_id;
	public $first_song_youtube_img_path;
	public $first_song_youtube_img_path_hq;
	
	protected $rank = 1;
	
	private $sort_obj;
	
	private $build_sort_bar_for = [
		'Week',
		'User'
	];
	
	private $sort_button_ui_names = [
		'default'	=> 'Chart Rank',
		'user'		=> 'Date Added',
		'shuffle'	=> 'Shuffle',
		'peak'		=> 'Peak Rank',
		'date'		=> 'Peak Date',
	];
	
	protected $default_type_sort_bys = [
		'artist' => 'shuffle',
		'radio' => 'shuffle'
	];
	
	public $block;
	
	function __construct ( $id ) {
		global $dev;
		global $page;
		$this->errors = new Errors();
		$this->id = $id;
		$this->name = $this->getName();
		$this->songs = $this->getSongs();  // Create array of Song Objects.
		$this->songs_count = count ( $this->songs );  // Count number of songs in Song Array.
		if ( $dev->is ) {
			$this->sort_obj = new Sort(static::$type);
		}
		$this->sort_by = $this->calcSortBy();
		$this->sortSongs();
		if ( $this->songs_count ) {
			$this->is_complete = $this->findIfComplete();  // See if week is complete with YouTube IDs.
			if ( ! $this->is_complete ) {
				$this->songs_incomplete = $this->getIncompleteSongs();
				$this->songs_incomplete_count = count ( $this->songs_incomplete );
			}
			$this->calcPlaylistData();
		}
		//d($this);
	}
	
	public function buildList (  ) {
		global $dev;
		?><div class="playlist clearfix"><?
			if ( isset ( $this->block ) ) {
				$this->block->output();
			}
			if ( static::$type == 'Week' ) {
				$this->buildNavBar();
			}
			if ( 0 ) {
				$this->sort_obj->buildSortBar();
			} else {
				if ( in_array ( static::$type , $this->build_sort_bar_for ) ) {
					$this->buildSortBar();
				}
			}
			for ( $i = 0 ; $i < $this->songs_count ; $i++ ) {
				$this->songs[$i]->buildUI( $i , $this->playlist_full , $i+1 );
			}
		?></div><?
	}	
	
	private function buildPlaylistFullContent ( $content_type = NULL ) {
		$content = NULL;
		$playlist_count = $this->songs_count;
		for ( $i = 0 ; $i < $playlist_count ; $i++ ) {  // Build comma delimited playlist with apostrophes for YouTube Player API.
			if ( $i == 0 ) {  // Append first apostrophy.
				$content .= "'";
			}
			switch ( $content_type ) {
				case 'title':
					$content .= $this->songs[$i]->title;  // Append Title.
					break;
				case 'artist':
					$content .=  $this->songs[$i]->artist->name;  // Append Artist.
					break;
				case 'youtube_id':
					$content .= $this->songs[$i]->youtube_id;  // Append YouTube ID.
					break;
				default:
					$content .= NULL;
			}
			if ( $i != ( $playlist_count - 1 ) ) {  // Append joining commas and apostrophes.
				$content .= "','";
			}
			if ( $i == ( $playlist_count - 1 ) ) {  // Append last apostrophy.
				$content .= "'";
			}
		}
		return $content;
	}

	private function buildURLForSort ( $new_sort_by = 'default' ) {
		global $url;
		$url_for_sort = NULL;
		$url_for_sort .= $url->removeQueryParam( 'sort_by' );
		$url_for_sort .= '&sort_by=' . $new_sort_by;
		return $url_for_sort;
	}
	
	public function buildSortBar (  ) {
		if ( 0 ) {
		?>
			<div id="sort_bar">
				<div class="title">
					<h3>Sort By:</h3>
				</div>
				<div class="controls"> <?
					$this->buildSortBarButtons();
				?></div>
			</div>
		<?
		}
	}
	
	private function buildSortBarButtons (  ) {
		global $page;
		switch ( $page->type ) {
			case 'user':
			case 'week':
				$this->buildSortBarButton('default');
				$this->buildSortBarButton('shuffle');
				$this->buildSortBarButton('peak');
				$this->buildSortBarButton('date');
				break;
			case 'radio':
				break;
			case 'artist':
				$this->buildSortBarButton('default');
				$this->buildSortBarButton('peak');
				$this->buildSortBarButton('date');
				break;
			case '':
				
				break;
			default:
				
		}
	}
	
	private function buildSortBarButton ( $sort_by = 'default' ) {
		global $page;
		global $facebook;
		if ( $sort_by == 'default' && $page->type == 'user' ) {
			$sort_button_ui_name = $this->sort_button_ui_names['user'];
		} elseif ( $sort_by == 'default' && $page->type == 'radio' ) {
			$sort_button_ui_name = $this->sort_button_ui_names['shuffle'];
		} elseif ( $sort_by == 'default' && $page->type == 'artist' ) {
			$sort_button_ui_name = $this->sort_button_ui_names['shuffle'];
		} else {
			$sort_button_ui_name = $this->sort_button_ui_names[$sort_by];
		}
		if ( $sort_by == $this->sort_by ) {
			?><p class="button <? echo $this->sortButtonSwitch($sort_by); ?>"><? echo $sort_button_ui_name; ?></p><?
		} else {
			?><a class="button <? echo $this->sortButtonSwitch($sort_by); ?>" href="<? echo $this->buildURLForSort($sort_by); ?>"><? echo $sort_button_ui_name; ?></a><?
		}
	}
	
	protected function calcSortBy (  ) {
		$sort_by = Page::getURLQueryParameter('sort_by');
		$page_type =  Page::getType();
		if ( ! $sort_by ) {
			$sort_by = 'default';
		}
		if ( $sort_by == 'default' ) {
			if ( array_key_exists ( $page_type , $this->default_type_sort_bys ) ) {
				$sort_by = $this->default_type_sort_bys[$page_type];
			}
		}
		return $sort_by;
	}
	
	public function sortButtonSwitch ( $button_name ) {
		$switch = ( $button_name == $this->sort_by ) ? 'on' : 'off' ;
		return $switch;
	}
	
	public static function calcBestRankPoints (  ) {
		global $database;
		$all_songs = array();
		$sql  = "SELECT ";
		$sql .= "id ";
		$sql .= "FROM ";
		$sql .= "song ";
		$result = $database->query($sql);
		for ( $i = 0 ; $i < 5000/*count ( $result )*/ ; $i++ ) {
			$all_songs['id'][$i] = $result[$i]['id'];
			$all_songs['rank_points'][$i] = Song::calcRankPoints($result[$i]['id']);
		}
		$max = max ( $all_songs['rank_points'] );
		$min = min ( $all_songs['rank_points'] );
		d($max);
		d($min);
		echo "done";
		die();
		return $best_rank_points;
	}
	
	protected function calcFirstSongImgPaths (  ) {
		$this->first_song_youtube_img_path = $this->songs[0]->youtube_img_path;
		$this->first_song_youtube_img_path_hq = $this->songs[0]->youtube_img_path_hq;
		return TRUE;
	}

	protected function calcPlaylistData (  ) {
		$this->first_song_rank = $this->rank;
		$this->first_song_index = $this->first_song_rank - 1;
		$this->first_song_youtube_id = $this->getFirstSongYouTubeID();
		$this->calcFirstSongImgPaths();
		$this->playlist_full = $this->getPlaylist( 'full' , 'asc' );
		$this->playlist_spaced = $this->getPlaylist( 'spaced' , 'asc' );
		$this->playlist_full_youtube_player = $this->buildPlaylistFullContent('youtube_id');
		$this->playlist_full_titles = $this->buildPlaylistFullContent('title');
		$this->playlist_full_artists = $this->buildPlaylistFullContent('artist');
	}

	protected static function createPlaylistSongLink ( $playlistuser_id , $song_id , $rank ) {
		global $database;
		$sql  = "INSERT INTO ";
		$sql .= $database->escape_value(static::$table_name) . "_Song ";
		$sql .= "( ";
		$sql .= "playlist" . $database->escape_value(strtolower ( static::$type )) . "_id, ";
		$sql .= "song_id, ";
		$sql .= "rank, ";
		$sql .= "saved ";
		$sql .= ") ";
		$sql .= "VALUES ";
		$sql .= "( ";
		$sql .= $database->escape_value($playlistuser_id) . ", ";
		$sql .= $database->escape_value($song_id) . ", ";
		$sql .= $database->escape_value($rank) . ", '";
		$sql .= $database->escape_value(DateTimeMod::formattedDateTime('mysql',NULL)) . "' ";
		$sql .= ") ";
		$result = $database->query($sql);
		$playlist_song_link_id = $database->insert_id();
		return $playlist_song_link_id;
	}

	public function fillIncompletes (  ) {  // Grab YouTube IDs where youtube_id is NULL or Empty.
		global $youtube_api;
		for ( $i = 0 ; $i < $this->songs_incomplete_count ; $i++ ) {
			$song_id = $this->songs_incomplete[$i]->id;
			$title = $this->songs_incomplete[$i]->title;
			$artist = $this->songs_incomplete[$i]->artist->name;
			$youtube_api->addToQue ( $song_id , $title , $artist );
		}
	}

	private function findIfComplete () {  // Determines if all songs in week have valid YouTube IDs.
		$complete = TRUE;
		for ( $i = 0 ; $i < $this->songs_count ; $i++ ) {
			$current_youtube_id = $this->songs[$i]->youtube_id;
			if ( $current_youtube_id == NULL || $current_youtube_id == '' ) {
				$complete = FALSE;
				break;
			}
		}
		return $complete;
	}
	
	function getFirstSongYouTubeID (  ) {
		$first_song_youtube_id = $this->songs[0]->youtube_id;
		return $first_song_youtube_id;
	}
	
	public static function getID ( $name , $type = 'week' ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "id ";
		$sql .= "FROM ";
		$sql .= "PlaylistWeek ";
		$sql .= "WHERE ";
		$sql .= "name = '" . $name . "' ";
		$sql .= "AND ";
		$sql .= "type = '" . $type . "' ";
		$result = $database->query($sql);
		$id = $result[0]['id'];
		return $id;
	}

	protected static function get ( $user_id , $name ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= static::$table_name . " ";
		$sql .= "WHERE ";
		$sql .= "user_id = '" . $database->escape_value($user_id) . "' ";
		$sql .= "AND ";
		$sql .= "name = '" . $database->escape_value($name) . "' ";
		$result = $database->query($sql);
		$playlist_id = ( $result ) ? $result[0]['id'] : NULL ;
		return $playlist_id;
	}
	
	private function getIncompleteSongs (  ) {
		$incomplete_songs = array();
		for ( $i = 0 ; $i < $this->songs_count ; $i++ ) {
			$current_youtube_id = $this->songs[$i]->youtube_id;
			if ( $current_youtube_id == NULL || $current_youtube_id == '' ) {
				$incomplete_songs[] = $this->songs[$i];
			}
		}
		return $incomplete_songs;
	}

	protected function getName (  ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "name ";
		$sql .= "FROM ";
		$sql .= "Playlist" . static::$type . " ";
		$sql .= "WHERE ";
		$sql .= "id = '" . $this->id . "' ";
		$result = $database->query($sql);
		$name = $result[0]['name'];
		return $name;
	}

	public static function getOnes (  ) {  // Return playlist of all number one hits.
		
	}

	public static function getOrSave ( $user_id , $name ) {
		$playlist_id = static::get( $user_id , $name );
		$playlist_id = ( $playlist_id ) ? $playlist_id : self::save( $user_id , $name ) ;
		return $playlist_id;
	}
	
	function getPlaylist ( $group = 'full' , $sort_order = 'asc' ) {  // Build csv list of YouTube IDs for YouTube Player API.
		$playlist = NULL;
		for ( $i = 0 ; $i < $this->songs_count ; $i++ ) {  // Build comma delimited playlist.
			$playlist .= $this->songs[$i]->youtube_id . ",";
		}
		if ( $group == 'spaced' ) {  // Remove live id from playlist.
			$playlist = str_replace ( $this->first_song_youtube_id , "" , $playlist );
		}
		$playlist = str_replace ( ",," , "," , $playlist );  // Replace double commas with single comma.
		$playlist = trim($playlist,",");  // Trim commas from left side.
		$playlist = rtrim($playlist,",");  // Trim commas from right side.
		return $playlist;
	}
	
	private function getSongs (  ) {  // Gathers all songs for week.
		global $database;	
		$songs = array();
		$sql  = "SELECT ";
		$sql .= "* ";
		$sql .= "FROM ";
		$sql .= static::$table_name . "_Song ";
		$sql .= "WHERE ";
		$sql .= strtolower ( static::$table_name ) . "_id = " . $database->escape_value($this->id) . " ";
		$sql .= "ORDER BY rank ASC ";
		if ( $this->song_count_max ) {
			$sql .= "LIMIT " . $this->song_count_max . " ";
		}
		$result = $database->query($sql);
		for ( $i = 0 ; $i < count ( $result ) ; $i++ ) {
			if ( static::$type != 'Week' ) {
				$songs[$i] = new Song ( $result[$i]['song_id'] , $result[$i]['playlist' . strtolower ( static::$type ) . '_id'] , 'full' );
			} else {
				$last_playlistweek_id = NULL;
				$songs[$i] = new SongWeek ( $result[$i]['song_id'] , $result[$i]['playlist' . strtolower ( static::$type ) . '_id'] , 'full' , $last_playlistweek_id );
			}
		}
		return $songs;
	}
	
	private function isExists ( $name ) {  // Determines if Playlist with $name exists in database.
		global $database;
		$sql  = "SELECT ";
		$sql .= "name ";
		$sql .= "FROM ";
		$sql .= static::$table_name . " ";
		$sql .= "WHERE ";
		$sql .= "name = '" . $database->escape_value($name) . "' ";
		$result = $database->query($sql);
		$name_exists = ( $result ) ? TRUE : FALSE;
		return $name_exists;
	}
	
	public static function openPlaylistFromName ( $name , $type = 'week' ) {
		$playlist_id = Playlist::getID ( $name , $type );
		$playlist = new Playlist ( $playlist_id );
		return $playlist;
	}

	public function output (  ) {
		
	}

	protected static function removePlaylistSongLink ( $playlist_id , $song_id ) {
		global $database;
		$sql  = "DELETE FROM ";
		$sql .= static::$table_name . "_Song ";
		$sql .= "WHERE ";
		$sql .= strtolower(static::$table_name) . "_id = " . $playlist_id . " ";
		$sql .= "AND ";
		$sql .= "song_id = " . $song_id . " ";
		$result = $database->query($sql);
		$result = ( $result ) ? 'Delete Successfull.' : 'Delete Failed.' ;
		return $result;
	}
	
	protected static function save ( $user_id , $name ) {
		global $database;
		$sql  = "INSERT INTO ";
		$sql .= static::$table_name . " ";
		$sql .= "( ";
		$sql .= "user_id, ";
		$sql .= "name, ";
		$sql .= "saved ";
		$sql .= ") ";
		$sql .= "VALUES ";
		$sql .= "( ";
		$sql .= $database->escape_value($user_id) . ", '";
		$sql .= $database->escape_value($name) . "', '";
		$sql .= $database->escape_value(DateTimeMod::formattedDateTime('mysql',NULL)) . "' ";
		$sql .= ") ";
		$result = $database->query($sql);
		$playlist_id = $database->insert_id();
		return $playlist_id;
	}

	protected static function songCount ( $id ) {
		global $database;
		$sql  = "SELECT ";
		$sql .= "COUNT(*) ";
		$sql .= "FROM ";
		$sql .= static::$table_name . "_Song ";
		$sql .= "WHERE ";
		$sql .= strtolower ( static::$table_name ) . "_id = " . $id . " ";
		$result = $database->query($sql);
		$song_count = ( $result ) ? $result[0]['COUNT(*)'] : NULL ;
		return $song_count;
	}
	
	protected function sortSongs ( $method = NULL ) {
		$sort_by = ( $method ) ? $method : $this->sort_by ;
		switch ( $sort_by ) {
			case 'default':
				
				break;
			case 'user':
				
				break;
			case 'shuffle':
				shuffle($this->songs);
				break;
			case 'peak':
				$this->songs = General::sortObjectArrayByAttribute ( $this->songs , 'top_chart_rank' , 'asc' );
				break;
			case 'date':
				$this->songs = General::sortObjectArrayByAttribute ( $this->songs , 'top_chart_rank_week' , 'desc' );
				break;
			default:
				
		}
	}

}
	
?>
