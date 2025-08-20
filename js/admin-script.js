/**
* The Child Theme Generator - Admin JavaScript
* Author: MEM Digital
* URL: https://memdigital.co.uk
*/

jQuery(document).ready(function($) {
   'use strict';
   
   // Initialize the plugin
   const ChildThemeGenerator = {
       
       init: function() {
           this.bindEvents();
           this.autoGenerateTextDomain();
           this.setupFormValidation();
       },
       
       bindEvents: function() {
           // Advanced options toggle - using event delegation
           $(document).on('click', '.tctg-advanced-toggle button', this.toggleAdvancedOptions);
           
           // Auto-generate text domain when theme name changes
           $(document).on('input', '#child_theme_name', this.autoGenerateTextDomain);
           
           // Form submission with loading state
           $(document).on('submit', '#tctg-form', this.handleFormSubmission);
           
           // File selection helpers
           $(document).on('click', '.tctg-select-all-common', this.selectAllCommonFiles);
           $(document).on('click', '.tctg-select-none-common', this.selectNoCommonFiles);
           
           // File upload display
           $(document).on('change', '#child_theme_thumbnail', this.handleFileUploadDisplay);
       },
       
       toggleAdvancedOptions: function(e) {
           e.preventDefault();
           e.stopPropagation();
           
           const $button = $(this);
           const $content = $button.closest('.tctg-advanced-section').find('.tctg-advanced-content');
           const isExpanded = $button.attr('aria-expanded') === 'true';
           
           if (isExpanded) {
               $content.slideUp(300);
               $button.attr('aria-expanded', 'false');
           } else {
               $content.slideDown(300);
               $button.attr('aria-expanded', 'true');
           }
       },
       
       autoGenerateTextDomain: function() {
           const themeName = $('#child_theme_name').val();
           if (themeName) {
               const textDomain = themeName
                   .toLowerCase()
                   .replace(/[^a-z0-9]/g, '-')
                   .replace(/-+/g, '-')
                   .replace(/^-|-$/g, '') + '-child';
               
               $('#child_theme_text_domain').val(textDomain);
           }
       },
       
       handleFileUploadDisplay: function() {
           const fileInput = this;
           const $filename = $('.tctg-file-upload-filename');
           
           if (fileInput.files && fileInput.files.length > 0) {
               $filename.text(fileInput.files[0].name);
           } else {
               $filename.text('No file chosen');
           }
       },
       
       setupFormValidation: function() {
           // Add required field validation
           $(document).on('blur', '#child_theme_name', function() {
               const $field = $(this);
               const value = $field.val().trim();
               
               if (value.length < 2) {
                   $field.addClass('error');
                   ChildThemeGenerator.showFieldError($field, 'Theme name must be at least 2 characters long.');
               } else {
                   $field.removeClass('error');
                   ChildThemeGenerator.hideFieldError($field);
               }
           });
           
           // URL validation
           $(document).on('blur', 'input[type="url"]', function() {
               const $field = $(this);
               const value = $field.val().trim();
               
               if (value && !ChildThemeGenerator.isValidUrl(value)) {
                   $field.addClass('error');
                   ChildThemeGenerator.showFieldError($field, 'Please enter a valid URL (including http:// or https://).');
               } else {
                   $field.removeClass('error');
                   ChildThemeGenerator.hideFieldError($field);
               }
           });
           
           // File upload validation
           $(document).on('change', '#child_theme_thumbnail', function() {
               const file = this.files[0];
               const $field = $(this);
               
               if (file) {
                   // Check file size (2MB limit)
                   if (file.size > 2 * 1024 * 1024) {
                       $field.addClass('error');
                       ChildThemeGenerator.showFieldError($field, 'File size must be less than 2MB.');
                       this.value = '';
                       return;
                   }
                   
                   // Check file type
                   const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                   if (!allowedTypes.includes(file.type)) {
                       $field.addClass('error');
                       ChildThemeGenerator.showFieldError($field, 'Only JPG, PNG, and GIF files are allowed.');
                       this.value = '';
                       return;
                   }
                   
                   $field.removeClass('error');
                   ChildThemeGenerator.hideFieldError($field);
               }
           });
       },
       
       showFieldError: function($field, message) {
           $field.addClass('error');
           const $error = $field.siblings('.field-error');
           if ($error.length) {
               $error.text(message);
           } else {
               $field.after('<span class="field-error" style="color: #ee2737; font-size: 12px; display: block; margin-top: 5px;">' + message + '</span>');
           }
       },
       
       hideFieldError: function($field) {
           $field.removeClass('error');
           $field.siblings('.field-error').remove();
       },
       
       isValidUrl: function(string) {
           try {
               new URL(string);
               return true;
           } catch (_) {
               return false;
           }
       },
       
       handleFormSubmission: function(e) {
           const $form = $(this);
           const $submitButton = $form.find('.button-primary');
           
           // Check for validation errors
           const $errorFields = $form.find('.error');
           if ($errorFields.length > 0) {
               e.preventDefault();
               $errorFields.first().focus();
               ChildThemeGenerator.showNotice('Please fix the errors before submitting.', 'error');
               return false;
           }
           
           // Show loading state
           $form.addClass('tctg-loading');
           $submitButton.prop('disabled', true);
           
           // Change button text
           const originalText = $submitButton.val();
           $submitButton.val('Creating Child Theme...');
           
           // If form validation passes, allow normal submission
           // The loading state will persist until page redirect
       },
       
       selectAllCommonFiles: function(e) {
           e.preventDefault();
           $('.tctg-common-files input[type="checkbox"]').prop('checked', true);
       },
       
       selectNoCommonFiles: function(e) {
           e.preventDefault();
           $('.tctg-common-files input[type="checkbox"]').prop('checked', false);
       },
       
       showNotice: function(message, type) {
           type = type || 'info';
           
           const $notice = $('<div class="notice notice-' + type + ' is-dismissible tctg-notice"><p>' + message + '</p></div>');
           
           $('.tctg-wrap').prepend($notice);
           
           // Auto-dismiss after 5 seconds
           setTimeout(function() {
               $notice.fadeOut(300, function() {
                   $(this).remove();
               });
           }, 5000);
       }
   };
   
   // Initialize the plugin
   ChildThemeGenerator.init();
   
   // Add smooth scrolling for better UX
   $('html').css('scroll-behavior', 'smooth');
   
   // Focus management for accessibility
   $(document).on('keydown', '.tctg-advanced-toggle button', function(e) {
       if (e.key === 'Enter' || e.key === ' ') {
           e.preventDefault();
           $(this).click();
       }
   });
   
   // Auto-save form data to localStorage (not for sensitive data)
   const formFields = ['child_theme_name', 'child_theme_author', 'child_theme_author_uri', 'child_theme_description', 'child_theme_theme_url', 'child_theme_tags'];
   
   formFields.forEach(function(fieldId) {
       const $field = $('#' + fieldId);
       
       // Load saved value on page load
       const savedValue = localStorage.getItem('tctg_' + fieldId);
       if (savedValue && !$field.val()) {
           $field.val(savedValue);
       }
       
       // Save on change using event delegation
       $(document).on('input', '#' + fieldId, function() {
           localStorage.setItem('tctg_' + fieldId, $(this).val());
       });
   });
   
   // Clear saved data on successful form submission
   $(document).on('submit', '#tctg-form', function() {
       formFields.forEach(function(fieldId) {
           localStorage.removeItem('tctg_' + fieldId);
       });
   });
   
   // Trigger auto-generation on page load
   ChildThemeGenerator.autoGenerateTextDomain();
});