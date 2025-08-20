<?php
/**
 * Plugin Name: The Child Theme Generator
 * Plugin URI: https://memdigital.co.uk
 * Description: Tired of overly complicated child theme generators? Our super simple and lightweight child theme generator makes the job easy - no coding required.
 * Version: 1.1.0
 * Author: MEM Digital
 * Author URI: https://memdigital.co.uk
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: the-child-theme-generator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TCTG_VERSION', '1.1.0');
define('TCTG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TCTG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TCTG_TEXT_DOMAIN', 'the-child-theme-generator');

/**
 * Main plugin class
 */
class TheChildThemeGenerator {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain(TCTG_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_create_child_theme_action', array($this, 'handle_form_submission'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX hooks for advanced features
        add_action('wp_ajax_get_parent_files', array($this, 'ajax_get_parent_files'));
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __('The Child Theme Generator', TCTG_TEXT_DOMAIN),
            __('TCT Generator', TCTG_TEXT_DOMAIN),
            'switch_themes',
            'child-theme-generator',
            array($this, 'admin_page_content'),
            'dashicons-admin-appearance',
            30
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_child-theme-generator') {
            return;
        }
        
        wp_enqueue_style(
            'tctg-admin-style',
            TCTG_PLUGIN_URL . 'css/admin-style.css',
            array(),
            TCTG_VERSION
        );
        
        wp_enqueue_script(
            'tctg-admin-script',
            TCTG_PLUGIN_URL . 'js/admin-script.js',
            array('jquery'),
            TCTG_VERSION,
            true
        );
        
