<?php

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * A class for simple settings page generation, using the Settings API.
 *
 * Create a new settings object with a unique ID and a heading:
 * - $example = new Lucid_Slider_Settings( 'lucid_settings', __( 'Heading' ) );
 *
 * Then add each part with:
 * - $example->submenu( [...] );
 * - $example->section( [...] );
 * - $example->field( [...] );
 *
 * Finally initialize the page:
 * - $example->init();
 *
 * To use a tabbed settings page, simply add a 'tabs' array to submenu(), see
 * function description.
 * 
 * @package Lucid_Slider
 */
class Lucid_Slider_Settings {

	/**
	 * The unique settings ID.
	 *
	 * This is the key used for the settings in the database and what is used
	 * with get_option().
	 * 
	 * NOTE: Only relevant when tabs are not used. In the case of tabs, every
	 * tab ID is a separate settings collection.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The heading for the settings page. Doesn't show when using tabs.
	 *
	 * @var string
	 */
	public $page_heading = '';

	/**
	 * The screen ID of the settings page.
	 *
	 * @var string
	 */
	protected $screen_id;

	/**
	 * Capability required to edit the settings.
	 *
	 * @var string
	 * @link http://codex.wordpress.org/Roles_and_Capabilities
	 */
	public $capability = 'manage_options';

	/**
	 * The submenu item for the settings page.
	 *
	 * @var array
	 * @see submenu()
	 */
	protected $submenu = array();

	/**
	 * Settings page tabs. Stored as 'unique_id' => 'Tab label'.
	 *
	 * @var array
	 */
	protected $tabs = array();

	/**
	 * Which sections belong to which tabs.
	 *
	 * @var array
	 */
	protected $tab_sections = array();

	/**
	 * Settings sections.
	 *
	 * @var array
	 * @see section()
	 */
	protected $sections = array();

	/**
	 * Settings sections default values.
	 *
	 * @var array
	 * @see section()
	 */
	protected $section_defaults = array();

	/**
	 * Settings fields.
	 *
	 * @var array
	 * @see field()
	 */
	protected $fields = array();

	/**
	 * Settings fields default values.
	 *
	 * @var array
	 * @see field()
	 */
	protected $field_defaults = array();

	/**
	 * All checkboxes.
	 * 
	 * Used to check if unchecking is required, since unchecked checkboxes don't
	 * get POSTed.
	 *
	 * @var array
	 */
	protected $checkboxes = array();

	/**
	 * Added checklists.
	 *
	 * A checklist need every option as a separate entry in $fields, since they
	 * are separate options. This means looping $fields in _add_fields() will
	 * result in multiple stops on the same collection of checkboxes, which in
	 * turn will cause duplicate output. The 'main' ID of the checklist (first
	 * param to field()) is thus added here and chacked in in _add_fields().
	 *
	 * @var array
	 */
	protected $checklists = array();

	/**
	 * Constructor, set ID and heading.
	 */
	public function __construct( $id, $page_heading ) {
		$this->id = (string) $id;
		$this->page_heading = (string) $page_heading;
	}

	/**
	 * Add a submenu to an admin menu.
	 *
	 * NOTE: If tabs are used, each tab ID will be used as the key for the
	 * settings on that tab page, not the ID set with the constructor.
	 *
	 * Additional arguments through the $args array:
	 * - 'add_to' (string) Slug for the parent menu, or the file name of a
	 *      standard WordPress admin page (wp-admin/<file_name>). Includes .php
	 *      extension.
	 * - 'title' (string) HTML <title> text.
	 * - 'tabs' (array) Tabs to add, format 'unique_id' => 'Tab label'.
	 * - 'capability' (string) Capability needed to edit the settings. If not
	 *      set, the common $capability property is used.
	 *
	 * @param string $menu_label Text for the link in the menu.
	 * @param array $args Additional arguments.
	 * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
	 * @link http://codex.wordpress.org/Roles_and_Capabilities
	 */
	public function submenu( $menu_label, array $args = array() ) {
		$defaults = array(
			'add_to' => 'options-general.php',
			'title' => $menu_label,
			'tabs' => array(),
			'capability' => $this->capability
		);
		$args = array_merge( $defaults, $args );

		$this->added_to = $args['add_to'];

		if ( ! empty( $args['tabs'] ) )
			$this->tabs = (array) $args['tabs'];

		$this->submenu = array_merge( array(
			'menu_label' => $menu_label
		), $args );
	}

