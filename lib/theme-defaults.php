<?php

//* Simple Social Icon Defaults
add_filter( 'simple_social_default_styles', 'minimal_social_default_styles' );
function minimal_social_default_styles( $defaults ) {

	$args = array(
		'alignment'              => 'aligncenter',
		'background_color'       => '#f5f5f5',
		'background_color_hover' => '#f7595a',
		'border_radius'          => 50,
		'icon_color'             => '#222222',
		'icon_color_hover'       => '#ffffff',
		'size'                   => 36,
		);
		
	$args = wp_parse_args( $args, $defaults );
	
	return $args;
	
}