        wp_localize_script('tctg-admin-script', 'tctg_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tctg_nonce')
        ));
    }
    
    /**
     * Display admin notices
     */
    public function admin_notices() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'child-theme-generator') {
            return;
        }
        
        if (isset($_GET['theme_exist']) && $_GET['theme_exist'] == '1') {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . __('A theme with the same name already exists!', TCTG_TEXT_DOMAIN) . '</p>';
            echo '</div>';
        }
        
        if (isset($_GET['theme_created']) && $_GET['theme_created'] == '1') {
            // Don't show the small banner - we'll show a big success message instead
            return;
        }
        
        if (isset($_GET['error'])) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . esc_html($_GET['error']) . '</p>';
            echo '</div>';
        }
    }
    
    /**
     * Handle form submission
     */
    public function handle_form_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['tctg_nonce'], 'create_child_theme')) {
            wp_die(__('Security check failed.', TCTG_TEXT_DOMAIN));
        }
        
        // Check user permissions
        if (!current_user_can('switch_themes')) {
            wp_die(__('You do not have sufficient permissions.', TCTG_TEXT_DOMAIN));
        }
        
        try {
            $this->create_child_theme();
        } catch (Exception $e) {
            wp_redirect(admin_url('themes.php?page=child-theme-generator&error=' . urlencode($e->getMessage())));
            exit;
        }
    }
    
    /**
     * Create child theme
     */
    private function create_child_theme() {
        // Get parent theme
        $parent_theme = wp_get_theme();
        
        if (!$parent_theme->exists()) {
            throw new Exception(__('Parent theme not found.', TCTG_TEXT_DOMAIN));
        }
        
        // Sanitize and validate inputs
        $child_theme_name = $this->sanitize_theme_name($_POST['child_theme_name'] ?? $parent_theme->get('Name'));
        $child_theme_slug = sanitize_title($child_theme_name) . '-child';
        $child_theme_dir = get_theme_root() . '/' . $child_theme_slug;
        
        // Check if theme already exists
        if (is_dir($child_theme_dir)) {
            wp_redirect(admin_url('themes.php?page=child-theme-generator&theme_exist=1'));
            exit;
        }
        
        // Create directory with proper permissions
        if (!wp_mkdir_p($child_theme_dir)) {
            throw new Exception(__('Failed to create child theme directory.', TCTG_TEXT_DOMAIN));
        }
        
        // Create theme files
        $this->create_theme_files($child_theme_dir, $child_theme_slug, $parent_theme);
        
        // Handle thumbnail upload
        $this->handle_thumbnail_upload($child_theme_dir);
        
        // Copy parent files if requested
        if (!empty($_POST['copy_parent_files'])) {
            $this->copy_parent_files($child_theme_dir, $parent_theme);
        }
        
        // Switch to child theme
        switch_theme($child_theme_slug);
        
        // Redirect with success message
        wp_redirect(admin_url('admin.php?page=child-theme-generator&theme_created=1&theme_name=' . urlencode($child_theme_name)));
        exit;
    }
    
    /**
     * Sanitize theme name
     */
    private function sanitize_theme_name($name) {
        $name = sanitize_text_field($name);
        $name = preg_replace('/\s*child\s*/i', '', $name);
        return trim($name);
    }
    
    /**
     * Create theme files
     */
    private function create_theme_files($child_theme_dir, $child_theme_slug, $parent_theme) {
        // Get sanitized form data
        $theme_data = array(
            'name' => sanitize_text_field($_POST['child_theme_name'] ?? $parent_theme->get('Name') . ' Child'),
            'author' => sanitize_text_field($_POST['child_theme_author'] ?? 'MEM Digital'),
            'author_uri' => esc_url_raw($_POST['child_theme_author_uri'] ?? 'https://memdigital.co.uk'),
            'description' => sanitize_textarea_field($_POST['child_theme_description'] ?? 'Child theme of ' . $parent_theme->get('Name')),
            'theme_uri' => esc_url_raw($_POST['child_theme_theme_url'] ?? ''),
            'text_domain' => sanitize_key($_POST['child_theme_text_domain'] ?? $child_theme_slug),
            'tags' => sanitize_text_field($_POST['child_theme_tags'] ?? '')
        );
        
        // Create style.css
        $this->create_style_css($child_theme_dir, $theme_data, $parent_theme);
        
        // Create functions.php
        $this->create_functions_php($child_theme_dir, $theme_data);
        
        // Create index.php
        $this->create_index_php($child_theme_dir);
        
        // Create readme.txt
        $this->create_readme_txt($child_theme_dir);
    }
    
    /**
     * Create style.css file
     */
    private function create_style_css($child_theme_dir, $theme_data, $parent_theme) {
        $style_content = <<<EOT
/*
Theme Name: {$theme_data['name']}
Template: {$parent_theme->get_stylesheet()}
Author: {$theme_data['author']}
Author URI: {$theme_data['author_uri']}
Description: {$theme_data['description']}
Theme URI: {$theme_data['theme_uri']}
Version: 1.0.0
Text Domain: {$theme_data['text_domain']}
Tags: {$theme_data['tags']}
*/

/* ========================================
   MEM Digital Limited
   
   "Code is the new marble!" - MEM Digital
   
   At MEM Digital, we blend timeless design 
   with emerging technology to support 
   forward-thinking business owners. From 
   hosting and websites to AI-powered 
   platforms, we take care of the digital 
   foundations so you can focus on 
   building your empire.
   
   Web & Digital Agency | United Kingdom
   https://memdigital.co.uk
   ======================================== */

/* ===========================================
   Add your custom styles below this comment
   if you want to that is. You can also use
   customizer or your page builder.
   =========================================== */

EOT;

        $this->write_file($child_theme_dir . '/style.css', $style_content);
    }
    
    /**
     * Create functions.php file
     */
    private function create_functions_php($child_theme_dir, $theme_data) {
        // Sanitize text domain for function name (replace dashes with underscores, ensure valid PHP identifier)
        $function_prefix = preg_replace('/[^a-zA-Z0-9_]/', '_', $theme_data['text_domain']);
        $function_prefix = preg_replace('/^[0-9]+/', '', $function_prefix); // Remove leading numbers
        $function_prefix = trim($function_prefix, '_'); // Remove leading/trailing underscores
        
        // Ensure we have a valid function prefix
        if (empty($function_prefix) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $function_prefix)) {
            $function_prefix = 'child_theme';
        }
        
        $functions_content = <<<EOT
<?php
/**
 * {$theme_data['name']} Functions
 * 
 * @package {$theme_data['text_domain']}
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/* ========================================
   MEM Digital Limited
   
   "Code is the new marble!" - MEM Digital
   
   At MEM Digital, we blend timeless design 
   with emerging technology to support 
   forward-thinking business owners. From 
   hosting and websites to AI-powered 
   platforms, we take care of the digital 
   foundations so you can focus on 
   building your empire.
   
   Web & Digital Agency | United Kingdom
   https://memdigital.co.uk
   ======================================== */

/**
 * Enqueue parent theme styles
 */
function {$function_prefix}_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', '{$function_prefix}_enqueue_styles');

/* ===========================================
   Add your custom functions below this line
   
   Example:
   
   // Custom function example
   function my_custom_function() {
       // Your code here
   }
   
   // Hook example
   add_action('init', 'my_custom_function');
   
   =========================================== */