	/**
	 * Add a settings section.
	 *
	 * Additional arguments through the $args array:
	 * - 'heading' (string) HTML <title> text.
	 * - 'tab' (string) Tab to add section to. Tabs defined with submenu().
	 * - 'output' (string) HTML to display at the top of the section.
	 *
	 * @param array $submenu An array with arrays of submenu.
	 * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
	 * @link http://codex.wordpress.org/Roles_and_Capabilities
	 */
	public function section( $id, array $args = array() ) {

		// Set the first tab as default if none is set.
		reset( $this->tabs );
		$default_tab = ( ! empty( $this->tabs ) ) ? key( $this->tabs ) : '';

		$this->section_defaults = array(
			'heading' => '',
			'tab' => $default_tab,
			'output' => ''
		);
		$args = array_merge( $this->section_defaults, $args );

		// Create an array of which sections belong to which tabs
		if ( array_key_exists( $args['tab'], $this->tabs ) ) :
			$this->tab_sections[$args['tab']][] = $id;
		endif;

		$this->sections[$id] = $args;
	}

	/**
	 * Add a settings field.
	 *
	 * Additional arguments through the $args array:
	 * - 'type' (string) Type of field.
	 *      Supported types:
	 *      - 'text'
	 *      - 'text_monospace'
	 *      - 'textarea'
	 *      - 'textarea_large'
	 *      - 'textarea_monospace'
	 *      - 'textarea_large_monospace'
	 *      - 'checkbox'
	 *      - 'checklist'
	 *      - 'radios'
	 *      - 'select
	 *      Unsupported types will fall back to 'text'.
	 * - 'section' (string) Type of field.
	 * - 'default' (mixed) Default field value. Is only set if options don't
	 *      exist, so will probably only run on theme/plugin activation.
	 * - 'description' (string) A help text to show under the field.
	 * - 'inline_label' (string) Field label for checkbox and radio button.
	 * - 'options' (array) Options for types 'select', 'radios', and 'checklist',
	 *      format: value => text.
	 * - 'validate' (string) Validate value against predefined functions, see
	 *      function validate().
	 * - 'must_match' (regex string) A regular expression that is matched
	 *      against the value, i.e. '/^\d{3}$/' to require exactly three digits.
	 * - 'must_not_match' (regex string) A regular expression that is matched
	 *      against the value, where the result is reversed. So something like
	 *      '/[#=]/' would mean the value can not contain a hash or an equal
	 *      sign.
	 * - 'error_message' (string) Message for when validation fails.
	 * - 'sanitize' (string) Sanitize value against predefined functions, see
	 *      function sanitize().
	 *
	 * @param string $id A unique ID for the field.
	 * @param string $label The field label.
	 * @param string $section Section to add the field to, defined with
	 *   section().
	 * @param array $args Array of additional arguments.
	 */
	public function field( $id, $label, array $args = array() ) {
		$this->field_defaults = array(
			'type' => 'text',
			'section' => '',
			'default' => '',
			'description' => '',
			'inline_label' => '',
			'options' => array(),
			'validate' => '',
			'must_match' => '',
			'must_not_match' => '',
			'error_message' => '',
			'sanitize' => ''
		);
		$args = array_merge( $this->field_defaults, $args );

		// Since a function gets called based on the value of 'type', unsupported
		// types are converted to text.
		$supported_types = array(
			'text',
			'text_monospace',
			'textarea',
			'textarea_large',
			'textarea_monospace',
			'textarea_large_monospace',
			'checkbox',
			'checklist',
			'radios',
			'select'
		);
		if ( ! in_array( $args['type'], $supported_types ) )
			$args['type'] = 'text';

		// If a section is not defined, set it to the first one.
		if ( empty( $args['section'] ) ) :
			reset( $this->sections );
			$args['section'] = key( $this->sections );
		endif;

		// Keep track of checkboxes grouped by section.
		if ( 'checkbox' == $args['type'] ) :
			$this->checkboxes[$args['section']][] = $id;

		// Checklists need special handling.
		elseif ( 'checklist' == $args['type'] ) :
			foreach ( $args['options'] as $field_id => $field_label ) :

				// Same as above.
				$this->checkboxes[$args['section']][] = $field_id;

				// Add each checkbox as a separate entry in $fields.
				$this->fields[$field_id] = array_merge( array(
					'label' => $label,
					'checklist' => $id
				), $args );
			endforeach;

			// Drop out so they don't get overwritten.
			return;
		endif;

		$this->fields[$id] = array_merge( array(
			'label' => $label
		), $args );
	}

