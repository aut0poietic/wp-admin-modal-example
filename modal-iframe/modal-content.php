<?php
	/**
	 * IFrame Modal Content Page
	 * This is where you'd build your content and because it's referenced using a wp_ajax method, we have access
	 * to the complete WordPress system, can submit the page to itself ( so long as you preserve the action GET
	 * parameter ) or whatever else you need.
	 *
	 * Expected GET values:
	 *          [action] => modal_frame_content
	 *          [post_id] => ( the ID of the currently active post )
	 */
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php _e( 'IFrame Modal' , 'iframe_modal' ); ?></a></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	/**
	 * We call wp_print_styles ourselves, in order to replicate the functionality of wp-admin pages.
	 */
	wp_print_styles();
	?>
</head>
<body>
<div class="navigation-bar">
	<nav>
		<ul>
			<li><a href="#"><?php _e( 'Option One' , 'iframe_modal' ); ?></a></li>
			<li><a href="#"><?php _e( 'Option Two' , 'iframe_modal' ); ?></a></li>
			<li class="separator">&nbsp;</li>
			<li><a href="#"><?php _e( 'Option Three' , 'iframe_modal' ); ?></a></li>
		</ul>
	</nav>
</div>
<section class="main" role="main">
	<header>
		<h1><?php _e( 'IFrame Modal' , 'iframe_modal' ); ?></h1>
	</header>
	<article>
		<p><?php _e( 'This would be where you built your dialog or workflow.' , 'iframe_modal' ); ?></p>
	</article>
	<footer>
		<div class="inner text-right">
			<button id="btn-cancel" class="button-large"><?php _e( 'Cancel' , 'iframe_modal' ); ?></button>
			<button id="btn-ok" class="button-primary button-large"><?php _e( 'Save &amp; Continue' , 'iframe_modal' ); ?></button>
		</div>
	</footer>
</section>
<?php
	/**
	 * We call wp_print_scripts ourselves, in order to replicate the functionality of wp-admin pages.
	 * However, this may need to be expanded to allow for scripts to appear in the head (such as modernizr or shim).
	 */
	wp_print_scripts();
?>
</body>
</html>