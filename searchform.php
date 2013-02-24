<?php
/* Typeahead search terms bank, cached with transient
-------------------------------------------------- */
global $theme_namespace, $use_theme_transients;
if ( ! $use_theme_transients || false === ( $typeahead = get_transient( $theme_namespace . '_search_terms' ) ) )
{
	$plaintext_cat_walker = new Plaintext_Cat_Walker;
	$args = array(
		'echo' => 0,
		'title_li' => '',
		'style' => 'none',
		'walker' => $plaintext_cat_walker
	);
	$cats = wp_list_categories( $args );
	$cats = rtrim( $cats, ', ' );

	$args = array(
		'echo' => 0,
		'title_li' => '',
		'style' => 'none',
		'taxonomy' => 'post_tag',
		'walker' => $plaintext_cat_walker
	);
	$tags = wp_list_categories( $args );
	$tags = rtrim( $tags, ', ' );

	$typeahead = '[' . $cats . ', ' . $tags . ']';

	if ( $use_theme_transients )
		set_transient( $theme_namespace . '_search_terms', $typeahead, MINUTE_IN_SECONDS );
}
?>

<form class="span3 search search-built-in" role="search" method="get" id="searchform" action="http://localhost/">
	<div class="input-group search-container">
		<label class="screen-reader-text hidden" for="s">Search for:</label>
		<input type="text" value="" name="s" id="s" data-provide="typeahead" data-source='<?php echo $typeahead; ?>'/>
		<!-- <button class="btn" type="button">Search</button> -->
		<div class="input-group-btn">
			<button class="btn" type="submit"><i class="glyphicon glyphicon-search"></i></button>
		</div>
	</div>
</form>
