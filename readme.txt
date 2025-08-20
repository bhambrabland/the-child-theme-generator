=== The Child Theme Generator ===
Contributors: memdigital
Donate link: https://memdigital.co.uk
Tags: child-theme, theme-development, developer-tools, customization, child-themes
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tired of overly complicated child theme generators? Our super simple and lightweight child theme generator makes the job easy - no coding required.

== Description ==

**The Child Theme Generator** is the simplest way to create professional child themes for WordPress. Built by developers, for developers (and everyone else too!).

### Why Another Child Theme Generator?

We were tired of overly complicated child theme generators that try to do everything. Sometimes you just need a clean, simple tool that creates a proper child theme without the bloat.

### What Makes This Different?

* **Super Simple Interface** - No confusing options or unnecessary complexity
* **Lightweight & Fast** - Minimal footprint, maximum performance  
* **Developer Friendly** - Clean, well-commented code in generated files
* **Common File Selection** - Copy standard WordPress template files when needed
* **Professional Output** - Properly structured child themes every time
* **Security First** - Built with WordPress security best practices

### Features

* **One-Click Child Theme Creation** - Generate a complete child theme in seconds
* **Custom Branding** - Add your own author details, descriptions, and screenshots
* **Common File Selection** - Choose from 9 most common WordPress template files to copy
* **Auto-Generated Text Domains** - Proper internationalization setup
* **Form Validation** - Real-time feedback prevents common mistakes
* **Mobile Responsive** - Works perfectly on all devices
* **Accessibility Ready** - Built with WCAG guidelines in mind

### Perfect For

* **Theme Developers** - Quickly create child themes for client projects
* **Agency Owners** - Streamline your development workflow
* **WordPress Consultants** - Professional tool for professional results
* **DIY Website Owners** - No coding knowledge required
* **Anyone** - Who wants to customize themes safely

### What Gets Created

Every child theme includes:
* **style.css** - Properly formatted with your custom details
* **functions.php** - Clean starter file with helpful comments
* **index.php** - Security placeholder file
* **readme.txt** - Documentation for your child theme
* **screenshot.png** - Optional custom theme screenshot

### Developer Notes

Generated child themes include:
* Proper parent theme enqueueing
* Clean, commented code structure
* Helpful code examples and guidelines
* MEM Digital signature comments (easily customizable)
* WordPress coding standards compliance

### Security & Best Practices

* Nonce verification for all form submissions
* Proper capability checks and user permissions
* Input sanitization and output escaping
* File upload validation and security
* No direct file access prevention

== Installation ==

### Automatic Installation

1. Go to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Search for "The Child Theme Generator"
4. Click **Install Now** and then **Activate**
5. Look for **TCT Generator** in your main admin menu

### Manual Installation

1. Download the plugin zip file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Activate the plugin
5. Look for **TCT Generator** in your main admin menu

### After Installation

1. Make sure you have a parent theme activated
2. Click on **TCT Generator** in your main admin menu
3. Fill in your desired child theme details
4. Click **Create Child Theme**
5. Your new child theme will be created and activated automatically

== Usage ==

### Basic Usage

1. **Access the Plugin**: Click on **TCT Generator** in your WordPress admin menu (located in the main sidebar)
2. **Fill in Details**: 
   - Enter your child theme name (required)
   - Add author information (optional)
   - Provide a description (optional)
   - Upload a screenshot image (optional)
3. **Create Theme**: Click the "Create Child Theme" button
4. **Automatic Activation**: Your child theme is created and activated immediately

### Advanced Options

Click "Advanced Options" to access additional features:

* **Copy Parent Files**: Select common template files to copy from your parent theme
* **Available Files**: header.php, footer.php, index.php, single.php, page.php, archive.php, search.php, 404.php, sidebar.php
* **Smart Selection**: Only files that exist in your parent theme will be copied

### Form Fields Explained

* **Child Theme Name** *(Required)*: The display name for your child theme
* **Author**: Your name or business name (defaults to "MEM Digital")
* **Author URL**: Your website URL
* **Description**: Brief description of your child theme
* **Theme URL**: Website where this theme will be used
* **Text Domain**: Auto-generated for translations
* **Tags**: Comma-separated keywords for the theme
* **Screenshot**: Theme preview image (1200×900px recommended)

### Tips for Best Results

* **Keep names simple**: Avoid special characters in theme names
* **Use descriptive names**: Make it easy to identify your child theme later
* **Only copy files you'll modify**: Don't copy parent files unless you plan to customize them
* **Upload quality screenshots**: Use high-resolution images for better presentation

== Frequently Asked Questions ==

= Where do I find the plugin after installation? =

After activation, look for **TCT Generator** in your main WordPress admin menu. It's no longer under Appearance - it now has its own dedicated menu item.

