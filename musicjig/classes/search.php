<?

class Search {
	
	public $query;
	
	public $num_songs_to_show = 5;
	public $num_artists_to_show = 5;
	
	public $all_songs;
	public $all_artists;
	
	public $songs_to_show;
	public $artists_to_show;
	
	public $num_songs;
	public $num_artists;

	function __construct ( $query ) {
		global $database;
		$this->query = General::encodeForHTML( $query , 'basic' );
		$this->all_songs = $this->findSearched('song');
		$this->all_songs = General::sortObjectArrayByAttribute( $this->all_songs , 'weight' , 'desc' );
		$this->songs_to_show = $this->getSongsToShow();
		$this->num_songs = count($this->songs_to_show);
		$this->all_artists = $this->findSearched('artist');
		$this->artists_to_show = $this->getArtistsToShow();
		$this->num_artists = count($this->artists_to_show);
	}
	
	private function findSearched ( $type ) {
		global $database;
		$table = ( $type == 'song' ) ? 'song' : 'artist' ;
		$field = ( $type == 'song' ) ? 'title' : 'name' ;
		$found = array();
		if ( $type == 'song' ) {
			$sql  = "SELECT ";
			$sql .= "* ";
			$sql .= "FROM ";
			$sql .= $table . " ";
			$sql .= "WHERE ";
			$sql .= $field . " LIKE '%" . $database->escape_value($this->query) . "%' ";
			$sql .= "ORDER BY " . $field . " ASC ";
			//$sql .= "LIMIT " . $this->num_songs_to_show . " ";
			$result = $database->query($sql);
		} elseif ( $type == 'artist' ) {
			$sql  = "SELECT ";
			$sql .= "l.id AS id, ";
			$sql .= "l.name AS name, ";
			$sql .= "count(*) AS count ";
			$sql .= "FROM ";
			$sql .= "artist as l ";
			$sql .= "JOIN ";
			$sql .= "song as r ";
			$sql .= "ON ";
			$sql .= "l.id = r.artist_id ";
			$sql .= "WHERE ";
			$sql .= "l.name LIKE '%" . $database->escape_value($this->query) . "%' ";
			$sql .= "GROUP BY ";
			$sql .= "l.name ";
			$sql .= "ORDER BY ";
			$sql .= "count DESC ";
			$sql .= "LIMIT " . $database->escape_value($this->num_artists_to_show) . " ";
			$result = $database->query($sql);
		}
		for ( $i = 0 ; $i < count ( $result ) ; $i++ ) {
			if ( $type == 'song' ) {
				$found[] = new Song( $result[$i]['id'] , NULL , 'basic' );
			} elseif ( $type == 'artist' ) {
				$found[] = new Artist( $result[$i]['id'] , 'basic' );
			}
		}
		return $found;
	}
	
	function getArtistsToShow (  ) {
		$artists_to_show = [];
		$size = ( $this->num_artists_to_show <= count ( $this->all_artists ) ) ? $this->num_artists_to_show : count ( $this->all_artists );
		for ( $i = 0 ; $i < $size ; $i++ ) {
			$artists_to_show[] = $this->all_artists[$i];
		}
		return $artists_to_show;
	}
	
	function getSongsToShow (  ) {
		$songs_to_show = [];
		$size = ( $this->num_songs_to_show <= count ( $this->all_songs ) ) ? $this->num_songs_to_show : count ( $this->all_songs );
		for ( $i = 0 ; $i < $size ; $i++ ) {
			$songs_to_show[] = $this->all_songs[$i];
		}
		return $songs_to_show;
	}
	
}

?>
