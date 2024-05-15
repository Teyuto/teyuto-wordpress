<?php 
// teyuto register main page
add_action('admin_menu', 'teyutowp_register_library_subpage');
function teyutowp_register_library_subpage() {
  add_menu_page(
    __('Teyuto', 'Teyuto'),
    __('Teyuto', 'Teyuto'),
    'manage_options',
    'teyuto-library',
    'teyutowp_library',
    '/wp-content/plugins/teyuto/assets/teyuto-iconxs.png',
    10
  );
}

// teyuto Library page
add_action('admin_menu', 'teyutowp_library_submenu_page');
function teyutowp_library_submenu_page() {
  add_submenu_page(
    'teyuto-library',
    '',
    'Library',
    'manage_options',
    'teyuto-library',
    'teyutowp_library'
  ); 
}

// teyuto Add New page
add_action('admin_menu', 'teyutowp_register_addnew_subpage');
function teyutowp_register_addnew_subpage() {
  add_submenu_page(
    'teyuto-library',
    'Add New',
    'Add New',
    'manage_options',
    'add-new-video',
    'teyutowp_add_new_video'
  );
}

// teyuto settings page
add_action('admin_menu', 'teyutowp_register_settings_subpage');
function teyutowp_register_settings_subpage() {
  add_submenu_page(
    'teyuto-library',
    'Settings',
    'Settings',
    'manage_options',
    'settings-teyuto',
    'teyutowp_api_settings_page'
  );
}
?>