	/**
	 * Run the settings registration on apropriate hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, '_load_settings' ) );
		add_action( 'admin_init', array( $this, '_register_setting' ) );
	}

	/**
	 * Load and register settings.
	 * 
	 * Add a menu entry to the defined menu and set up loading of the content
	 * for the settings page.
	 */
	public function _load_settings() {
		if ( empty( $this->submenu ) ) return;

		$this->screen_id = add_submenu_page(
			$this->submenu['add_to'],
			$this->submenu['title'],
			$this->submenu['menu_label'],
			$this->submenu['capability'],
			$this->id,
			array( $this, '_display_page' )
		);
		
		// Only load the settings content when on the added submenu page
		if ( ! empty( $this->screen_id ) ) :
			add_action( 'load-' . $this->screen_id, array( $this, '_add_sections' ) );
			add_action( 'load-' . $this->screen_id, array( $this, '_add_fields' ) );
		endif;
	}

	/**
	 * Add all sections from $this->sections.
	 */
	public function _add_sections() {
		$this->sections = apply_filters( 'lsjl_settings_sections', $this->sections );

		foreach ( $this->sections as $section => $args ) :

			// Another merge to prevent notices from externally added sections
			$args = array_merge( $this->section_defaults, $args );

			// If using tabs, the page ID the section should be added to is the
			// tab it's set to show on. Otheriwse it's just the initially set
			// setting ID.
			$page = ( ! empty( $this->tabs ) && ! empty( $this->sections[$section]['tab'] ) )
				? $this->sections[$section]['tab']
				: $this->id;

			add_settings_section(
				$section,
				$args['heading'],
				array( $this, '_display_section' ),
				$page
			);
		endforeach;
	}

	/**
	 * Add all fields from $this->fields.
	 */
	public function _add_fields() {
		$this->fields = apply_filters( 'lsjl_settings_fields', $this->fields );

		foreach ( $this->fields as $field_id => $args ) :

			// Another merge to prevent notices from externally added fields
			$args = array_merge( $this->field_defaults, $args );

			// If using tabs, the page ID the field should be added to is the tab
			// of the section it belongs in. Otheriwse it's just the initially set
			// setting ID.
			$page = ( ! empty( $this->tabs ) && ! empty( $this->sections[$args['section']]['tab'] ) )
				? $this->sections[$args['section']]['tab']
				: $this->id;

			// Don't add 'for' to the left column label for checkboxes and radio
			// buttons, since they will have adjacent labels.
			$label_for = ( $args['type'] == 'checkbox'
				|| $args['type'] == 'checklist'
				|| $args['type'] == 'radios'
				|| $args['type'] == 'radio' )
				? ''
				: $field_id;

			// Get option value here instead of in every function. If using tabs,
			// every tab ID is used as a separate options entry.
			if ( ! empty( $this->tabs ) ) :
				// Get first tab if none is set
				reset( $this->tabs );
				$settings_id = isset( $_GET['tab'] ) ? $_GET['tab'] : key( $this->tabs );
			else :
				$settings_id = $this->id;
			endif;

			$options = (array) get_option( $settings_id );

			// Value
			$value = '';
			if ( isset( $options[$field_id] ) )
				$value = trim( $options[$field_id] );

			// Method
			$method = '_add_' . $args['type'];

			// Checklist handling. Check for current page.
			if ( $settings_id == $page
			  && 'checklist' == $args['type'] ) :

				// Checklist options are stored by each option value, since the
				// checkboxes are not related to each other other than visually.
				// Thus the key for $field_ids can not be used.
				$value = array();
				foreach ( $args['options'] as $id => $label ) :
					$value[$id] = $options[$id];
				endforeach;

				// Only add the checklist fields once. See $this->checklists.
				if ( in_array( $args['checklist'], $this->checklists ) ) continue;
				$this->checklists[] = $args['checklist'];
			endif;

			// Add the field
			add_settings_field(
				$field_id,
				$args['label'],
				array( $this, $method ),
				$page,
				$args['section'],

				// Pass arguments to the _add_<type> functions
				array(
					'label_for' => $label_for,
					'prefix' => $page,
					'id' => $field_id,
					'value' => $value,
					'label' => $args['inline_label'],
					'description' => $args['description'],
					'options' => $args['options']
				)
			);
		endforeach;

		// Add highlighting for fields with errors.
		add_action( 'admin_footer', array( $this, '_error_highlighting' ) );
	}

