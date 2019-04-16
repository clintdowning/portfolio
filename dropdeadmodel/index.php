<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/pre-processing.php'; ?>

<!DOCTYPE html>

<html lang="en">

	<?php require 'includes/head.php'; ?>
    
    <body id="page1">

		<?php if(myip()){require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/pro-framework-panel.php';} ?>

        <div class="main-bg">
        
			<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
			
			<?php
                if ( isset ( $_GET["content"] ) ) {
					switch ( $_GET["content"] ) {
						case 'contact':
						case 'privacy':
						case 'rate':
						case 'terms':
							require_once $_SERVER['DOCUMENT_ROOT'] . "/content/" . $_GET["content"] . ".php";
							break;
						case 'how-to-be-a-model':
							require_once $_SERVER['DOCUMENT_ROOT'] . "/content/ad/" . $_GET["content"] . ".php";
							break;
						default:
							require_once $_SERVER['DOCUMENT_ROOT'] . '/content/01_primary.php';
					}
                    
                } else {
					require_once $_SERVER['DOCUMENT_ROOT'] . '/content/01_primary.php';
                }
            ?>

        </div>
        
        <div class="border-bottom">
        </div>
        
		<?php // require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/aside.php'; ?>
        
		<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
        
    </body>

</html>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/post-processing.php'; ?>
