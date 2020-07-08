<?php
	$route = get_route();
	
	if(isset($route[1])) {
		switch($route[1]) {
			case 'elearner': $active_menu = 68;
				break;
			case 'collegiate': $active_menu = 67;
				break;
			case 'medical': $active_menu = 66;
				break;
			case 'intermediate': $active_menu = 65;
				break;
			case 'elementary': $active_menu = 64;
				break;
		}
	}

	$current_user = wp_get_current_user();
	$is_user_logged_in = is_user_logged_in();

	$cart_items = get_cart_items();

	$locale_code = explode('_', get_locale());

	// enqueue math specific css and js
	function ik_enqueue_math_css() {
		wp_enqueue_style('common-math', get_stylesheet_directory_uri() . '/library/css/common-math.css');
		wp_enqueue_script('common-math', get_stylesheet_directory_uri() . '/library/js/common-math.js', array('common-js'));
	}
	add_action('wp_enqueue_scripts', 'ik_enqueue_math_css', '999');
?>
<!DOCTYPE html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title><?php wp_title(''); ?></title>

		<?php // <meta name="HandheldFriendly" content="True"> ?>
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
            <meta name="theme-color" content="#121212">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php if(!is_admin_panel()) : ?>
			<script src="<?php echo get_template_directory_uri(); ?>/library/js/iklearn.js"></script>
		<?php endif ?>
		<script>var home_url = "<?php echo home_url() ?>", LANG_CODE = "<?php echo $locale_code[0] ?>", isuserloggedin = <?php echo $is_user_logged_in ? 1 : 0 ?>;</script>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>		

		<?php if(is_admin_panel()) : ?>
			<style type="text/css">
				a.sign-up-link {
					pointer-events: none !important;
					cursor: default !important;
					color: #999 !important;
				}
			</style>
		<?php endif ?>

		<?php if(isset($active_menu)) : ?>
			<style type="text/css">
				#main-nav nav .main-menu li#menu-item-<?php echo $active_menu ?> a {
					color: #FFF;
				}
			</style>
		<?php endif ?>

	</head>

	<body <?php body_class('body-math'); ?> itemscope itemtype="http://schema.org/WebPage">
	
		<div id="container">

			<header class="header header-main" itemscope itemtype="http://schema.org/WPHeader">

				<div class="top-nav"></div>

				<div class="main-nav-block"></div>

				<div class="container" style="position: relative">		
				
                                        <div id="logo" >
						<a href="<?php echo site_home_url(); ?>" rel="nofollow" title="<?php bloginfo('name'); ?>">
							<img src="<?php echo get_template_directory_uri(); ?>/library/images/logo-ik.png" alt="" style="height: auto; margin: 0;">
						</a>
					</div>
			
					<div id="sub-logo">
                                            <a href="<?php echo site_home_url(); ?>/" rel="nofollow" title="Innovative Knowledge">
                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/6.jpg" alt="">
						</a>
					</div>
                                    
                                        <div id="header-sat" class="cs-button-sat">
                                            <a style="color:#B0B0B0; text-decoration: none;" href="<?php echo site_home_url(); ?>/?r=ensat">SAT</a>
					</div>
                                    
                                        <div id="header-math" class="cs-button-math">
                                            <a style="color:#B0B0B0; text-decoration: none;" href="<?php echo site_math_url();?>">MATH</a>
					</div>
                                    
                                        <div id="header-english" class="cs-button-english">
                                            <a style="color:#B0B0B0; text-decoration: none;" href="<?php echo site_home_url(); ?>">ENGLISH</a>
					</div>

					<?php if(defined('IK_TEST_SERVER')) : ?>
						<div style="position: absolute;left: 240px;top: 5px">
							<h2 style="margin: 0px;color: #fff;font-style: italic;text-shadow: 1px 1px #000">Test Site</h2>
						</div>
					<?php endif ?>

					<?php $URL = $_SERVER['REQUEST_URI'];
                                        $segment = explode('/',$URL);
                                        if($segment[2] == 'home'){
                                                MWHtml::sel_lang_switcher();
                                        }else{
                                                MWHtml::sel_lang_switcher(1);
                                        } ?>
                                    
                                        <?php if ($is_user_logged_in){ ?>
                                            <ul id="user-nav" class="css-pad-log-home">
                                        <?php }else{ ?>
                                            <ul id="user-nav" class="css-pad-nolog-home">
                                        <?php } ?>
                                                <?php if ($is_user_logged_in) : ?>
                                                        <li><a class="display-name css-name-menu-log" href="<?php echo locale_home_url() ?>/?r=my-account">[<?php echo $current_user->display_name ?>]</a></li>
                                                        <li><a class="shopping-cart css-ma-li-home" href="<?php echo home_url_ssl() ?>/?r=payments" title="<?php _e('Shopping Cart', 'iii-dictionary') ?>"><span class="icon-cart3"></span>(<?php echo count($cart_items) ?>)</a></li>
                                                <?php else: ?>
                                                        <li><a class="shopping-cart" href="<?php echo home_url_ssl() ?>/?r=payments" title="<?php _e('Shopping Cart', 'iii-dictionary') ?>"><span class="icon-cart3"></span>(<?php echo count($cart_items) ?>)</a></li>
                                                <?php endif;?>
                                                <?php if (!$is_user_logged_in) : ?>
                                                        <li class="css-li-login"><a href="" id="show_login" title="<?php _e('Login', 'iii-dictionary') ?>"><?php _e('Login', 'iii-dictionary') ?><span class="login-icon"></span></a></li>
                                                        <li class=""><a class="sign-up-link" id="show_signup" href="" title="<?php _e('Sign-up', 'iii-dictionary') ?>"><?php _e('Sign-up', 'iii-dictionary') ?><span class="signup-icon"></span></a></li>
                                                <?php else : ?>
                                                        <li class="css-li-logout"><a class="logout-link css-link-log-in" href="<?php echo wp_logout_url(home_url()) ?>" title="<?php _e('Logout', 'iii-dictionary') ?>"><?php _e('Logout', 'iii-dictionary') ?><span class="logout-icon"></span></a></li>
                                                <?php endif ?>
                                                        <li id="icon-home-hidden"><a href="<?php echo site_home_url(); ?>/home" title="<?php _e('Home', 'iii-dictionary') ?>"><?php _e('Home', 'iii-dictionary') ?><span class="home-icon"></span></a></li>
                                        </ul>
                                    
					<div id="btn-main-menu" class="btn-menu-collapse"></div>

					<div id="main-nav" class="row">
                                                <div class="menu-new-head">
                                                    <div class="btn-menu-sat"><a href="<?php echo site_home_url().'/?r=ensat'?>" style="color:#bd5454">SAT</a></div>
                                                    <div class="btn-menu-math"><a href="<?php echo site_math_url()?>" style="color:#449f7e">MATH</a></div>
                                                    <div class="btn-menu-english"><a href="<?php echo site_home_url()?>" style="color:#488aca">ENGLISH</a></div>
                                                </div>
                                                
						<!--<div id="btn-sub-menu" class="btn-menu-collapse"></div>-->

						<nav class="navbar navbar-default menu-home" id="sub-user-nav">
							<?php wp_nav_menu(array(
									 'container' => false,                           // remove nav container
									 'container_class' => '',                 // class of container (should you choose to use it)
									 'menu' => 'Function Menu',  // nav name
									 'menu_class' => 'user-nav nav navbar-nav ',               // adding custom nav class
									 'theme_location' => 'user-nav',                 // where it's located in the theme
									 'before' => '',                                 // before the menu
									   'after' => '',                                  // after the menu
									   'link_before' => '',                            // before each link
									   'link_after' => '',                             // after each link
									   'depth' => 0,                                   // limit the depth of the nav
									 'fallback_cb' => '',                             // fallback function (if there is one)
									 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
							)); ?>
						</nav>

						<nav class="navbar navbar-default" id="lang-switcher-nav">
							<?php wp_nav_menu(array(
									 'container' => false,                           // remove nav container
									 'container_class' => '',                 // class of container (should you choose to use it)
									 'menu_class' => 'menu-lang-switcher nav navbar-nav',               // adding custom nav class
									 'theme_location' => 'lang-switcher-nav',                 // where it's located in the theme
									 'before' => '',                                 // before the menu
									   'after' => '',                                  // after the menu
									   'link_before' => '',                            // before each link
									   'link_after' => '',                             // after each link
									   'depth' => 0,                                   // limit the depth of the nav
									 'fallback_cb' => ''                             // fallback function (if there is one)
							)); ?>
						</nav>
					</div>
                                    <!-- Title horizontal  -->
                                        <nav class="navbar navbar-default css-not-show-mb css-horizontal-home" >
                                            <a href="#" rel="nofollow" title="Innovative Knowledge">
                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/15.jpg" alt="" id="img-logo">
                                            </a>
                                        </nav>
                                    
                                    <!-- Sub menu horizontal  -->
                                        <nav class="navbar navbar-default css-only-show-desktop" id="sub-menu-horizontal-home">
                                                <?php wp_nav_menu(array(
                                                                 'container' => false,                           // remove nav container
                                                                 'container_class' => '',                 // class of container (should you choose to use it)
                                                                 'menu' => 'Function Menu',  // nav name
                                                                 'menu_class' => 'user-nav nav navbar-nav ',               // adding custom nav class
                                                                 'theme_location' => 'user-nav',                 // where it's located in the theme
                                                                 'before' => '',                                 // before the menu
                                                                   'after' => '',                                  // after the menu
                                                                   'link_before' => '',                            // before each link
                                                                   'link_after' => '',                             // after each link
                                                                   'depth' => 0,                                   // limit the depth of the nav
                                                                 'fallback_cb' => '',                             // fallback function (if there is one)
                                                                 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
                                                )); ?>
                                        </nav>
				</div>
<div class="">
                                    <?php get_template_part('ajax', 'auth'); ?>
                                    </div>
			</header>