	/**
	 * Display the settings page.
	 */
	public function _display_page() {
		if ( ! current_user_can( $this->capability ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'lucid-slider' ) );

		ob_start(); ?>

		<div class="wrap">

			<?php // If using tabs, the ID of the current settings section is the
			// same as the current tab. Otheriwse it's just the initially set ID.
			if ( ! empty( $this->tabs ) ) :
				// Get first tab if none is set
				reset( $this->tabs );
				$settings = isset( $_GET['tab'] ) ? $_GET['tab'] : key( $this->tabs );
				$this->settings_tabs();
			else :
				$settings = $this->id;
				screen_icon();
				echo "<h2>{$this->page_heading}</h2>";
			endif;

			settings_errors(); ?>

			<form method="post" action="options.php">
				<?php // Renders settings fields lined up in tables and also
				// handles security with referer and nonce checks.
				settings_fields( $settings );
				do_settings_sections( $settings );
				submit_button(); ?>
			</form>

		</div>
	<?php echo ob_get_clean();
	}

	/**
	 * Display section output if set.
	 *
	 * @param array $section Data for the section being processed.
	 */
	public function _display_section( $section ) {
		if ( ! empty( $this->sections[$section['id']]['output'] ) ) :
			echo wp_kses_post( $this->sections[$section['id']]['output'] );
		endif;
	}

	/**
	 * Render tabs.
	 */
	public function settings_tabs() {
		if ( empty( $this->tabs ) ) return;

		// Get first tab if none is set
		reset( $this->tabs );
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : key( $this->tabs );

		screen_icon(); ?>

		<h2 class="nav-tab-wrapper">
		<?php // echo $this->page_heading;
		foreach ( $this->tabs as $tab => $label ) :
			$active = ( $current_tab == $tab ) ? ' nav-tab-active' : ''; ?>
			<a class="nav-tab<?php echo $active; ?>" href="<?php echo "?page={$this->id}&tab={$tab}"; ?>"><?php echo $label; ?></a>
		<?php endforeach; ?>
		</h2>
	<?php }

	/**
	 * Register settings page and fields.
	 */
	public function _register_setting() {

		// With tabs, every tab is a separate settings collection
		if ( ! empty( $this->tabs ) ) :
			$this->tabs = apply_filters( 'lsjl_settings_tabs', $this->tabs );

			foreach ( $this->tabs as $tab => $label ) :
				$this->_add_defaults( $tab );

				register_setting(
					$tab,
					$tab,
					array( $this, 'sanitize_options' )
				);
			endforeach;

		// Without tabs the setting page ID is used
		else :
			$this->_add_defaults( $this->id );

			register_setting(
				$this->id,
				$this->id,
				array( $this, 'sanitize_options' )
			);
		endif;
	}

	/**
	 * Add default options.
	 *
	 * Should only run if there are no options set, which should only happen if
	 * the settings have never been saved.
	 *
	 * @param string $id Setting ID, tab or global.
	 */
	protected function _add_defaults( $id ) {

		// Don't to anything if the options exist.
		$current_option = get_option( $id );
		if ( ! empty( $current_option ) ) return;

		add_option( $id );
		$added_checklists = array();
		$defaults = array();

		foreach ( $this->fields as $field_id => $args ) :
			
			// Get out if the field is not on the tab being processed.
			if ( ! empty( $this->tabs )
			  && $this->sections[$args['section']]['tab'] != $id ) continue;

			// Checkboxes use a zero for empty defaults
			$default = ( 'checkbox' == $args['type'] || 'checklist' == $args['type'] ) ? 0 : '';

			if ( ! empty( $args['default'] ) )
				$default = $args['default'];

			// Checklists have every option as a separate setting.
			if ( 'checklist' == $args['type'] ) :

				// Skip multiple iterations for the same checklist.
				if ( in_array( $field_id, $added_checklists ) ) continue;
				
				foreach ( $args['options'] as $option => $label ) :
					$defaults[$option] = $default;
				endforeach;
				
				$added_checklists[] = $field_id;

			// Regular fields have their ID.
			else :
				$defaults[$field_id] = $default;
			endif;
		endforeach;

		update_option( $id, $defaults );
	}





