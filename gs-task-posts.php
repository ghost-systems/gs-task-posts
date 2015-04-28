<?php
/*
Plugin Name: Task Posts
Description: Creates new post type for tasks.
Version: 0.0.2
Author: Ghost Systems
Author URI: https://ghost.systems/
*/

// Disallow direct access.
defined('ABSPATH') or die ('No direct access allowed.');
// Register the custom post type on initialisation.
add_action('init', 'addTaskPostType', 0);


// Create custom post type 'tasks'
function addTaskPostType() 
{
	// Set labels for Custom Post Type
	$labels = array(
		'name' => 'Tasks',
		'singular_name' => 'Task',
		'all_items' => 'All Tasks',
		'add_new_item' => 'Add New Task',
		'edit_item' => 'Edit Task',
		'new_item' => 'New Task',
		'view_item' => 'View Task',
		'search_items' => 'Search Tasks',
		'not_found' => 'No tasks found',
		'not_found_in_trash' => 'No tasks found in Trash'
	);
	
	// Set what standard data the custom post type supports
	$supports = array(
		'title', 
		'editor', 
		'revisions',
	);
	
	// Set arguments for Custom Post Type
	$arguments = array(
		'labels' => $labels,
		'description' => 'Standard journal task format.',
		'public' => true,
		'has_archive' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-welcome-write-blog',
		'hierarchical' => false,
		'supports' => $supports,
		'rewrite' => array('slug' => 'tasks'),
		//'register_meta_box_cb' => 'addCustomFields',
	);
	
	// Register Custom Post Type
	register_post_type('gs_task', $arguments);			
}





// Register custom fields to admin pages.

add_action('add_meta_boxes', 'addTaskDetails');
add_action('save_post', 'saveTaskDetails', 10, 2);

function addTaskDetails() {
	add_meta_box('task-details', 'Task Details', 'renderTaskDetailsBox', 'gs_task', 'side', 'high');
}


function renderTaskDetailsBox($object, $box) {
?>
	<div>
		<label for="gs-start-time">Start Time</label>
		<input id="gs-start-time" type="datetime-local" name="gs-start-time" value="<?php echo wp_specialchars(get_post_meta($object->ID, 'Start Time', true), 1); ?>"/>
	</div>
	<input type="hidden" name="gs-tasks-nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
<?php 
}


function saveTaskDetails($post_id, $post) {

	if (!wp_verify_nonce($_POST['gs-tasks-nonce'], plugin_basename(__FILE__))) {
		return $post_id;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	$meta_value = get_post_meta($post_id, 'Start Time', true);
	$new_meta_value = stripslashes($_POST['gs-start-time']);

	if ($new_meta_value && ('' == $meta_value)) {
		add_post_meta($post_id, 'Start Time', $new_meta_value, true);
	} elseif ($new_meta_value != $meta_value) {
		update_post_meta($post_id, 'Start Time', $new_meta_value);
	} elseif (('' == $new_meta_value) && $meta_value) {
		delete_post_meta($post_id, 'Start Time', $meta_value);
	}
}



