<?php
	if(!isset($description))
		$description = "CHANGE ME";

	if(!isset($keywords))
		$keywords = "CHANGE ME";

	if(!isset($titlePage))
        $titlePage = "CHANGE ME";
?>		

<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $titlePage ?>  | <?php echo WEB_TITLE ?></title>
		<meta name="description" content="<?php echo $description; ?>">
		<meta name="keywords" content="<?php echo $keywords; ?>">
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">

		<base href="" />

		<!-- Google Analytics -->
		<script>

		</script>
		<!-- End Google Analytics -->

        <!-- Piwik -->
        <script>

        </script>
        <!-- Piwik -->

		<!-- Load CSS file -->
		<link href="<?php WEBROOT ?>assets/css/CHANGEME" media="screen" rel="stylesheet" type="text/css">

		<!-- Load JS files -->
		<script src="<?php WEBROOT ?>assets/js/jquery.min.js" type="text/javascript"></script>
		<!--[if lt IE 9] -->
		<script src="<?php WEBROOT ?>assets/js/html5shiv.js" type="text/javascript"></script>
		<!-- [endif]-->		
		<script src="<?php WEBROOT ?>assets/js/CHANGEME"></script>
	</head>
	<body>
	    <header>
	    	MENU
	    </header>
	    <main>
	      	<?php echo $content; ?>
	    </main>
	</body>
</html>