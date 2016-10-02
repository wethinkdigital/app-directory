<?php
function post_template_meta_box($post) {
  if ( 'systemplate' == $post->post_type && 0 != count( get_post_templates() ) ) {
    $template = get_post_meta($post->ID,'_post_template',true);
    ?>
<label class="screen-reader-text" for="post_template"><?php _e('Post Template') ?></label><select name="post_template" id="post_template">
<option value='default'><?php _e('Default Template'); ?></option>
<?php post_template_dropdown($template); ?>
</select>
<?php
  } ?>
<?php
}

add_action('add_meta_boxes','add_post_template_metabox');
function add_post_template_metabox() {
    add_meta_box('postparentdiv', __('Template'), 'post_template_meta_box', 'systemplate', 'side', 'core');
}

function get_post_templates() {
  $themes = get_themes();
  $theme = get_current_theme();
  $templates = $themes[$theme]['Template Files'];
  $post_templates = array();

  if ( is_array( $templates ) ) {
    $base = array( trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()) );

    foreach ( $templates as $template ) {
      $basename = str_replace($base, '', $template);
      if ($basename != 'functions.php') {
        // don't allow template files in subdirectories
        if ( false !== strpos($basename, '/') )
          continue;

        $template_data = implode( '', file( $template ));

        $name = '';
        if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) )
          $name = _cleanup_header_comment($name[1]);

        if ( !empty( $name ) ) {
          $post_templates[trim( $name )] = $basename;
        }
      }
    }
  }

  return $post_templates;
}

function post_template_dropdown( $default = '' ) {
  $templates = get_post_templates();
  ksort( $templates );
  foreach (array_keys( $templates ) as $template )
    : if ( $default == $templates[$template] )
      $selected = " selected='selected'";
    else
      $selected = '';
  echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
  endforeach;
}

add_action('save_post','save_post_template',10,2);
function save_post_template($post_id,$post) {
  if ($post->post_type=='systemplate' && !empty($_POST['post_template']))
    update_post_meta($post->ID,'_post_template',$_POST['post_template']);
}

add_filter('template_include','get_post_template_for_template_loader');
function get_post_template_for_template_loader($template) {
  global $wp_query;
  $post = $wp_query->get_queried_object();
  if ($post) {
    $post_template = get_post_meta($post->ID,'_post_template',true);
    if (!empty($post_template) && $post_template!='default')
      $template = get_template_directory() . "/{$post_template}";
  }
  return $template;
}