EOT;

        $this->write_file($child_theme_dir . '/functions.php', $functions_content);
    }
    
    /**
     * Create index.php file
     */
    private function create_index_php($child_theme_dir) {
        $index_content = "<?php\n// Silence is golden.\n";
        $this->write_file($child_theme_dir . '/index.php', $index_content);
    }
    
    /**
     * Create readme.txt file
     */
    private function create_readme_txt($child_theme_dir) {
        $readme_content = <<<EOT
Taking a sneaky peek inside the theme files?
No problem at all.
We hope you like what you see.

This child theme was created using The Child Theme Generator
by MEM Digital - making WordPress development simpler.

If you'd like to say hello:
Email: hello@memdigital.co.uk
Visit: https://memdigital.co.uk

Happy coding!
EOT;

        $this->write_file($child_theme_dir . '/readme.txt', $readme_content);
    }
    
    /**
     * Handle thumbnail upload
     */
    private function handle_thumbnail_upload($child_theme_dir) {
        if (empty($_FILES['child_theme_thumbnail']['tmp_name'])) {
            return;
        }
        
        $file = $_FILES['child_theme_thumbnail'];
        
        // Validate file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            throw new Exception(__('Invalid file type. Only JPG, PNG, and GIF files are allowed.', TCTG_TEXT_DOMAIN));
        }
        
        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception(__('File size too large. Maximum size is 2MB.', TCTG_TEXT_DOMAIN));
        }
        
        // Move uploaded file
        $screenshot_path = $child_theme_dir . '/screenshot.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!move_uploaded_file($file['tmp_name'], $screenshot_path)) {
            throw new Exception(__('Failed to upload thumbnail image.', TCTG_TEXT_DOMAIN));
        }
    }
    
    /**
     * Copy parent theme files
     */
    private function copy_parent_files($child_theme_dir, $parent_theme) {
        $files_to_copy = isset($_POST['parent_files']) ? (array) $_POST['parent_files'] : array();
        $parent_dir = $parent_theme->get_stylesheet_directory();
        
        foreach ($files_to_copy as $file) {
            $file = sanitize_file_name($file);
            $source = $parent_dir . '/' . $file;
            $destination = $child_theme_dir . '/' . $file;
            
            if (file_exists($source) && !strpos($file, '..')) {
                copy($source, $destination);
            }
        }
    }
    
    /**
     * AJAX handler to get parent theme files
     */
    public function ajax_get_parent_files() {
        check_ajax_referer('tctg_nonce', 'nonce');
        
        $parent_theme = wp_get_theme();
        $parent_dir = $parent_theme->get_stylesheet_directory();
        
        $files = $this->get_theme_files($parent_dir);
        
        wp_send_json_success($files);
    }
    
    /**
     * Get theme files recursively
     */
    private function get_theme_files($dir, $base_dir = null) {
        if ($base_dir === null) {
            $base_dir = $dir;
        }
        
        $files = array();
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $full_path = $dir . '/' . $item;
            $relative_path = str_replace($base_dir . '/', '', $full_path);
            
            if (is_file($full_path)) {
                $files[] = $relative_path;
            } elseif (is_dir($full_path) && !in_array($item, array('node_modules', '.git', '.svn'))) {
                $files = array_merge($files, $this->get_theme_files($full_path, $base_dir));
            }
        }
        
        return $files;
    }
    
    /**
     * Write content to file
     */
    private function write_file($path, $content) {
        if (file_put_contents($path, $content) === false) {
            throw new Exception(sprintf(__('Failed to create file: %s', TCTG_TEXT_DOMAIN), basename($path)));
        }
    }
    
    /**
     * Display admin page content
     */
    public function admin_page_content() {
        $parent_theme = wp_get_theme();
        $theme_created = isset($_GET['theme_created']) && $_GET['theme_created'] == '1';
        $created_theme_name = isset($_GET['theme_name']) ? sanitize_text_field($_GET['theme_name']) : '';
        ?>
        <div class="tctg-wrap">
            <div class="tctg-header">
                <a href="https://memdigital.co.uk" target="_blank" rel="noopener noreferrer" class="tctg-logo-link">
                    <img src="https://memdigital.co.uk/snippets/MEM-Digital-Circle-Symbol.svg" alt="<?php esc_attr_e('MEM Digital Logo', TCTG_TEXT_DOMAIN); ?>" class="tctg-logo">
                </a>
                <h1><?php _e('The Child Theme Generator', TCTG_TEXT_DOMAIN); ?></h1>
                <p class="tctg-description"><?php _e('Create a child theme for your current active theme in seconds.', TCTG_TEXT_DOMAIN); ?></p>
            </div>
            
            <?php if ($theme_created): ?>
                <!-- Success Message -->
                <div class="tctg-success-container">
                    <div class="tctg-success-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <h2 class="tctg-success-title"><?php _e('Your Child Theme Was Created Successfully!', TCTG_TEXT_DOMAIN); ?></h2>
                    <p class="tctg-success-description">
                        <?php if ($created_theme_name): ?>
                            <?php printf(__('"%s" has been created and activated. <br><br> You can now safely customize your theme without losing changes when the parent theme updates - AND - you can deactivate and delete this plugin.', TCTG_TEXT_DOMAIN), esc_html($created_theme_name)); ?>
                        <?php else: ?>
                            <?php _e('Your child theme has been created and activated. <br><br> You can now safely customize your theme without losing changes when the parent theme updates - AND - you can deactivate and delete this plugin.', TCTG_TEXT_DOMAIN); ?>
                        <?php endif; ?>
                    </p>
                    
                    <div class="tctg-success-actions">
                        <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-primary tctg-success-button">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php _e('View All Themes', TCTG_TEXT_DOMAIN); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-secondary tctg-success-button">
                            <span class="dashicons dashicons-admin-customizer"></span>
                            <?php _e('Customize Theme', TCTG_TEXT_DOMAIN); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=child-theme-generator')); ?>" class="button button-secondary tctg-success-button">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('Create Another Child Theme', TCTG_TEXT_DOMAIN); ?>
                        </a>
                    </div>
                    
                    <div class="tctg-success-info">
                        <h3><?php _e('What\'s Next?', TCTG_TEXT_DOMAIN); ?></h3>
                        <ul class="tctg-success-list">
                            <li><strong><?php _e('Safe Customization:', TCTG_TEXT_DOMAIN); ?></strong> <?php _e('Your customizations are now protected from parent theme updates', TCTG_TEXT_DOMAIN); ?></li>
                            <li><strong><?php _e('Edit Files:', TCTG_TEXT_DOMAIN); ?></strong> <?php _e('Add custom CSS to style.css or PHP functions to functions.php', TCTG_TEXT_DOMAIN); ?></li>
                            <li><strong><?php _e('Use Customizer:', TCTG_TEXT_DOMAIN); ?></strong> <?php _e('Make visual changes using WordPress Customizer', TCTG_TEXT_DOMAIN); ?></li>
                            <li><strong><?php _e('Update Parent:', TCTG_TEXT_DOMAIN); ?></strong> <?php _e('Parent theme updates won\'t affect your customizations', TCTG_TEXT_DOMAIN); ?></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Regular Form -->
                <div class="tctg-current-theme">
                    <p><strong><?php _e('Current Active Theme:', TCTG_TEXT_DOMAIN); ?></strong> <?php echo esc_html($parent_theme->get('Name')); ?> v<?php echo esc_html($parent_theme->get('Version')); ?></p>
                </div>
                
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" id="tctg-form">
                <?php wp_nonce_field('create_child_theme', 'tctg_nonce'); ?>
                <input type="hidden" name="action" value="create_child_theme_action">
                
                <div class="tctg-form-container">
                    <div class="tctg-form-field">
                        <label for="child_theme_name" class="tctg-label">
                            <?php _e('Child Theme Name', TCTG_TEXT_DOMAIN); ?><span class="required">*</span>
                        </label>
                        <p class="tctg-description"><?php _e('Keep it short and sweet', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="text" name="child_theme_name" id="child_theme_name" class="tctg-input" value="" required>
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_author" class="tctg-label">
                            <?php _e('Author', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Your name or business name', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="text" name="child_theme_author" id="child_theme_author" class="tctg-input" value="">
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_author_uri" class="tctg-label">
                            <?php _e('Author URL', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Your website URL', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="url" name="child_theme_author_uri" id="child_theme_author_uri" class="tctg-input" value="">
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_description" class="tctg-label">
                            <?php _e('Description', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Brief description of your child theme', TCTG_TEXT_DOMAIN); ?></p>
                        <textarea name="child_theme_description" id="child_theme_description" class="tctg-textarea" rows="3"></textarea>
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_theme_url" class="tctg-label">
                            <?php _e('Theme URL', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Website where this theme will be used (optional)', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="url" name="child_theme_theme_url" id="child_theme_theme_url" class="tctg-input">
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_text_domain" class="tctg-label">
                            <?php _e('Text Domain', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Used for translations (auto-generated)', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="text" name="child_theme_text_domain" id="child_theme_text_domain" class="tctg-input" readonly>
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_tags" class="tctg-label">
                            <?php _e('Tags', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('Comma-separated tags (optional)', TCTG_TEXT_DOMAIN); ?></p>
                        <input type="text" name="child_theme_tags" id="child_theme_tags" class="tctg-input" 
                               placeholder="<?php esc_attr_e('responsive, custom, business', TCTG_TEXT_DOMAIN); ?>">
                    </div>
                    
                    <div class="tctg-form-field">
                        <label for="child_theme_thumbnail" class="tctg-label">
                            <?php _e('Screenshot', TCTG_TEXT_DOMAIN); ?>
                        </label>
                        <p class="tctg-description"><?php _e('1200 × 900 pixels recommended (optional)', TCTG_TEXT_DOMAIN); ?></p>
                        <div class="tctg-file-upload-wrapper">
                            <label for="child_theme_thumbnail" class="tctg-file-upload-button">
                                <?php _e('Choose File', TCTG_TEXT_DOMAIN); ?>
                            </label>
                            <input type="file" name="child_theme_thumbnail" id="child_theme_thumbnail" 
                                   accept="image/jpeg,image/png,image/gif">
                            <span class="tctg-file-upload-filename"><?php _e('No file chosen', TCTG_TEXT_DOMAIN); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="tctg-advanced-section">
                    <h3 class="tctg-advanced-toggle">
                        <button type="button" class="button-link" aria-expanded="false">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Advanced Options', TCTG_TEXT_DOMAIN); ?>
                        </button>
                    </h3>
                    
                    <div class="tctg-advanced-content" style="display: none;">
                        <p class="description"><?php _e('Select common parent theme files to copy to your child theme. Only select files you plan to customize.', TCTG_TEXT_DOMAIN); ?></p>
                        
                        <div class="tctg-common-files">
                            <h4><?php _e('Copy these common files if they exist:', TCTG_TEXT_DOMAIN); ?></h4>
                            
                            <div class="tctg-file-grid">
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="header.php">
                                    <span class="tctg-file-name">header.php</span>
                                    <span class="tctg-file-desc"><?php _e('Site header template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="footer.php">
                                    <span class="tctg-file-name">footer.php</span>
                                    <span class="tctg-file-desc"><?php _e('Site footer template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="index.php">
                                    <span class="tctg-file-name">index.php</span>
                                    <span class="tctg-file-desc"><?php _e('Main template fallback', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="single.php">
                                    <span class="tctg-file-name">single.php</span>
                                    <span class="tctg-file-desc"><?php _e('Single post template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="page.php">
                                    <span class="tctg-file-name">page.php</span>
                                    <span class="tctg-file-desc"><?php _e('Page template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="archive.php">
                                    <span class="tctg-file-name">archive.php</span>
                                    <span class="tctg-file-desc"><?php _e('Archive template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="search.php">
                                    <span class="tctg-file-name">search.php</span>
                                    <span class="tctg-file-desc"><?php _e('Search results template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="404.php">
                                    <span class="tctg-file-name">404.php</span>
                                    <span class="tctg-file-desc"><?php _e('Error page template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                                
                                <label class="tctg-file-option">
                                    <input type="checkbox" name="parent_files[]" value="sidebar.php">
                                    <span class="tctg-file-name">sidebar.php</span>
                                    <span class="tctg-file-desc"><?php _e('Sidebar template', TCTG_TEXT_DOMAIN); ?></span>
                                </label>
                            </div>
                            
                            <div class="tctg-file-actions">
                                <button type="button" class="tctg-select-all-common button-secondary">
                                    <?php _e('Select All', TCTG_TEXT_DOMAIN); ?>
                                </button>
                                <button type="button" class="tctg-select-none-common button-secondary">
                                    <?php _e('Select None', TCTG_TEXT_DOMAIN); ?>
                                </button>
                            </div>
                            
                            <p class="tctg-file-note">
                                <strong><?php _e('Note:', TCTG_TEXT_DOMAIN); ?></strong> 
                                <?php _e('Only files that exist in your parent theme will be copied. Files are copied exactly as they are - you can then customize them in your child theme.', TCTG_TEXT_DOMAIN); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="tctg-submit">
                    <?php submit_button(__('Create Child Theme', TCTG_TEXT_DOMAIN), 'primary', 'submit', false); ?>
                </div>
                </form>
                
                <div class="tctg-support-box">
                    <p><?php _e('Need website support?', TCTG_TEXT_DOMAIN); ?> <a href="mailto:hello@memdigital.co.uk">hello@memdigital.co.uk</a></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Initialize the plugin
new TheChildThemeGenerator();

/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, function() {
    // Check minimum requirements
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        wp_die(__('The Child Theme Generator requires PHP 7.4 or higher.', TCTG_TEXT_DOMAIN));
    }
    
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        wp_die(__('The Child Theme Generator requires WordPress 5.0 or higher.', TCTG_TEXT_DOMAIN));
    }
});

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});