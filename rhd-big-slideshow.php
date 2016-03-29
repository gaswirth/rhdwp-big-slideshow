<?php
/**
 * Plugin Name: RHD Big Slideshow
 * Description: Big full-screen custom slider, using cycle2
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 0.1
**/


function rhd_big_slideshow_enqueue()
{
	wp_enqueue_style( 'rhd-big-slideshow', plugin_dir_url(__FILE__) . '/css/rhd-big-slideshow.css', array(), '0.1', 'all' );
	wp_enqueue_script( 'jquery-cycle2', plugin_dir_url(__FILE__) . '/js/vendor/jquery-cycle2/build/jquery.cycle2.min.js', array( 'jquery' ), '2.1.6', true );
}
add_action( 'wp_enqueue_scripts', 'rhd_big_slideshow_enqueue' );


/**
 * rhd_big_slideshow function.
 *
 * @access public
 * @param mixed $the_post
 * @return void
 */
function rhd_big_slideshow( $the_post ) {
	if ( ! has_shortcode( $the_post->post_content, 'gallery' ) )
		return apply_filters( 'the_content', $the_post->post_content );

	$gallery = get_post_gallery( $the_post, false );

	$args = array(
		'post_type'			=> 'attachment',
		'posts_per_page'	=> -1,
		'post_status'		=> 'any',
		'post__in'			=> explode( ',', $gallery['ids'] )
	);
	$attachments = get_posts( $args );

	$cycle_args = 'data-cycle-overlay-template="<span class=\'caption\'>{{rhdCaption}}</span>" data-cycle-caption-plugin=caption2 data-cycle-overlay-fx-sel=">div" data-cycle-pager="#rhd-cycle-pager" data-cycle-pager-template="<a href=\'#\'><img src=\'{{src}}\' width=80 height=80></a>" data-cycle-swipe=true data-cycle-swipe-fx=scrollHorz';

	if ( $attachments ) {
		echo '<div class="rhd-big-slideshow cycle-slideshow"' . $cycle_args . '>';
		echo '<div class="cycle-overlay"></div>';

		foreach ( $attachments as $attachment ) {
			// Attributes
			$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
			$title = $attachment->post_title;
			$url = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$caption = ( ! empty( $attachment->post_content ) ) ? $attachment->post_content : 'Temporary Caption';

			// Make sure ALT is not empty
			if ( empty ( $alt ) )
				$alt = $attachment->post_excerpt;

			if ( empty ( $alt ) )
				$alt = $attachment->post_title;

			echo '<img class="rhd-big-slideshow-image" src="' . $url[0] . '" alt="' . $alt . '" data-rhd-caption="' . $caption . '">';
		}

		echo '</div>';
		echo '<div id="rhd-cycle-pager"></div>';
	}
}
?>