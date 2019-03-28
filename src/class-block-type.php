<?php
/**
 * Main class
 *
 * @package wp-bootstrap-blocks
 */

namespace WP_Bootstrap_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Block_Type
 */
class Block_Type {
	/**
	 * Name of block type including namespace.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Block attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Default values of block attributes.
	 *
	 * @var array
	 */
	protected $default_attributes = array();

	/**
	 * Block_Type constructor.
	 *
	 * @param string $name Block type name including namespace.
	 * @param array  $attributes Block attributes.
	 * @param array  $default_attributes Default values of block attributes.
	 */
	public function __construct( $name, $attributes, $default_attributes ) {
		$this->name = $name;
		$this->attributes = $attributes;
		$this->default_attributes = $default_attributes;

		add_action( 'init', array( $this, 'register_block_type' ) );
	}

	/**
	 * Registers block type
	 */
	public function register_block_type() {
		/**
		 * Possibility to hook into default attribute values.
		 */
		$this->default_attributes = apply_filters( "{$this->get_filter_prefix()}_default_attributes", $this->default_attributes );

		foreach ( $this->default_attributes as $attribute_name => $default_value ) {
			if ( array_key_exists( $attribute_name, $this->attributes ) ) {
				$this->attributes[ $attribute_name ]['default'] = $default_value;
			}
		}

		register_block_type(
			$this->name,
			array(
				'render_callback' => array( $this, 'render_callback' ),
				'attributes' => $this->attributes,
			)
		);
	}

	/**
	 * Render callback for wp-bootstrap-blocks/row block.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content HTML content of block.
	 * @return string Rendered block.
	 */
	public function render_callback( $attributes, $content ) {
		return wp_bootstrap_blocks_get_template( $this->get_template_name(), $attributes, $content );
	}

	/**
	 * Get filter prefix.
	 *
	 * @return string
	 */
	protected function get_filter_prefix() {
		// Replace all special characters in block type name with underscore.
		// Eg. wp-bootstrap-blocks/myblock => wp_bootstrap_blocks_myblock
		return preg_replace( '/[-\/]/', '_', $this->name );
	}

	/**
	 * Get name of block template.
	 *
	 * @return string
	 */
	protected function get_template_name() {
		// Remove namespace from block name.
		$namespace_separator_position = strrpos( $this->name, '/' );
		return false === $namespace_separator_position ? $this->name : substr( $this->name, $namespace_separator_position + 1 );
	}
}
