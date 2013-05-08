<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Modal Content</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_print_styles() ; ?>
</head>
<body>

	<section class="main" role="main">
		<header>
			<h1>My Modal</h1>
		</header>
		<article >
			<p>You should probably do something with this...</p>
		</article>
		<footer>
			<div class="inner text-right">
				<button id="btn-cancel" class="button-large">Cancel</button>
				<button id="btn-ok" class="button-primary button-large">OK</button>
			</div>
		</footer>
	</section>

<?php wp_print_scripts( ) ;?>
</body>
</html>