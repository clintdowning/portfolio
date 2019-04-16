<? require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/html_pre_processing.php'; ?>
<? require_once $_SERVER['DOCUMENT_ROOT'] . '/content_processing/_delta.php'; ?>

<!DOCTYPE html>
<html lang="en">
<? require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/head.php'; ?>
<body id="page1" >
	<? require $_SERVER['DOCUMENT_ROOT'] . '/main_tag_includes/body_start_inside.php'; ?>
	<? require $_SERVER['DOCUMENT_ROOT'] . '/includes/debug_vars.php'; ?>
	<? require $_SERVER['DOCUMENT_ROOT'] . '/includes/developer-nav.php'; ?>
	<? require $_SERVER['DOCUMENT_ROOT'] . '/includes/pro_framework.php'; ?>
	<? require $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
    
	<!-- GET PAGE MAIN CONTENT -->
	
		<? 
            if ( isset ( $_GET['page'] ) ) {
                switch ( $_GET['page'] ) {
                    case 'actors':
                    case 'movie':
                        require $_SERVER['DOCUMENT_ROOT'] . '/content/page/' . $_GET['page'] . '.php'; 
                        break;
					case 'actor':
						require $_SERVER['DOCUMENT_ROOT'] . '/content/page/' . $_GET['page'] . '.php'; 
						break;
                    default:
                        require $_SERVER['DOCUMENT_ROOT'] . '/content_processing/page/actors.php';
                        break;
                }
            } else {
                require $_SERVER['DOCUMENT_ROOT'] . '/content_processing/page/actors.php'; 
            }
        ?>
    
	<? require $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
	<? require $_SERVER['DOCUMENT_ROOT'] . '/main_tag_includes/body_end_inside.php'; ?>
	
</body>
</html>

<? require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/html_post_processing.php'; ?>
