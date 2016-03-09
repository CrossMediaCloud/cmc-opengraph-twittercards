<?php
/*
Plugin Name: Cross Media Cloud | Open Graph, Twitter Cards for Website
Description: Add Open Graph and Twitter Cards to website
Version: 3.7
Author: Cross Media Cloud
Author URI: http://www.cross-media-cloud.de/
Author Email: post@cross-media-cloud.de
License: GPL
Textdomain: cmc-opengraph-twittercrads
*/

class CrossMediaCloudOpenGraphTwitterCards {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'cmc_opengraph_twittercards_textdomain' ) );

		// Core functions
		add_action( 'wp_head', array( $this, 'cmc_opengraph_twittercards_add_to_head' ) );

		// Add fields to options
		add_action( 'admin_init', array( $this, 'cmc_opengraph_twittercards_register_seoDescription' ) );
		add_action( 'admin_init', array( $this, 'cmc_opengraph_twittercards_register_seoTitle' ) );
		add_action( 'admin_init', array( $this, 'cmc_opengraph_twittercards_register_publisher_gplus_id' ) );
		add_action( 'admin_init', array( $this, 'cmc_opengraph_twittercards_register_publisher_twitter_account' ) );
		add_action( 'admin_init', array( $this, 'cmc_opengraph_twittercards_register_metaimage' ) );

		// Add a short description to author profile
		add_action( 'show_user_profile', array( $this, 'cmc_opengraph_twittercards_add_author_shortBio' ) );
		add_action( 'edit_user_profile', array( $this, 'cmc_opengraph_twittercards_add_author_shortBio' ) );

		// Save the short description to author profile
		add_action( 'personal_options_update', array( $this, 'cmc_opengraph_twittercards_save_author_shortBio' ) );
		add_action( 'edit_user_profile_update', array( $this, 'cmc_opengraph_twittercards_save_author_shortBio' ) );

		// Add Meta-Box to Post-Edit-Page
		add_action( 'add_meta_boxes', array( $this, 'cmc_opengraph_twittercards_add_post_meta_box' ) );

		// Do something with the data entered */
		add_action( 'save_post', array( $this, 'cmc_opengraph_twittercards_save_post_meta_box' ) );

		// Modernize the User-Profile
		add_filter( 'user_contactmethods', array( $this, 'cmc_opengraph_twittercards_modernize_userprofile' ), 10, 1 );

		//add_filter( 'wp_title', array( $this, 'cmc_opengraph_twittercards_better_title' ), 1001, 3 );

	} // end constructor

	/**
	 * Loads the plugin text domain for translation
	 */
	public function cmc_opengraph_twittercards_textdomain() {

		load_plugin_textdomain( 'cmc-opengraph-twittercrads', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	} // end plugin_textdomain

	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	function cmc_opengraph_twittercards_add_to_head() {

		if ( is_home() ) {

			// OpenGraph
			$cmc_opengraph_twittercards_blog_meta_title = esc_attr( apply_filters( 'cmc_opengraph_twittercards_blog_meta_title', get_bloginfo( 'blogname' ) . ' | ' . get_bloginfo( 'description' ) ) ); ?>
			<meta property="og:title" content="<?php echo $cmc_opengraph_twittercards_blog_meta_title; ?>">
			<meta property="og:url" content="<?php echo esc_attr( esc_url( get_the_permalink() ) ); ?>">
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

			<?php // Twitter ?>
			<meta property="twitter:title" content="<?php echo $cmc_opengraph_twittercards_blog_meta_title; ?>" />
			<meta property="twitter:url" content="<?php echo esc_attr( esc_url( get_permalink() ) ); ?>" />
			<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">
			<?php
			if ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
				echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
			}

			if ( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) { ?>

				<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) ); ?>">

				<?php // Twitter cards ?>
				<meta property="twitter:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>" />

			<?php }

		} elseif ( is_front_page() ) {

			// OpenGraph
			$cmc_opengraph_twittercards_front_page_meta_title = esc_attr( apply_filters( 'cmc_opengraph_twittercards_front_page_meta_title', get_bloginfo( 'blogname' ) . ' | ' . get_bloginfo( 'description' ) ) ); ?>
			<meta property="og:title" content="<?php echo $cmc_opengraph_twittercards_front_page_meta_title; ?>">
			<meta property="og:type" content="website">
			<meta property="og:url" content="<?php echo esc_attr( home_url() ); ?>">
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

			<?php // Twitter ?>
			<meta property="twitter:title" content="<?php echo $cmc_opengraph_twittercards_front_page_meta_title; ?>" />
			<meta property="twitter:url" content="<?php echo esc_attr( get_permalink() ); ?>" />
			<?php
			if ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
				echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
			}

			// Let's check for a better description
			if ( get_option( 'cmc_opengraph_twittercards_seo_description' ) ) {
				// Get the description
				$cmc_opengraph_twittercards_description_text = esc_attr( strip_tags( get_option( 'cmc_opengraph_twittercards_seo_description' ) ) ); ?>

				<meta name="description" content="<?php echo esc_attr( substr( strip_tags( $cmc_opengraph_twittercards_description_text ), 0, apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ) ); ?>">
				<meta property="og:description" content="<?php echo $cmc_opengraph_twittercards_description_text; ?>">
				<meta property="twitter:description" content="<?php echo $cmc_opengraph_twittercards_description_text; ?>" />
			<?php }

			if ( has_post_thumbnail() ) {

				// Get the data
				$cmc_opengraph_twittercards_meta_thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), apply_filters( 'cmc_opengraph_twittercards_meta_thumbnail_size', 'full' ) );

				// OpenGraph ?>
				<meta property="og:image" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[1] ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[2] ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ) ); ?>">
				<meta property="og:image" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[1] ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[2] ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ) ); ?>">

				<?php // Twitter cards ?>
				<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary_large_image' ); ?>">
				<meta name="twitter:image:src" content="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ) ); ?>">

			<?php } elseif ( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) {

				// OpenGraph ?>
				<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">

				<?php // Twitter cards ?>
				<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">
				<meta property="twitter:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>" />

			<?php }

			// Google plus
			if ( get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) {
				echo '<link href="' . esc_attr( esc_url( 'https://plus.google.com/' . get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) ) . '" rel="publisher" />';
			} ?>

		<?php } elseif ( is_singular() ) {

			$posttags = get_the_tags( get_the_ID() );
			if ( is_array( $posttags ) ) {

				$posttags       = array_values( $posttags );
				$posttag_length = count( $posttags ) - 1;
				echo '<meta name="keywords" content="';
				foreach ( $posttags as $key => $tag ) {
					echo $tag->name;
					if ( $posttag_length != $key ) {
						echo ', ';
					}
				}
				echo '">';

			}

			// OpenGraph Basics ?>
			<meta property="og:title" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_singular_meta_title', strip_tags( get_the_title() ) ) ); ?>">
			<meta property="og:type" content="article">
			<meta property="og:url" content="<?php echo esc_attr( esc_url( get_the_permalink() ) ); ?>">
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

			<?php // Twitter Basics ?>
			<meta property="twitter:title" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_singular_meta_title', strip_tags( get_the_title() ) ) ); ?>" />
			<meta property="twitter:url" content="<?php echo esc_attr( esc_url( get_the_permalink() ) ); ?>" />
			<?php
			// Twitter Accounts from the author
			$cmc_opengraph_twittercards_author_twitter_account = get_user_meta( get_post_field( 'post_author', get_the_ID() ), 'twitter', true );
			if ( strlen( $cmc_opengraph_twittercards_author_twitter_account ) ) {
				echo '<meta name="twitter:creator" content="@' . $cmc_opengraph_twittercards_author_twitter_account . '">';
				if ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
					echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
				}
			} elseif ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
				echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
			}

			if ( get_post_meta( get_the_ID(), 'short_description', true ) ) {
				$cmc_opengraph_twittercards_description_text = esc_attr( strip_tags( get_post_meta( get_the_ID(), 'short_description', true ) ) ); ?>
				<meta name="description" content="<?php echo $cmc_opengraph_twittercards_description_text; ?>">
				<meta property="og:description" content="<?php echo $cmc_opengraph_twittercards_description_text; ?>">
				<meta property="twitter:description" content="<?php echo $cmc_opengraph_twittercards_description_text; ?>" />
			<?php }

			if ( has_post_thumbnail() ) {

				// Get the data
				$cmc_opengraph_twittercards_meta_thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), apply_filters( 'cmc_opengraph_twittercards_meta_thumbnail_size', 'full' ) );

				// OpenGraph ?>
				<meta property="og:image" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[1] ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( $cmc_opengraph_twittercards_meta_thumbnail_url[2] ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ) ); ?>">

				<?php // Twitter cards ?>
				<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary_large_image' ); ?>">
				<meta name="twitter:image:src" content="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_meta_thumbnail_url[0] ) ); ?>">

			<?php } elseif ( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) {

				// OpenGraph ?>
				<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) ); ?>">

				<?php // Twitter cards ?>
				<meta property="twitter:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>" />
				<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">

			<?php }

			// Twitter Accounts from the author
			$cmc_opengraph_twittercards_author_gplus = get_user_meta( get_post_field( 'post_author', get_the_ID(), 'display' ), 'googleplus', true );
			if ( strlen( $cmc_opengraph_twittercards_author_gplus ) ) {
				echo '<link rel="author" href="https://plus.google.com/' . $cmc_opengraph_twittercards_author_gplus . '" />';
				if ( get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) {
					echo '<link href="' . esc_attr( esc_url( 'https://plus.google.com/' . get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) ) . '" rel="publisher" />';
				}
			} elseif ( get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) {
				echo '<link href="' . esc_attr( esc_url( 'https://plus.google.com/' . get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) ) . '" rel="publisher" />';
			} ?>

		<?php } elseif ( is_author() ) { ?>

			<?php
			// Get the datat of the author
			$cmc_opengraph_twittercards_author = get_user_by( 'id', get_post_field( 'post_author', get_the_ID() ) ); ?>
			<meta name="description" content="<?php echo esc_attr( substr( strip_tags( get_the_author_meta( 'shortBio', $cmc_opengraph_twittercards_author->ID ) ), 0, apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ) ); ?>">
			<meta property="og:title" content="<?php echo esc_attr( __( 'About', 'cmc-opengraph-twittercrads' ) . ' ' . $cmc_opengraph_twittercards_author->display_name . ' ' . __( 'at', 'cmc-opengraph-twittercrads' ) . ' ' . get_bloginfo( 'blogname' ) ); ?>">
			<meta property="og:type" content="profile">
			<meta property="og:url" content="<?php echo esc_attr( esc_url( get_author_posts_url( $cmc_opengraph_twittercards_author->ID ) ) ); ?>">
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">
			<meta property="og:description" content="<?php echo esc_attr( substr( strip_tags( get_the_author_meta( 'shortBio', $cmc_opengraph_twittercards_author->ID ) ), 0, apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ) ); ?>">
			<?php // Twitter Basics ?>
			<meta property="twitter:title" content="<?php echo esc_attr( __( 'About', 'cmc-opengraph-twittercrads' ) . ' ' . $cmc_opengraph_twittercards_author->display_name . ' ' . __( 'at', 'cmc-opengraph-twittercrads' ) . ' ' . get_bloginfo( 'blogname' ) ); ?>" />
			<meta property="twitter:url" content="<?php echo esc_attr( esc_url( get_author_posts_url( $cmc_opengraph_twittercards_author->ID ) ) ); ?>" />

			<?php
			// OpenGraph
			preg_match( "/src='(.*?)'/i", get_avatar( $cmc_opengraph_twittercards_author->ID, '250', get_template_directory_uri() . '/img/gravatar-250.png', $cmc_opengraph_twittercards_author->display_name ), $cmc_opengraph_twittercards_author_avatar_url ); ?>
			<meta property="og:image" content="<?php echo esc_attr( $cmc_opengraph_twittercards_author_avatar_url[1] ); ?>">
			<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
			<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
			<link rel="image_src" href="<?php echo esc_attr( esc_url( $cmc_opengraph_twittercards_author_avatar_url[1] ) ); ?>">
			<?php // Twitter cards ?>
			<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">
			<meta property="twitter:image" content="<?php echo esc_attr( $cmc_opengraph_twittercards_author_avatar_url[1] ); ?>" />

		<?php } elseif ( is_archive() ) {

			if ( is_category() ) {
				// save the category-object
				$cmc_archive_category_object_array = get_the_category();
				$cmc_archive_category_object       = $cmc_archive_category_object_array[0];

				// OpenGraph Basics ?>
				<meta property="og:title" content="<?php echo esc_attr( sprintf( __( 'All posts about %s', 'cmc-opengraph-twittercrads' ), $cmc_archive_category_object->name ) ); ?>">
				<meta property="og:url" content="<?php echo esc_attr( esc_url( get_category_link( $cmc_archive_category_object->term_id ) ) ); ?>">
				<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

				<?php // Twitter Basics ?>
				<meta property="twitter:title" content="<?php echo esc_attr( __( 'All posts about %s', 'cmc-opengraph-twittercrads' ), $cmc_archive_category_object->name ); ?>" />
				<meta property="twitter:url" content="<?php echo esc_attr( esc_url( get_category_link( $cmc_archive_category_object->term_id ) ) ); ?>" />
				<?php
				if ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
					echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
				}

				if ( 0 != strlen( $cmc_archive_category_object->description ) ) {
					$cmc_archive_category_description = substr( strip_tags( $cmc_archive_category_object->description ), 0, apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ); ?>
					<meta name="description" content="<?php echo esc_attr( $cmc_archive_category_description ); ?>">
					<meta property="og:description" content="<?php echo esc_attr( $cmc_archive_category_description ); ?>">
					<meta property="twitter:description" content="<?php echo esc_attr( $cmc_archive_category_description ); ?>" />
				<?php } ?>

				<?php // add a logo if set
				if ( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) { ?>
					<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
					<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
					<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
					<link rel="image_src" href="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">

					<?php // Twitter cards ?>
					<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">
					<meta property="twitter:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>" />
				<?php }

			} else { // ELSE - if ( is_category() )

				// Filter to add custoum post types
				$cmc_opengraph_twittercards_custom_archives = apply_filters( 'cmc_opengraph_twittercards_custom_archives', $taxonomy_array = array() );

				if ( count( $cmc_opengraph_twittercards_custom_archives ) ) {

					foreach ( $cmc_opengraph_twittercards_custom_archives as $custom_archive_args ) { ?>

						<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

						<?php // output a url if set
						if ( isset( $custom_archive_args['url'] ) ) {

							// Make the url safe and put it out
							$custom_archive_args['url'] = esc_url( $custom_archive_args['url'] ); ?>
							<meta property="og:url" content="<?php echo esc_attr( $custom_archive_args['url'] ); ?>">

						<?php }

						if ( isset( $custom_archive_args['title'] ) ) {

							// Put the title out ?>
							<meta property="og:title" content="<?php echo esc_attr( $custom_archive_args['title'] ); ?>">

						<?php }

						if ( isset( $custom_archive_args['description'] ) ) {

							// make the string fit. remove tags.
							$custom_archive_args['description'] = substr( strip_tags( $custom_archive_args['description'] ), 0, apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ); ?>
							<meta name="description" content="<?php echo esc_attr( $custom_archive_args['description'] ); ?>">
							<meta property="og:description" content="<?php echo esc_attr( $custom_archive_args['description'] ); ?>">

						<?php }

						// set the var up
						$cmc_opengraph_twittercards_use_default_fallback_image = true;

						// check for incoming setting
						if ( isset( $custom_archive_args['show_default_fallback_image'] ) ) {

							if ( is_bool( $custom_archive_args['show_default_fallback_image'] ) ) {

								// overwrite the original setting
								$cmc_opengraph_twittercards_use_default_fallback_image = $custom_archive_args['show_default_fallback_image'];

							}

						}

						// Check fpr premissions an if a image is set
						if ( $cmc_opengraph_twittercards_use_default_fallback_image AND get_option( 'cmc_opengraph_twittercards_metaimage' ) ) { ?>

							<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
							<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
							<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
							<link rel="image_src" href="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">

						<?php } // END - if ( $cmc_opengraph_twittercards_use_default_fallback_image AND get_option( 'cmc_opengraph_twittercards_metaimage' ) ) {

					}

				} // END - if ( count( $cmc_opengraph_twittercards_custom_archives ) )


			} // END - if ( is_category() ) ?>

		<?php } elseif ( is_search() ) {

			// no OpenGraph here

		} elseif ( is_404() ) {

			// no OpenGraph here

		} else {

			// OpenGraph ?>
			<meta property="og:title" content="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>">
			<meta property="og:url" content="<?php echo esc_attr( esc_url( get_the_permalink() ) ); ?>">
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">

			<?php // Twitter ?>
			<meta property="twitter:title" content="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" />
			<meta property="twitter:url" content="<?php echo esc_attr( esc_url( get_permalink() ) ); ?>" />
			<meta name="twitter:card" content="<?php echo apply_filters( 'cmc_opengraph_twittercards_overwrite_cardtype', 'summary' ); ?>">
			<?php
			if ( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
				echo '<meta property="twitter:site" content="@' . get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) . '" />';
			}

			if ( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) { ?>

				<meta property="og:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
				<meta property="og:image:width" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_width', '250' ) ); ?>">
				<meta property="og:image:height" content="<?php echo esc_attr( apply_filters( 'cmc_opengraph_twittercards_default_metaimage_height', '250' ) ); ?>">
				<link rel="image_src" href="<?php echo esc_attr( esc_url( get_option( 'cmc_opengraph_twittercards_metaimage' ) ) ); ?>">

				<?php // Twitter cards ?>
				<meta property="twitter:image" content="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>" />

			<?php } ?>

		<?php }

	} // end cmc_add_to_head()

	// START SEO Description in Adminmenu -> Genaral
	function cmc_opengraph_twittercards_register_seoDescription() {
		register_setting(
			'general',
			'cmc_opengraph_twittercards_seo_description',
			array(
				$this,
				'cmc_opengraph_twittercards_validate_seoDescription',
			)
		);

		add_settings_field(
			'cmc_opengraph_twittercards_seo_description',
			__( 'SEO Description', 'cmc-opengraph-twittercrads' ),
			array(
				$this,
				'cmc_opengraph_twittercards_formFields_seoDescription',
			),
			'general',
			'default'
		);

	}

	function cmc_opengraph_twittercards_formFields_seoDescription() {
		// echo the field ?>
		<textarea id="cmc_opengraph_twittercards_seo_description" name="cmc_opengraph_twittercards_seo_description" cols="60" rows="5" maxlength="250"><?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_seo_description' ) ); ?></textarea>
		<p class="description"><?php _e( 'Will be displayed on the Site and as Descriptions in Sozial Networks', 'cmc-opengraph-twittercrads' ); ?></p>
	<?php }

	function cmc_opengraph_twittercards_validate_seoDescription( $input ) {
		// make it safe
		$valid = substr( trim( strip_tags( $input ) ), 0, 250 );
		// Leave a message
		if ( $valid != get_option( 'cmc_opengraph_twittercards_seo_description' ) ) {
			add_settings_error(
				'cmc_opengraph_twittercards_seo_description', // setting title
				'cmc_opengraph_twittercards_seo_description',
				'SEO Description saved.', // error message
				'updated' // type of message
			);
		}

		// return it
		return $valid;
	}
	// END SEO Description Text-Field in Adminmenu -> Genaral

	// START SEO Title in Adminmenu -> Genaral
	function cmc_opengraph_twittercards_register_seoTitle() {

		register_setting(
			'general',
			'cmc_opengraph_twittercards_seo_title',
			array(
				$this,
				'cmc_opengraph_twittercards_validate_seoTitle',
			)
		);

		add_settings_field(
			'cmc_opengraph_twittercards_seo_title',
			__( 'SEO Title Extension', 'cmc-opengraph-twittercrads' ),
			array(
				$this,
				'cmc_opengraph_twittercards_formFields_seoTitle',
			),
			'general',
			'default'
		);

	}

	function cmc_opengraph_twittercards_formFields_seoTitle() {
		// echo the field ?>
		<input id="cmc_opengraph_twittercards_seo_title" name="cmc_opengraph_twittercards_seo_title" class="regular-text" type="text" value="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_seo_title' ) ); ?>">
		<p class="description"><?php _e( 'Some SEO Optimisation', 'cmc-opengraph-twittercrads' ); ?></p>
	<?php }

	function cmc_opengraph_twittercards_validate_seoTitle( $input ) {
		// make it safe
		$valid = trim( strip_tags( $input ) );
		// Leave a message
		if ( $valid != get_option( 'cmc_opengraph_twittercards_seo_title' ) ) {
			add_settings_error(
				'cmc_opengraph_twittercards_seo_title', // setting title
				'cmc_opengraph_twittercards_seo_title',
				'SEO Title saved.', // error message
				'updated' // type of message
			);
		}

		// return it
		return $valid;
	}
	// END SEO Title Text-Field in Adminmenu -> Genaral

	// START G+ ID for page-owner in Adminmenu -> Genaral
	function cmc_opengraph_twittercards_register_publisher_gplus_id() {
		register_setting(
			'general',
			'cmc_opengraph_twittercards_publisher_gplus_id',
			array(
				$this,
				'cmc_opengraph_twittercards_validate_publisher_gplus_id',
			)
		);

		add_settings_field(
			'cmc_opengraph_twittercards_publisher_gplus_id',
			__( 'Publisher G+ ID', 'cmc-opengraph-twittercrads' ),
			array(
				$this,
				'cmc_opengraph_twittercards_formFields_publisher_gplus_id',
			),
			'general',
			'default'
		);

	}

	function cmc_opengraph_twittercards_formFields_publisher_gplus_id() {
		// echo the field ?>
		<input id="cmc_opengraph_twittercards_publisher_gplus_id" name="cmc_opengraph_twittercards_publisher_gplus_id" class="regular-text" type="text" value="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ); ?>">
		<p class="description"><?php _e( 'Enter the G+ ID of the Publisher of this site. Just the long number', 'cmc-opengraph-twittercrads' ); ?></p>
	<?php }

	function cmc_opengraph_twittercards_validate_publisher_gplus_id( $input ) {
		// make it safe
		$valid = sanitize_text_field( $input );
		// Leave a message
		if ( $valid != get_option( 'cmc_opengraph_twittercards_publisher_gplus_id' ) ) {
			add_settings_error(
				'cmc_opengraph_twittercards_publisher_gplus_id', // setting title
				'cmc_opengraph_twittercards_publisher_gplus_id',
				'Publisher g+ ID saved.', // error message
				'updated' // type of message
			);
		}

		// return it
		return $valid;
	}
	// END G+ ID for page-owner in Adminmenu -> Genaral

	// START Twitter account for page-owner in Adminmenu -> Genaral
	function cmc_opengraph_twittercards_register_publisher_twitter_account() {
		register_setting(
			'general',
			'cmc_opengraph_twittercards_publisher_twitter_account',
			array(
				$this,
				'cmc_opengraph_twittercards_validate_publisher_twitter_account',
			)
		);

		add_settings_field(
			'cmc_opengraph_twittercards_publisher_twitter_account',
			__( 'Twitter-Account', 'cmc-opengraph-twittercrads' ),
			array(
				$this,
				'cmc_opengraph_twittercards_formFields_publisher_twitter_account',
			),
			'general',
			'default'
		);

	}

	function cmc_opengraph_twittercards_formFields_publisher_twitter_account() {
		// echo the field ?>
		<input id="cmc_opengraph_twittercards_publisher_twitter_account" name="cmc_opengraph_twittercards_publisher_twitter_account" class="regular-text" type="text" value="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ); ?>">
		<p class="description"><?php _e( 'Enter the Twitter-Account of the Publisher of this site. Without the @', 'cmc-opengraph-twittercrads' ); ?></p>
	<?php }

	function cmc_opengraph_twittercards_validate_publisher_twitter_account( $input ) {
		// make it safe
		$valid = sanitize_text_field( $input );
		// Leave a message
		if ( $valid != get_option( 'cmc_opengraph_twittercards_publisher_twitter_account' ) ) {
			add_settings_error(
				'cmc_opengraph_twittercards_publisher_twitter_account', // setting title
				'cmc_opengraph_twittercards_publisher_twitter_account',
				'Publisher Twitter-Account saved.', // error message
				'updated' // type of message
			);
		}

		// return it
		return $valid;
	}
	// END Twitter account for page-owner in Adminmenu -> Genaral

	// START Default meta-image in Adminmenu -> Genaral
	function cmc_opengraph_twittercards_register_metaimage() {
		register_setting(
			'general',
			'cmc_opengraph_twittercards_metaimage',
			array(
				$this,
				'cmc_opengraph_twittercards_validate_metaimage',
			)
		);

		add_settings_field(
			'cmc_opengraph_twittercards_metaimage',
			__( 'Default Meta-Image', 'cmc-opengraph-twittercrads' ),
			array(
				$this,
				'cmc_opengraph_twittercards_formFields_metaImage',
			),
			'general',
			'default'
		);

	}

	function cmc_opengraph_twittercards_formFields_metaImage() {
		// echo the field ?>
		<input id="cmc_opengraph_twittercards_metaimage" name="cmc_opengraph_twittercards_metaimage" class="regular-text" type="text" value="<?php echo esc_attr( get_option( 'cmc_opengraph_twittercards_metaimage' ) ); ?>">
		<p class="description"><?php _e( 'Enter URL for an default image for sozial-networks. 250x250 Px (incl. http://)', 'cmc-opengraph-twittercrads' ); ?></p>
	<?php }

	function cmc_opengraph_twittercards_validate_metaImage( $input ) {
		// make it safe
		$valid = esc_url( $input );
		// Leave a message
		if ( $valid != get_option( 'cmc_opengraph_twittercards_metaimage' ) ) {
			add_settings_error(
				'cmc_opengraph_twittercards_metaimage', // setting title
				'cmc_opengraph_twittercards_metaimage',
				'Default Meta-Image saved.', // error message
				'updated' // type of message
			);
		}

		// return it
		return $valid;
	}
	// END Default meta-image in Adminmenu -> Genaral

	// START Add short description to user profile
	function cmc_opengraph_twittercards_add_author_shortBio( $user ) { ?>
		<h3><?php _e( 'More Things about you', 'cmc-opengraph-twittercrads' ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="shortBio"><?php _e( 'Short Bio', 'cmc-opengraph-twittercrads' ); ?></label>
				</th>
				<td>
					<textarea rows="3" cols="20" name="shortBio" id="shortBio" maxlength="<?php echo apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ?>"><?php echo esc_html( get_the_author_meta( 'shortBio', $user->ID ) ); ?></textarea><br>
					<span class="description"><?php printf( __( '%s charactes about you.', 'cmc-opengraph-twittercrads' ), apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
	// END Add short description to user profile

	// START Saving short description for the user profile
	function cmc_opengraph_twittercards_save_author_shortBio( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) ) {
			$shortBioBuffer = trim( strip_tags( $_POST['shortBio'] ) );
			if ( $shortBioBuffer == '' ) {
				delete_user_meta( $user_id, 'shortBio' );
			} else {
				update_user_meta( $user_id, 'shortBio', $shortBioBuffer, get_the_author_meta( 'shortBio', $user_id ) );
			}
		}
	}
	// END Saving short description for the user profile

	// START Add Meta-Box to Post-Edit-Page
	function cmc_opengraph_twittercards_add_post_meta_box() {

		$screens = apply_filters( 'cmc_opengraph_twittercards_filter_post_type', array( 'post', 'page' ) );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'cmc_opengraph_twittercards_extrameta',
				__( 'Short Description', 'cmc-opengraph-twittercrads' ),
				array( $this, 'cmc_opengraph_twittercards_extrameta_box' ),
				$screen
			);

		}

	}
	// END Add Meta-Box to Post-Edit-Page

	// START content for Meta-Box at Post-Edit-Page
	function cmc_opengraph_twittercards_extrameta_box( $post ) {

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'cmc_opengraph_twittercards_noncename' );

		// Use get_post_meta to retrieve an existing value from the database and use the value for the form ?>
		<!--<label for="short_description">
			<?php _e( 'Short Description', 'cmc-opengraph-twittercrads' ); ?>
		</label>-->
		<textarea id="short_description" name="short_description" maxlength="<?php echo apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ); ?>" rows="3" cols="80"><?php echo esc_html( get_post_meta( $post->ID, 'short_description', true ) ); ?></textarea>
		<p><?php printf( __( 'Make a nice description with max. %s characters. Will be used for Search engines and Social Media. ', 'cmc-opengraph-twittercrads' ), apply_filters( 'cmc_opengraph_twittercards_seo_description_length', 155 ) ); ?></p>
	<?php }
	// END content for Meta-Box at Post-Edit-Page

	// START Save Meta-Box-Content at Post-Edit-Post
	function cmc_opengraph_twittercards_save_post_meta_box( $post_id ) {

		if ( ! isset( $_POST['post_type'] ) ) {
			return;
		}

		// First we need to check if the current user is authorised to do this action.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		// Secondly we need to check if the user intended to change this value.
		if ( ! isset( $_POST['cmc_opengraph_twittercards_noncename'] ) || ! wp_verify_nonce( $_POST['cmc_opengraph_twittercards_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// Thirdly we can save the value to the database

		//if saving in a custom table, get post_ID
		$post_ID = $_POST['post_ID'];

		//sanitize user input
		$cmc_opengraph_twittercards_short_description = sanitize_text_field( trim( str_replace( array(
			"\r\n",
			"\r",
		), " ", strip_tags( $_POST['short_description'] ) ) ) );
		$cmc_opengraph_twittercards_short_description = str_replace( '  ', ' ', $cmc_opengraph_twittercards_short_description );

		// Do something with $cmc_opengraph_twittercards_short_description
		if ( $cmc_opengraph_twittercards_short_description == '' ) {
			// remove if empty
			delete_post_meta( $post_id, 'short_description' );
		} else {
			//add or update
			add_post_meta( $post_ID, 'short_description', $cmc_opengraph_twittercards_short_description, true ) or
			update_post_meta( $post_ID, 'short_description', $cmc_opengraph_twittercards_short_description );
		}
	}
	// END Save Meta-Box-Content at Post-Edit-Post

	// START Modernize the User-Profile
	function cmc_opengraph_twittercards_modernize_userprofile( $user_contact ) {

		/* Add user contact methods */
		$user_contact['twitter']    = __( 'Twitter Username <span class="description">(without @)</span>', 'cmc-opengraph-twittercrads' );
		$user_contact['facebook']   = __( 'Facebook URL <span class="description">(incl. http://)</span>', 'cmc-opengraph-twittercrads' );
		$user_contact['googleplus'] = __( 'Google+ ID <span class="description">(just the long number)</span>', 'cmc-opengraph-twittercrads' );
		$user_contact['xing']       = __( 'Xing-Profil-Name <br><span class="description">(xing.com/profiles/<strong>Tom_Timsen</strong>)</span>', 'cmc-opengraph-twittercrads' );
		$user_contact['linkedIn']   = __( 'LinkedIn URL <span class="description">(incl. http://)</span>', 'cmc-opengraph-twittercrads' );

		/* Remove user contact methods */
		unset( $user_contact['aim'] );
		unset( $user_contact['yim'] );
		unset( $user_contact['jabber'] );

		return $user_contact;
	}
	// END Modernize the User-Profile

	// START make a better title
	function cmc_opengraph_twittercards_better_title( $title, $sep, $seplocation ) {

		if ( get_option( 'cmc_opengraph_twittercards_better_title' ) ) {

			// Prevent rewrite the title again if is_home is true
			if ( is_home() ) {
				if ( 'page' == get_option( 'show_on_front' ) ) {
					return $title;
				} else {
					if ( get_option( 'cmc_opengraph_twittercards_seo_title' ) ) {
						return $title . ' ' . $sep . ' ' . get_option( 'cmc_opengraph_twittercards_seo_title' );
					} else {
						return $title;
					}
				}
			} // END - if ( is_home() )

			if ( is_front_page() ) {

				// Do the title for front-page
				if ( get_option( 'cmc_opengraph_twittercards_seo_title' ) ) {
					return $title . ' ' . $sep . ' ' . get_bloginfo( 'description' ) . ' ' . $sep . ' ' . get_option( 'cmc_opengraph_twittercards_seo_title' );
				} else {
					return $title . ' ' . $sep . ' ' . get_bloginfo( 'description' );
				}

			} elseif ( class_exists( 'WooCommerce' ) ) {

				// WooCommerce is active
				if ( is_woocommerce() ) {
					$title = str_replace( get_bloginfo( 'blogname' ), '', $title );
					return $title . __( 'Shop', 'cmc-opengraph-twittercrads' ) . ' ' . $sep . ' ' . get_bloginfo( 'blogname' ) . ' ' . $sep . ' ' . get_bloginfo( 'description' );
				} else {
					return $title . ' ' . $sep . ' ' . get_bloginfo( 'description' );
				}

			} else {

				// For the rest
				return $title . ' ' . $sep . ' ' . get_bloginfo( 'description' );

			}

		} else {

			return $title;

		}

	}
	// END make a better title

} // end class

// Initial it
$crossMediaCloudOpenGraphTwitterCards = new CrossMediaCloudOpenGraphTwitterCards();