	/*========================================================================*\
	      =Fields
	\*========================================================================*/

	/**
	 * Display a text field.
	 */
	public function _add_text( $args, $class = '' ) {

		$class = ( ! empty( $class ) )
			? 'regular-text ' . $class
			: 'regular-text'; ?>
		
		<input type="text" class="<?php echo $class; ?>" id="<?php echo $args['id']; ?>" name="<?php echo "{$args['prefix']}[{$args['id']}]"; ?>" value="<?php echo esc_attr( $args['value'] ); ?>">

		<?php $this->add_description( $args['description'] );
	}

	/**
	 * Display a text field with a monospaced font.
	 */
	public function _add_text_monospace( $args ) {
		$this->_add_text( $args, 'code' );
	}

	/**
	 * Display a textarea.
	 */
	public function _add_textarea( $args, $class = '' ) { ?>
		<textarea id="<?php echo $args['id']; ?>" name="<?php echo "{$args['prefix']}[{$args['id']}]"; ?>" rows="8" cols="80"<?php if ( ! empty( $class ) ) echo " class=\"{$class}\""; ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>

		<?php $this->add_description( $args['description'] );
	}

	/**
	 * Display a textarea that spans the entire page.
	 */
	public function _add_textarea_large( $args ) {
		$this->_add_textarea( $args, 'large-text' );
	}

	/**
	 * Display a textarea with a monospaced font.
	 */
	public function _add_textarea_monospace( $args ) {
		$this->_add_textarea( $args, 'code' );
	}

	/**
	 * Display a textarea with a monospaced font that spans the entire page.
	 */
	public function _add_textarea_large_monospace( $args ) {
		$this->_add_textarea( $args, 'large-text code' );
	}

	/**
	 * Display a checkbox.
	 */
	public function _add_checkbox( $args ) { ?>
		<input type="checkbox" id="<?php echo $args['id']; ?>" name="<?php echo "{$args['prefix']}[{$args['id']}]"; ?>" value="1" <?php checked( $args['value'], 1 ); ?>>

		<?php if ( ! empty( $args['label'] ) ) : ?>
			<label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
		<?php endif;

		$this->add_description( $args['description'] );
	}

	/**
	 * Display a list of checkboxes.
	 */
	public function _add_checklist( $args ) {
		$count = 0;

		foreach ( $args['options'] as $id => $label ) :
			if ( $count > 0 ) echo '<br>';
			$count++; ?>
			<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo "{$args['prefix']}[{$id}]"; ?>" value="1" <?php checked( $args['value'][$id], 1 ); ?>>

			<label for="<?php echo $id; ?>"><?php echo $label; ?></label>
		<?php endforeach;

		$this->add_description( $args['description'] );
	}

	/**
	 * Display a select list.
	 */
	public function _add_select( $args ) { ?>
		<select id="<?php echo $args['id']; ?>" name="<?php echo "{$args['prefix']}[{$args['id']}]"; ?>">
			<?php foreach ( $args['options'] as $val => $text ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $args['value'], $val ); ?>><?php echo $text; ?></option>
			<?php endforeach; ?>
		</select>

		<?php $this->add_description( $args['description'] );
	}

	/**
	 * Display radio buttons.
	 */
	public function _add_radios( $args ) {
		$count = 0;

		foreach ( $args['options'] as $val => $label ) :
			if ( $count > 0 ) echo '<br>';
			$count++; ?>
			<input type="radio" id="<?php echo $args['id'] . '_' . $count; ?>" name="<?php echo "{$args['prefix']}[{$args['id']}]"; ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( $args['value'], $val ); ?>>

			<label for="<?php echo $args['id'] . '_' . $count; ?>"><?php echo $label; ?></label>
		<?php endforeach;

		$this->add_description( $args['description'] );
	}

	/**
	 * Display a field description.
	 */
	protected function add_description( $text ) {
		if ( ! empty( $text ) )
			echo '<br><span class="description">' . $text . '</span>';
	}





	/*========================================================================*\
	      =Sanitation and validation
	\*========================================================================*/