= Do I need coding knowledge to use this plugin? =

Not at all! The basic child theme creation requires no coding knowledge. Just fill in the form and click create. Advanced users can optionally copy specific parent theme files.

= Will this work with any WordPress theme? =

Yes! This plugin works with any properly coded WordPress theme. It reads your currently active theme and creates a child theme based on it.

= What happens to my parent theme? =

Nothing! Your parent theme remains completely unchanged. The child theme inherits all functionality and can be customized safely.

= Can I customize the generated files? =

Absolutely! The generated child theme files include helpful comments and examples to guide your customizations.

= Is it safe to update my parent theme after creating a child theme? =

Yes! That's the whole point of child themes. You can safely update your parent theme without losing your customizations.

= Can I create multiple child themes from the same parent? =

Yes, you can create as many child themes as you need. Each will have a unique name and directory.

= What if I make a mistake in the form? =

The plugin includes real-time form validation to catch common mistakes before you submit. You'll get helpful error messages if something needs fixing.

= Does this work with premium themes? =

Yes! This plugin works with free themes, premium themes, and custom themes alike.

= Can I add a custom screenshot to my child theme? =

Absolutely! You can upload a custom screenshot image (JPG, PNG, or GIF) that will appear in your Themes page.

= Will my child theme appear in the WordPress themes directory? =

No, child themes created by this plugin are for your site only. They appear in your local Themes page under Appearance > Themes.

= Can I copy specific files from my parent theme? =

Yes! The advanced options section lets you select from 9 common WordPress template files including header.php, footer.php, index.php, single.php, page.php, archive.php, search.php, 404.php, and sidebar.php. Only files that exist in your parent theme will be copied.

= What should I do if I get a critical error? =

This version (1.1.0) fixes the critical error issues from previous versions. If you still experience problems, deactivate and reactivate the plugin, or contact support.

== Screenshots ==

1. **Main Interface** - TCT Generator menu location and clean form interface
2. **Advanced Options** - Common template files selection for customization
3. **Form Validation** - Real-time feedback prevents common mistakes
4. **Success Screen** - Confirmation when your child theme is created
5. **Generated Files** - Example of clean, well-commented child theme files

== Changelog ==

= 1.1.0 =
* Fixed critical error in generated functions.php file
* Improved function name sanitization for PHP compatibility
* Updated branding comments with website URL
* Enhanced user-friendly messaging in generated CSS files
* Moved plugin to main admin menu as "TCT Generator"
* Updated menu icon and positioning for better accessibility

= 1.0.0 =
* Initial release
* Simple child theme creation interface
* Common template files selection (9 most used WordPress files)
* Form validation and error handling
* Mobile-responsive design
* Accessibility improvements
* Security enhancements
* WordPress coding standards compliance

== Upgrade Notice ==

= 1.1.0 =
Critical update: Fixes PHP errors in generated child themes. Plugin now has its own main menu location for easier access.

== Developer Information ==

This plugin is developed and maintained by **MEM Digital**, a UK-based web and digital agency specializing in WordPress development, hosting, and AI-powered automation.

### About MEM Digital

"Code is the new marble!" - MEM Digital

At MEM Digital, we blend timeless design with emerging technology to support forward-thinking business owners. From hosting and websites to AI-powered platforms, we take care of the digital foundations so you can focus on building your empire.

**Website:** [memdigital.co.uk](https://memdigital.co.uk)
**Email:** hello@memdigital.co.uk

### Contributing

Found a bug or have a feature request? We'd love to hear from you! 

### Support

For support questions, please use the WordPress.org support forums. For business inquiries or custom development needs, contact us directly through our website.

== Technical Details ==

### Requirements

* WordPress 5.0 or higher
* PHP 7.4 or higher
* Themes directory write permissions
* Active parent theme

### Hooks & Filters

The plugin provides several hooks for developers who want to extend functionality:

* `tctg_before_create_child_theme` - Action fired before child theme creation
* `tctg_after_create_child_theme` - Action fired after successful creation
* `tctg_child_theme_files` - Filter to modify which files get created
* `tctg_style_css_template` - Filter to customize the style.css template
* `tctg_functions_php_template` - Filter to customize the functions.php template

### File Structure

Generated child themes follow WordPress standards:

```
child-theme-name/
├── style.css          (Theme header and custom styles)
├── functions.php      (Parent theme enqueuing and custom functions)
├── index.php          (Security placeholder)
├── readme.txt         (Theme documentation)
└── screenshot.png     (Optional theme preview image)
```

### Menu Location

The plugin adds a top-level menu item called **TCT Generator** to your WordPress admin. This provides easy access without cluttering the Appearance menu.