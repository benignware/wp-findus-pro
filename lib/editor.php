<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
add_action( 'enqueue_block_editor_assets', function() {
  wp_enqueue_style( 'findus-editor-css', plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), false, '1.0', 'all' );
});
*/
add_filter( 'block_categories', function($categories, $post) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'map',
				'title' => __( 'Map', 'findus' ),
			),
		)
	);
}, 10, 2);

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */


// Hook: Block assets.
add_action( 'init', function() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'findus-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array( 'wp-editor' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'findus-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
    'findus-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'findus/map', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'findus-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'findus-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'findus-editor-css',
			'render_callback' => function($attributes, $content) {
				$attributes = wp_findus_snakeify_keys($attributes);
				$content = $attributes['content'] ?: $attributes['location'];
				$content = nl2br($content);

				$html = findus_shortcode($attributes, $content);

				return $html;
			}
		)
	);

	/*
	wp_localize_script( 'findus-js', 'FindusOptions',
    array(
			'data' => json_encode(array(
				'options' => array(
					// 'apiKey' => get_option('api_key')
				)
			))
    )
  );
	*/
});
