<?php
/**
 * Native block registry.
 *
 * One add() call per block. Each block has a PHP template under block-views/
 * and optional CSS under assets/css/. The editor script is assets/js/general/blocks.js.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

class BlockRegistry {

	/** @var array<string, array<string, mixed>> */
	private static $registered = array();

	/**
	 * Register a theme block.
	 *
	 * @param array<string, mixed> $config name, title, template, css, js.
	 */
	public static function add( array $config ): void {
		$name = sanitize_title( $config['name'] ?? '' );

		if ( '' === $name ) {
			return;
		}

		self::$registered[ $name ] = wp_parse_args(
			$config,
			array(
				'name'        => $name,
				'title'       => ucwords( str_replace( '-', ' ', $name ) ),
				'description' => '',
				'template'    => 'general/' . $name . '.php',
				'css'         => array(),
				'js'          => array(),
			)
		);
	}

	/**
	 * Hook registration and assets.
	 */
	public static function boot(): void {
		add_action( 'init', array( self::class, 'register_blocks' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( self::class, 'enqueue_editor_assets' ) );
	}

	/**
	 * Tell Gutenberg how to edit the PHP-rendered section blocks.
	 */
	public static function enqueue_editor_assets(): void {
		$path = get_template_directory() . '/assets/js/general/blocks.js';

		wp_enqueue_script(
			'psychwire-assessment-blocks',
			get_template_directory_uri() . '/assets/js/general/blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
			(string) filemtime( $path ),
			false
		);

		wp_localize_script(
			'psychwire-assessment-blocks',
			'PsychwireAssessment',
			array(
				'portrait' => psychwire_assessment_asset_uri( 'images/portrait.png' ),
				'copy'     => psychwire_assessment_editor_copy(),
			)
		);
	}

	/**
	 * Register each block with WP_Block_Type_Registry.
	 */
	public static function register_blocks(): void {
		foreach ( self::$registered as $name => $config ) {
			register_block_type(
				'psychwire-assessment/' . $name,
				array(
					'api_version'     => 3,
					'title'           => $config['title'],
					'description'     => $config['description'],
					'category'        => 'theme',
					'attributes'      => $config['attributes'] ?? array(),
					'render_callback' => static function ( $attributes ) use ( $name ) {
						return self::render( $name, is_array( $attributes ) ? $attributes : array() );
					},
					'supports'        => array(
						'html'     => false,
						'inserter' => ! empty( $config['inserter'] ),
					),
				)
			);
		}
	}

	/**
	 * Include the block template. Templates print HTML.
	 */
	public static function render( string $name, array $attributes = array() ): string {
		$config = self::$registered[ $name ] ?? null;

		if ( ! $config ) {
			return '';
		}

		$path = get_template_directory() . '/block-views/' . ltrim( $config['template'], '/' );

		if ( ! file_exists( $path ) ) {
			return '';
		}

		ob_start();
		include $path;

		return (string) ob_get_clean();
	}

	/**
	 * CSS files declared on the registered blocks, relative to assets/.
	 *
	 * The front end loads these inside one screen.css file.
	 *
	 * @return string[]
	 */
	public static function css_relative_paths(): array {
		$paths = array();

		foreach ( self::$registered as $config ) {
			foreach ( (array) $config['css'] as $file ) {
				$paths[] = 'css/' . ltrim( (string) $file, '/' );
			}
		}

		return $paths;
	}

	/**
	 * Enqueue JS declared on each block.
	 *
	 * Block CSS is bundled with the theme stylesheet. A separate link tag
	 * for each file would block rendering on HTTP/1.1.
	 */
	public static function enqueue_assets(): void {
		$bundled = function_exists( 'psychwire_assessment_screen_stylesheet' ) && '' !== psychwire_assessment_screen_stylesheet();

		foreach ( self::$registered as $name => $config ) {
			if ( ! $bundled ) {
				foreach ( (array) $config['css'] as $file ) {
					self::enqueue_style( $name . '-' . sanitize_title( $file ), 'css/' . ltrim( (string) $file, '/' ) );
				}
			}

			foreach ( (array) $config['js'] as $file ) {
				$relative = is_array( $file ) ? $file['file'] : $file;
				self::enqueue_script( $name . '-' . sanitize_title( $relative ), 'js/' . ltrim( $relative, '/' ) );
			}
		}
	}

	/**
	 * Enqueue one theme asset stylesheet.
	 */
	private static function enqueue_style( string $handle, string $relative ): void {
		$path = get_template_directory() . '/assets/' . $relative;

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_style(
			'psychwire-assessment-' . $handle,
			get_template_directory_uri() . '/assets/' . $relative,
			array( 'psychwire-assessment-root' ),
			(string) filemtime( $path )
		);
	}

	/**
	 * Enqueue one theme asset script in the footer.
	 */
	private static function enqueue_script( string $handle, string $relative ): void {
		$path = get_template_directory() . '/assets/' . $relative;

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_script(
			'psychwire-assessment-' . $handle,
			get_template_directory_uri() . '/assets/' . $relative,
			array(),
			(string) filemtime( $path ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