	/**
	 * Sanitize the input from the settings.
	 *
	 * Checks for an explicitly defined sanitation 'none' to save unfiltered
	 * values. Runs through sanitize() if none is set, where it defaults to
	 * stripping illegal tags with wp_kses_post().
	 *
	 * @see validate()
	 * @see validate_custom()
	 * @see sanitize()
	 */
	public function sanitize_options( $input ) {

		// Since the tabs are registered as individual settings, they are
		// considered separate pages and are available in $_POST
		if ( ! empty( $this->tabs ) ) :
			$settings_id = preg_replace( '/[^A-Za-z0-9\-_]/', '', $_POST['option_page'] );
		else :
			$settings_id = $this->id;
		endif;

		$output = (array) get_option( $settings_id );

		foreach ( $input as $name => $val ) :
			if ( isset( $input[$name] ) ) :

				// Anti-notice annoyances
				$f = $this->fields[$name];
				$validate = ( ! empty( $f['validate'] ) ) ? $f['validate'] : false;
				$sanitize = ( ! empty( $f['sanitize'] ) ) ? $f['sanitize'] : '';
				$must_match = ( ! empty( $f['must_match'] ) ) ? $f['must_match'] : false;
				$must_not_match = ( ! empty( $f['must_not_match'] ) ) ? $f['must_not_match'] : false;
				$error = ( ! empty( $f['error_message'] ) ) ? $f['error_message'] : '';

				// Validation, sets error if there is a problem
				if ( $validate || $must_match || $must_not_match ) :
					$result = trim( $input[$name] );

					// Do appropriate validation depending on type
					if ( $validate ) :
						$result = $this->validate( $validate, $input[$name], $error );
					elseif ( $must_match ) :
						$result = $this->validate_custom( $must_match, $input[$name], $error );
					else :
						$result = $this->validate_custom( $must_not_match, $input[$name], $error, true );
					endif;

					// Validation functions return an array in case of error. Kind
					// of ugly but works...
					if ( is_array( $result )
					  && ! empty( $result['error'] ) ) :
						add_settings_error( $name, $name, $result['error'] );
					else :
						$output[$name] = $result;
					endif;

				// No checking, value saved as is
				elseif ( 'none' == $sanitize ) :
					$output[$name] = $input[$name];

				// Sanitation, converts the input to a fitting format
				else :
					$output[$name] = $this->sanitize( $sanitize, trim( $input[$name] ) );
				endif;

			endif;
		endforeach;

		// Special checkbox handling: unchecked boxes don't get POSTed, so isset.
		foreach ( $this->checkboxes as $section => $boxes ) :
			foreach ( $boxes as $key => $checkbox_id ) :

				// If using tabs, only look for checkboxes on the tab being
				// processed.
				if ( ! empty( $this->tabs )
				  && $this->sections[$section]['tab'] != $settings_id ) continue;

				if ( ! isset( $input[$checkbox_id] ) )
					$output[$checkbox_id] = 0;

			endforeach;
		endforeach;

		return $output;
	}

	/**
	 * Run predefined validation on a value.
	 *
	 * Contains predefined validation checks for 'email', 'url' and 'hex_color'.
	 *
	 * The URL check contains a simple second part in addition to the complex
	 * regex, where if set matches <2 characters>.<2-15 characters> (with an
	 * optional slash). This is so a URL like 'google.com' counts as valid. Can
	 * br filtered with 'lucid_validate_simple_url', simply return false.
	 *
	 * FILTER_VALIDATE_URL is not used due to a bug in some versions of PHP
	 * where dashes are invalid. It also doesn't handle non-ASCII characters.
	 *
	 * @param string $type Type of validation, predfined or custom.
	 * @param string $value Value to validate.
	 * @param string $error Error message to display in case of invalid value.
	 * @link http://daringfireball.net/2010/07/improved_regex_for_matching_urls
	 * @return string|array The value if it's valid, array in case of error.
	 */
	protected function validate( $type, $value, $error = '' ) {
		$valid = true;

		// Email
		if ( 'email' == $type ) :
			if ( empty( $error ) ) $error = __( 'The email address seems to be invalid.', 'lucid-slider' );

			$valid = (bool) filter_var( $value, FILTER_VALIDATE_EMAIL );

		// URL
		elseif ( 'url' == $type ) :
			if ( empty( $error ) ) $error = __( 'The URL seems to be invalid.', 'lucid-slider' );

			// Is simple is tested, a URL like 'google.com' is valid
			$include_simple = true;
			$include_simple = apply_filters( 'lucid_validate_simple_url', $include_simple );
			$simple_url = ( $include_simple )
				? ( preg_match( '/^\w{2,}\.\w{2,15}\/?$/', $value ) )
				: false;

			// http://daringfireball.net/2010/07/improved_regex_for_matching_urls
			//
			// Modifications: Allow up to a 15 character TLD (since new ones can
			// be bought). Make second capture group optional, so only a single
			// character after a slash is needed. Match beginning and end of
			// string.
			$valid = (bool) (
				( preg_match( '/^((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,15}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))*(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))$/', $value ) )
				|| $simple_url
			);

		// Hexadecimal color (101 or bada55, hash is removed)
		elseif ( 'hex_color' == $type ) :
			if ( empty( $error ) ) $error = __( 'Please format the hex color correctly.', 'lucid-slider' );
			$value = str_replace( '#', '', $value );

			$valid = (bool) preg_match( '/^([a-f0-9]{6}|[a-f0-9]{3})$/', strtolower( $value ) );
			
		endif;

		// If there is an error, send it back as an array. Kind of ugly way to
		// determine if it's an error or not (is_array).
		if ( ! $valid )
			$value = array( 'error' => $error );

		return $value;
	}

