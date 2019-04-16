<? require_once $_SERVER['DOCUMENT_ROOT'] . '/main_tag_includes/html_pre.php'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	
	<? require_once DR . '/sections/head.php'; ?>
	
	<body>
		<? require DR . '/main_tag_includes/body_start_inside.php'; ?>
		<div id="page" class="search_<? echo $page->user_wants; ?>">
		
			<? if ( Dev::$show_top ) { require DR . '/sections/vars.php'; } ?>
			
			<? require DR . '/sections/header.php'; ?>
			<? require DR . '/sections/header2.php'; ?>
			
			<? require DR . '/sections/content.php'; ?>
			<? require DR . '/sections/content2.php'; ?>
			
			<? require DR . '/sections/footer.php'; ?>
			
			<? require DR . '/sections/debug.php'; ?>
		
			<? if ( Dev::$show_bottom ) { require DR . '/sections/vars.php'; } ?>
			
		</div>
		
		<? require DR . '/main_tag_includes/body_end_inside.php'; ?>
		
	</body>
	
</html>

<? require $_SERVER['DOCUMENT_ROOT'] . '/main_tag_includes/html_post.php'; ?>
