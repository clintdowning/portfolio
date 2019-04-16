<? require $_SERVER['DOCUMENT_ROOT'] . '/main-tag-includes/html-open-outside.php'; ?><!doctype html>
<html id="core">
<head <? echo $page->facebook_struc->namespace; ?>>
	<meta charset="utf-8">
	<!-- Anti-Caching - Prevent client caching of resources. -->
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
		<meta http-equiv="Pragma" content="no-cache"/>
		<meta http-equiv="Expires" content="0"/>
	<meta name="viewport" content="width=device-width, initial-scale=0.60, maximum-scale=1, user-scalable=0" >
	<meta name="format-detection" content="telephone=no">
	<? echo $page->facebook_struc->meta_final; ?>
	<title><? echo $page->title; ?></title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<? require DR . '/main-tag-includes/head-close-inside.php'; ?>
	<? require DR . '/_css/_delta.php'; ?>
</head>

<body>
	
	<? //echo $facebook->displayInterface(); ?>

	<? require DR . '/dev/test_vars.php'; ?>
	
	<div class="mj_main" >

		<div class="player_group" >
			<header>
				<div class="clearfix">
					<a href="index.php" >
						<img class="logo_header" src="/assets/images/musicjig_app_icon_medium.png" alt="MusicJig Logo" />
						<h1><? echo $domains['short']; ?></h1>
					</a>
					<? $facebook->share(); ?>
					<h2><? echo $page_slogan; ?></h2>
				</div>
			</header>
			<? require DR . '/sections/nav.php'; ?>
			<div id="mj_player" >
				<div id="player"></div>
				<? //require DR . '/sections/controls.php'; ?>
				<div class="title_artist" >
					<div class="text clearfix" >
						<h2 class="title" ><? echo $page->playlist->songs[$page->playlist->first_song_index]->showTitle('list_viewable'); ?></h2>
						<h3 class="artist" ><? echo $page->playlist->songs[$page->playlist->first_song_index]->artist->showArtist('list_viewable'); ?></h3>
					</div>
					<div class="youtube_powered" >
						<a href="https://www.youtube.com/" target="_blank">
							<img src="/assets/images/youtube/developed-with-youtube-sentence-case-light-mod.png" alt="Developed with YouTube Logo" />
						</a>
						<p>This app is not associated or endorsed by YouTube and <br/>does not represent the views or opinions of YouTube or YouTube personnel.</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="scrolling" >
			<? //$facebook->buildUI(); ?>
			<? $page->playlist->buildList(); ?>
		</div>

	</div>
	
	<? require DR . '/sections/modal.php'; ?>
	
	<? require DR . '/main-tag-includes/body-close-inside.php'; ?>
	
</body>
</html>

<? require DR . '/main-tag-includes/body-close-outside.php'; ?>
