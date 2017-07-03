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
		<link href="<?php WEBROOT ?>assets/css/semantic.min.css" media="screen" rel="stylesheet" type="text/css">

		<!-- Google Webfont -->
		<link href='https://fonts.googleapis.com/css?family=PT+Mono' rel='stylesheet' type='text/css'>
		<!-- Themify Icons -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/themify-icons.css">
		<!-- Icomoon Icons -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/icomoon-icons.css">
		<!-- Bootstrap -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/bootstrap.css">
		<!-- Owl Carousel -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/owl.carousel.min.css">
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/owl.theme.default.min.css">
		<!-- Magnific Popup -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/magnific-popup.css">
		<!-- Easy Responsive Tabs -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/easy-responsive-tabs.css">
		<!-- Theme Style -->
		<link rel="stylesheet" href="<?php WEBROOT ?>assets/css/style.css">

		<!-- Load JS files -->
		<script src="<?php WEBROOT ?>assets/js/jquery.min.js" type="text/javascript"></script>
		<!--[if lt IE 9] -->
		<script src="<?php WEBROOT ?>assets/js/modernizr-2.6.2.min.js"></script>
		<script src="<?php WEBROOT ?>assets/js/respond.min.js"></script>
		<!-- [endif]-->		
	</head>
	<body>
	    <header id="fh5co-header" role="banner">    
	      <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	        <div class="container">
	          <div class="navbar-header"> 
	            <!-- Mobile Toggle Menu Button -->
	            <a href="#" class="js-fh5co-nav-toggle fh5co-nav-toggle" data-toggle="collapse" data-target="#fh5co-navbar" aria-expanded="false" aria-controls="navbar"><i></i></a>
	            <a class="navbar-brand" href="index.html">Build</a>
	          </div>
	          <div id="fh5co-navbar" class="navbar-collapse collapse">
	            <ul class="nav navbar-nav">
	              <li><a href="inside-page.html">Inside Page</a></li>
	              <li><a href="elements.html">Elements</a></li>
	            </ul>
	            <ul class="nav navbar-nav navbar-right">
	              <li><a href="#" class="btn btn-calltoaction btn-primary">Get in touch</a></li>
	            </ul>
	          </div>
	        </div>
	      </nav>
		</header>
		  <div id="fh5co-hero" style="background-image: url(images/hero_2.jpg)">
    <a href="#fh5co-main" class="smoothscroll animated bounce fh5co-arrow"><i class="ti-angle-down"></i></a>
    <div class="overlay"></div>
    <div class="container">
      <div class="col-md-8 col-md-offset-2">
        <div class="text">
          <h1> <strong >BUILD</strong> a free HTML5 template <em>by</em> <strong>FREEHTML5.co</strong></h1>
        </div>
      </div>
    </div>
  </div>

	    <main>
	      	<?php echo $content; ?>
	    </main>
	    <footer role="contentinfo" id="fh5co-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="footer-box border-bottom">
            <h3 class="footer-heading">About Us</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem reiciendis consequuntur nisi enim similique </p>
          </div>
          
            <h3 class="footer-heading">Subscribe</h3>
            <form action="#" class="form-subscribe">
              <div class="field">
                <input type="email" class="form-control" placeholder="hello@gmail.com">
                <button class="btn btn-primary">Subscribe</button>
              </div>
            </form>
            <div class="fh5co-spacer fh5co-spacer-sm"></div>
          
        </div>
        <div class="col-md-3 col-sm-6">
          
            <h3 class="footer-heading">Recent Blog</h3>
            <ul class="footer-list">
              <li><a href="#">Nobis odio nulla autem aliquam vitae doloremque.</a></li>
              <li><a href="#">Consectetur adipisicing elit.</a></li>
              <li><a href="#">Lorem ipsum dolor sit amet.</a></li>
            </ul>
          
        </div>


        <div class="visible-sm clearfix"></div>


        <div class="col-md-3 col-sm-6">
          
            <h3 class="footer-heading">Categories</h3>
            <ul class="footer-list">
              <li><a href="#"><abbr title="Hypertext Markup Language 5">HTML5</abbr></a></li>
              <li><a href="#"><abbr title="Cascading Stylesheets 3">CSS 3</abbr></a></li>
              <li><a href="#">jQuery</a></li>
              <li><a href="#">Free HTML5</a></li>
            </ul>
          
        </div>


        <div class="col-md-3 col-sm-6 clearfix">

          <div class="row">
            <div class="col-md-6 col-sm-6">
              <div class="footer-box">
                <h3 class="footer-heading">Get in Touch</h3>
                <ul class="footer-list">
                  <li><a href="#">Our Team</a></li>
                  <li><a href="#">Contact Us</a></li>
                  <li><a href="#">Privacy Policy</a></li>
                  
                </ul>
              </div>
            </div>
            <div class="col-md-6 col-sm-6">
              <div class="footer-box">
                <h3 class="footer-heading">Support</h3>
                <ul class="footer-list">
                  <li><a href="#">FAQ's</a></li>
                  <li><a href="#">Knowledgebase</a></li>
                  <li><a href="#">Forum</a></li>
                </ul>
              </div>
            </div>
          </div>
          
        </div>
        
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="copyright">
            <p class="text-center"><small>A Free HTML5 Template by 
            <a href="http://freehtml5.co/">FREEHTML5.co</a> Images: <a href="http://unsplash.com/" target="_blank">Unsplash</a><br> &copy; 2015 Display. All Rights Reserved.</small></p>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!-- Go To Top -->
    <a href="#" class="fh5co-gotop"><i class="ti-shift-left"></i></a>
    
      
    <!-- jQuery -->
    <script src="<?php WEBROOT ?>assets/js/jquery-1.10.2.min.js"></script>
    <!-- jQuery Easing -->
    <script src="<?php WEBROOT ?>assets/js/jquery.easing.1.3.js"></script>
    <!-- Bootstrap -->
    <script src="<?php WEBROOT ?>assets/js/bootstrap.js"></script>
    <!-- Owl carousel -->
    <script src="<?php WEBROOT ?>assets/js/owl.carousel.min.js"></script>
    <!-- Magnific Popup -->
    <script src="<?php WEBROOT ?>assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Easy Responsive Tabs -->
    <script src="<?php WEBROOT ?>assets/js/easyResponsiveTabs.js"></script>
    <!-- FastClick for Mobile/Tablets -->
    <script src="<?php WEBROOT ?>assets/js/fastclick.js"></script>

    <!-- Main JS -->
    <script src="<?php WEBROOT ?>assets/js/main.js"></script>

	</body>
</html>