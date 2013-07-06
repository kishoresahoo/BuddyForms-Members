<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress, BuddyForms
 * @author		Sven Lehnert
 * @copyright	2013, Sven Lehnert
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the redirect link
 * 
 * @package BuddyForms
 * @since 0.3 beta
 */
function bf_members_get_redirect_link( $id = false ) {
	global $bp, $buddyforms, $wp_query;
		
	if( ! $id )
		return false;
	
	$link = '';
	if(isset($buddyforms['selected_post_types'])){
		foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
				
			if(isset($buddyforms['buddyforms'][$selected_post_type['form']]['attached_page']))
				$attached_page_id = $buddyforms['buddyforms'][$selected_post_type['form']]['attached_page'];
			
			if(isset($attached_page_id) && $attached_page_id == $id){

				$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/';
				
				if(isset($bp->unfiltered_uri[1])){
					if($bp->unfiltered_uri[1] == 'create')
						$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/create/';
					if($bp->unfiltered_uri[1] == 'edit')
						$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/edit/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];
					if($bp->unfiltered_uri[1] == 'delete')
						$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/delete/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];
					if($bp->unfiltered_uri[1] == 'revison')
						$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/revison/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];
				}
				
			}
				
		}
	}

	return apply_filters( 'bf_members_get_redirect_link', $link );
}

/**
 * Redirect the user to their respective profile page
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
function bf_members_redirect_to_profile() {
	global $post, $wp_query, $bp;

	if( ! isset( $post->ID ) || ! is_user_logged_in() )
		return false;

	$link = bf_members_get_redirect_link( $post->ID );

	if( ! empty( $link ) ) :
		wp_safe_redirect( $link );
		exit;
	endif;
}
add_action( 'template_redirect', 'bf_members_redirect_to_profile' );

/**
 * Link router function
 *
 * @package BuddyForms
 * @since 0.3 beta
 * @uses	bp_get_option()
 * @uses	is_page()
 * @uses	bp_loggedin_user_domain()
 */
function bf_members_page_link_router( $link, $id )	{
	if( ! is_user_logged_in() || is_admin() )
		return $link;

	$new_link = bf_members_get_redirect_link( $id );

	if( ! empty( $new_link ) )
		$link = $new_link;

	return apply_filters( 'bf_members_router_link', $link );
}
add_filter( 'page_link', 'bf_members_page_link_router', 10, 2 );