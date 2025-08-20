# The Child Theme Generator

[![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)](https://github.com/yourusername/the-child-theme-generator)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

> Tired of overly complicated child theme generators? Our super simple and lightweight child theme generator makes the job easy - no coding required.

## 🚀 Features

- **One-Click Child Theme Creation** - Generate a complete child theme in seconds
- **Custom Branding** - Add your own author details, descriptions, and screenshots
- **Common File Selection** - Choose from 9 most common WordPress template files to copy
- **Auto-Generated Text Domains** - Proper internationalization setup
- **Form Validation** - Real-time feedback prevents common mistakes
- **Mobile Responsive** - Works perfectly on all devices
- **Accessibility Ready** - Built with WCAG guidelines in mind
- **Security First** - Built with WordPress security best practices

## 📸 Screenshots

![Main Interface](screenshots/main-interface.png)
*Clean, intuitive form interface*

![Success Page](screenshots/success-page.png)
*Beautiful success experience with next steps*

![Advanced Options](screenshots/advanced-options.png)
*Template file selection for customization*

## 🛠 Installation

### Automatic Installation

1. Download the latest release from the [Releases page](https://github.com/bhambrabland/the-child-theme-generator/releases)
2. Go to your WordPress admin dashboard
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the zip file and click **Install Now**
5. Activate the plugin
6. Look for **TCT Generator** in your main admin menu

### Manual Installation

1. Clone this repository to your `/wp-content/plugins/` directory:
   ```bash
   git clone https://github.com/bhambrabland/the-child-theme-generator.git
   ```
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look for **TCT Generator** in your main admin menu

## 📋 Usage

### Basic Usage

1. **Access the Plugin**: Click on **TCT Generator** in your WordPress admin menu
2. **Fill in Details**: 
   - Enter your child theme name (required)
   - Add author information (optional)
   - Provide a description (optional)
   - Upload a screenshot image (optional)
3. **Create Theme**: Click the "Create Child Theme" button
4. **Automatic Activation**: Your child theme is created and activated immediately

### Advanced Options

- **Copy Parent Files**: Select common template files to copy from your parent theme
- **Available Files**: `header.php`, `footer.php`, `index.php`, `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`, `sidebar.php`
- **Smart Selection**: Only files that exist in your parent theme will be copied

## 🔧 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Themes directory write permissions
- Active parent theme

## 📁 What Gets Created

Every child theme includes:

```
child-theme-name/
├── style.css          # Theme header and custom styles
├── functions.php      # Parent theme enqueuing and custom functions
├── index.php          # Security placeholder
├── readme.txt         # Theme documentation
└── screenshot.png     # Optional theme preview image (if uploaded)
```

## 🎨 Generated Code Example

### style.css
```css
/*
Theme Name: Your Theme Name Child
Template: parent-theme
Author: Your Name
Description: Child theme of Parent Theme
Version: 1.0.0
*/

/* MEM Digital branding comments */
/* Custom styles section */
```

### functions.php
```php
<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/* MEM Digital branding comments */

/**
 * Enqueue parent theme styles
 */
function your_theme_enqueue_styles() {
    // Parent theme stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    
    // Child theme stylesheet
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'your_theme_enqueue_styles');
```

## 🔌 Hooks & Filters

For developers who want to extend functionality:

```php
// Action fired before child theme creation
do_action('tctg_before_create_child_theme');

// Action fired after successful creation
do_action('tctg_after_create_child_theme');

// Filter to modify which files get created
apply_filters('tctg_child_theme_files', $files);

// Filter to customize the style.css template
apply_filters('tctg_style_css_template', $template);

// Filter to customize the functions.php template
apply_filters('tctg_functions_php_template', $template);
```

## 🐛 Bug Reports & Feature Requests

Found a bug or have a feature request? Please open an issue on our [GitHub Issues page](https://github.com/bhambrabland/the-child-theme-generator/issues).

When reporting bugs, please include:
- WordPress version
- PHP version
- Theme being used
- Steps to reproduce the issue
- Any error messages

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Set up a local WordPress development environment
3. Symlink or copy the plugin to your `wp-content/plugins/` directory
4. Make your changes and test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the GPL v2+ License - see the [LICENSE](LICENSE) file for details.

## 👥 Credits

**Developed by [MEM Digital](https://memdigital.co.uk)**

*"Code is the new marble!" - MEM Digital*

At MEM Digital, we blend timeless design with emerging technology to support forward-thinking business owners. From hosting and websites to AI-powered platforms, we take care of the digital foundations so you can focus on building your empire.

- **Website**: [memdigital.co.uk](https://memdigital.co.uk)
- **Email**: hello@memdigital.co.uk

## 📈 Changelog

### [1.1.0] - 2025-01-20
- Improved function name sanitization for PHP compatibility
- Updated branding comments with website URL
- Enhanced user-friendly messaging in generated CSS files
- Moved plugin to main admin menu as "TCT Generator"
- Added beautiful success page with next steps
- Improved button styling and animations

### [1.0.0] - 2025-01-01
- Initial release
- Simple child theme creation interface
- Common template files selection
- Form validation and error handling
- Mobile-responsive design
- Security enhancements

## ⭐ Support

If you find this plugin helpful, please consider:
- ⭐ Starring this repository
- 🐛 Reporting bugs or requesting features
- 💬 Sharing with others who might find it useful
- 📧 Contacting us for custom WordPress development needs

---

**Made with ❤️ by [MEM Digital](https://memdigital.co.uk)**