	/**
	 * Run custom validation on a value.
	 *
	 * Runs a custom regex validation. If reverse is set, the preg_match result
	 * is reversed.
	 *
	 * @param string $regex The regex to run in preg_match.
	 * @param string $value Value to validate.
	 * @param string $error Error message to display in case of invalid value.
	 * @param bool $reverse Reverse preg_match result?
	 * @return string|array The value if it's valid, array in case of error.
	 */
	protected function validate_custom( $regex, $value, $error = '', $reverse = false ) {
		$valid = true;

		if ( empty( $error ) )
			$error = __( 'There were settings with invalid values.', 'lucid-slider' );

		if ( $reverse ) :
			// must_not_match
			$valid = (bool) ! preg_match( $regex, $value );
		else :
			// must_match
			$valid = (bool) preg_match( $regex, $value );
		endif;

		// If there is an error, send it back as an array. Kind of ugly way to
		// determine if it's an error or not (is_array).
		if ( ! $valid )
			$value = array( 'error' => $error );

		return $value;
	}

	/**
	 * Sanitize a value.
	 *
	 * Options:
	 * - 'checkbox'     Always 1 or 0.
	 * - 'int'          Integer, positive or negative.
	 * - 'float'        Floating point number.
	 * - 'alphanumeric' Letters, numbers, underscore and dash.
	 * - 'no_html'      Strips HTML with strip_tags.
	 * - 'shortcode'    Removes greater/less than and forces enclosing square
	 *      brackets.
	 * 
	 * Falls back to stripping illegal HTML tags with wp_kses_post.
	 *
	 * @param string $type What kind of sanitation to run.
	 * @param string $value Value to sanitize
	 * @return mixed The sanitized value.
	 */
	protected function sanitize( $type, $value ) {
		switch ( $type ) :

			case 'checkbox' :
				if ( ! empty( $value ) ) $value = 1;
				break;

			case 'int' :
				// Hyphens inside the string are stripped, so bring it back
				// afterwards if it's in the first position.
				$is_negative = ( '-' == $value[0] ) ? true : false;
				$value = preg_replace( '/[^0-9]/', '', $value );
				if ( $is_negative ) $value = '-' . $value;
				$value = intval( $value );
				break;

			case 'float' :
				$value = floatval( $value );
				break;

			case 'alphanumeric' :
				$value = preg_replace( '/[^a-zA-Z0-9_-]/', '', $value );
				break;

			case 'no_html' :
				$value = strip_tags( $value );
				break;

			case 'url' :
				$value = esc_url_raw( $value );

			case 'shortcode' :
				$tmp = str_replace( array( '[', ']', '<', '>' ), '', $value );
				$value = '[' . trim( $tmp ) . ']';
				break;

			default :
				$value = wp_kses_post( $value );
				break;

		endswitch;

		return $value;
	}

	/**
	 * Hightlight fields with errors.
	 */
	public function _error_highlighting() { ?>
		<script>
		;(function( $ ) {
			var $errors = $( '.settings-error' );

			if ( 0 !== $errors.length ) {
				$errors.each( function() {
					var id = $(this).attr('id').replace( /setting-error-/, '' );
					$( '#' + id ).css({ 'border-color': '#cc0000' });
				});
			}
		})( jQuery );
		</script>
	<?php }
}