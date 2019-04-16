<?

require $_SERVER['DOCUMENT_ROOT'] . '/_js/ajax/resources.php';
$query = ( isset ( $_GET['query'] ) ) ? $_GET['query'] : NULL;
$search = new Search ( $query );

?>
	
<p class="query">Searched for "<? echo $search->query; ?>".</p>

<div class="songs">
	<h2>Songs:</h2>
	<? if ( $search->num_songs ) { ?>
		<ul>
			<? for ( $i = 0 ; $i < $search->num_songs ; $i++ ) { ?>
				<?
					$top_chart_rank_week = $search->all_songs[$i]->top_chart_rank_week;
					$top_chart_rank_week_sexy = $search->all_songs[$i]->top_chart_rank_week_sexy;
					$top_chart_rank = $search->all_songs[$i]->top_chart_rank;
					$song_title = $search->all_songs[$i]->title;
					$song_artist = $search->all_songs[$i]->artist->name;
				?>
				<li>
					<a href="index.php?type=week&playlist_name=<? echo $top_chart_rank_week; ?>&rank=<? echo $top_chart_rank; ?>&action=search_result_song" >
						<span class="song"><? echo $song_title; ?> </span>
						<span class="artist">(<? echo $song_artist; ?>)</span>
						<span class="top_chart_rank_and_week">Ranked #<? echo $top_chart_rank; ?> on <? echo $top_chart_rank_week_sexy; ?>.</span>
					</a>
				</li>
			<? } ?>
		</ul>
	<? } else { ?>
		<p>No songs found.</p>
	<? } ?>
</div>

<div class="artists">
	<h2>Artists:</h2>
	<? if ( $search->num_artists ) { ?>
		<ul>
			<? for ( $i = 0 ; $i < $search->num_artists ; $i++ ) { ?>
				<?
					$song_artist = $search->all_artists[$i]->name;
					$artist_song_count = $search->all_artists[$i]->song_count_ui;
				?>
				<li>
					<a href="index.php?type=artist&playlist_name=<? echo urlencode ( $song_artist ); ?>&action=search_result_artist" >
						<span class="artist"><? echo $song_artist; ?></span>
						<span class="song_count"><? echo $artist_song_count; ?></span>
					</a>
				</li>
			<? } ?>
		</ul>
	<? } else { ?>
		<p>No artists found.</p>
	<? } ?>
</div>

<div id="close_search">
	<img class="orange" src="/assets/images/close-window-icon-orange.png" >
	<img class="white" src="/assets/images/close-window-icon-white.png" >
</div>

<script>
	$(document).ready(function() {
		$('#query').css("background-image","url('/assets/images/mag_glass.png')");
	});
</script>


