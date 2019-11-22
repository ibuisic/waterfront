<?php

/**
 * @file
 * theme-settings.php
 *
 * Provides theme settings
 *
 * @see ./includes/settings.inc
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implementation of hook_form_system_theme_settings_alter()
 *
 * @param $form
 *   Nested array of form elements that comprise the form.
 *
 * @param $form_state
 *   A keyed array containing the current state of the form.
 */
function waterfront_basis_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  // Theme info
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $theme_path =  \Drupal::theme()->getActiveTheme()->getPath();

  // Regions
  $region_list = system_region_list($theme, $show = REGIONS_ALL);
  $exclude_regions = array('hidden');

  // For the custom form handler
  $theme_file = drupal_get_path('theme', $theme) . '/theme-settings.php';
  $build_info = $form_state->getBuildInfo();
  if (!in_array($theme_file, $build_info['files'])) {
    $build_info['files'][] = $theme_file;
  }
  $form_state->setBuildInfo($build_info);
  $form['#submit'][] = $theme . '_form_system_theme_settings_submit';


    // Vertical tabs
  $form['waterfront_basis'] = array(
    '#type' => 'vertical_tabs',
    '#prefix' => '<h2><small>' . t('Settings') . '</small></h2>',
    '#weight' => -10,
    '#description' => t('Note: Some of these settings require you to <a href="@cache-link">flush caches.</a></br>',
      array(
        '@cache-link' => Url::fromRoute('system.performance_settings')->toString(),
      )
    )
  );

  // General settings
  $form['settings'] = array(
    '#type' => 'details',
    '#title' => t('General'),
    '#group' => 'waterfront_basis',
  );

  $form['settings']['general'] = array(
    '#type' => 'details',
    '#title' => 'General theme Settings',
    '#collapsible' => true,
    '#open' => true,
  );

  $form['settings']['general']['inline_logo'] = array(
    '#type' => 'checkbox',
    '#title' => t('Inline SVG logo'),
    '#description' => t('Place logo code inside HTML.'),
    '#default_value' => theme_get_setting('inline_logo')
  );


  $form['settings']['general']['menu_icons'] = array(
    '#type' => 'checkbox',
    '#title' => t('Menu icons'),
    '#description' => t('When this is checked you can use a seperator <b> | </b> and write down classes and it will generate an <code>&#60;i&#62;</code> tag with those classes.'),
    '#default_value' => theme_get_setting('menu_icons')
  );


  // Content
  $form['settings']['content'] = array(
    '#type' => 'details',
    '#collapsible' => true,
    '#title' => t('Content')
  );

  $form['settings']['content']['main_container_classes'] = array(
    '#type' => 'textfield',
    '#title' => t('Main content classes'),
    '#default_value' => theme_get_setting('main_container_classes')
  );

  // Forms
  $form['forms'] = array(
    '#type' => 'details',
    '#title' => t('Forms'),
    '#group' => 'waterfront_basis',
  );

  $form['forms']['general'] = array(
    '#type' => 'details',
    '#title' => 'General Form Settings',
    '#collapsible' => true,
    '#open' => true,
  );

  $form['forms']['general']['submit_classes'] = array(
    '#type' => 'textfield',
    '#title' => t('Submit Button classes'),
    '#default_value' => theme_get_setting('submit_classes')
  );

  $form['forms']['general']['submit_button'] = array(
    '#type' => 'checkbox',
    '#title' => t('Submit buttons'),
    '#description' => t('Convert all input submit elements to buttons. Note: use at your own risk, some bugs may occur!'),
    '#default_value' => theme_get_setting('submit_button')
  );

  // Images
  $form['images_and_tables'] = array(
    '#type' => 'details',
    '#title' => t('Images & Tables'),
    '#group' => 'waterfront_basis',
  );

  $form['images_and_tables']['images'] = array(
    '#type' => 'details',
    '#title' => 'Image Settings',
    '#collapsible' => true,
    '#open' => true,
  );

  $form['images_and_tables']['images']['image_classes'] = array(
    '#type' => 'textfield',
    '#title' => t('Image classes'),
    '#default_value' => theme_get_setting('image_classes')
  );


  // Tables
  $form['images_and_tables']['tables'] = array(
    '#type' => 'details',
    '#title' => 'Table Settings',
    '#collapsible' => true,
  );

  $form['images_and_tables']['tables']['table_classes'] = array(
    '#type' => 'textfield',
    '#title' => t('Table classes'),
    '#default_value' => theme_get_setting('table_classes')
  );

  // Layout
  $form['regions'] = array(
    '#type' => 'details',
    '#title' => t('Regions'),
    '#group' => 'waterfront_basis',
    '#description' => t('Additional classes for each region')
  );

  // Regions
  foreach ($region_list as $name => $description) {
    if (!in_array($name, $exclude_regions)){
      if (theme_get_setting('region_classes_' . $name) !== null) {
        $region_class = theme_get_setting('region_classes_' . $name);
      } else {
        $region_class = '';
      }

      $form['regions'][$name] = array(
        '#type' => 'details',
        '#title' => $description,
        '#collapsible' => true,
        '#open' => false,
      );
      $form['regions'][$name]['region_classes_' . $name] = array(
        '#type' => 'textfield',
        '#title' => t('@description classes', array('@description' => $description)),
        '#default_value' => $region_class
      );
    }
  }

  // Custom  CSS
  $form['custom_css'] = array(
    '#type' => 'details',
    '#title' => t('Custom CSS'),
    '#group' => 'waterfront_basis',
  );

  $form['custom_css']['waterfront_basis_custom_css_on'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable custom CSS'),
    '#default_value' => theme_get_setting('waterfront_basis_custom_css_on'),
  );
  if (file_exists('public://waterfront_basis-custom.css'))
    $custom_file = file_get_contents('public://waterfront_basis-custom.css');
  else
    $custom_file = '';

  $form['custom_css']['waterfront_basis_custom_css'] = array(
    '#type' => 'textarea',
    '#title' => t('Custom CSS'),
    '#rows' => 14,
    '#resizable' => TRUE,
    '#default_value' => $custom_file,
    '#states' => [
      'invisible',
      'visible' => [
        'input[name="waterfront_basis_custom_css_on"]' => ['checked' => TRUE],
      ],
    ],
  );

  // Change collapsible fieldsets (now details) to default #open => FALSE.
  $form['theme_settings']['#open'] = false;
  $form['logo']['#open'] = false;
  $form['favicon']['#open'] = false;
}


/**
 * Save custom CSS to file on theme setting form submit.
 */
function waterfront_basis_form_system_theme_settings_submit(&$form, FormStateInterface $form_state) {
  file_unmanaged_save_data($form_state->getValue('waterfront_basis_custom_css'), 'public://waterfront_basis-custom.css', FILE_EXISTS_REPLACE);
}
