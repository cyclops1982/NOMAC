<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage NOMAC
 * @since Twenty Ten 1.0
 */

get_header();
?>
<div class="content" role="main">

	<div id="post-0" class="post page error404 not-found">
		<h1>[404] Niet gevonden</h1>
		<p>Sorry, maar we konden niet vinden wat je zocht. Misschien helpt de search functie.</p>
	</div>

<?php
get_template_part( 'searchform', 'search' );
?>

	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

</div>

<?php 

get_sidebar();
get_footer(); 
?>
