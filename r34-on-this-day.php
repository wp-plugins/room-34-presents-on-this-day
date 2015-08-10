<?php
/*
Plugin Name: Room 34 presents On This Day
Plugin URI: http://blog.room34.com/archives/4841
Description: A very simple widget that displays a list of blog posts that were published on the same date in previous years. Title and "no posts" message are customizable.
Version: 1.3
Author: Scott Anderson / Room 34 Creative Services, LLC
Author URI: http://room34.com
License: GPL2
*/

/*  Copyright 2012-2015 Room 34 Creative Services, LLC (email: info@room34.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class R34OnThisDay extends WP_Widget {

	var $default_title = 'On This Day';
	var $default_no_posts_message = 'Nothing has ever happened on this day. <em>Ever.</em>';

	function R34OnThisDay() {
		parent::__construct('r34otd', 'On This Day');
	}
	
	function widget($args, $instance) {
		extract($args);

		// Set title
		if (empty($instance['title'])) {
			$instance['title'] = $this->default_title;
		}
		
		// Set no posts message
		if (empty($instance['no_posts_message'])) {
			$instance['no_posts_message'] = $this->default_no_posts_message;
		}

		// Get historical posts
		$now = time() + (get_option('gmt_offset') * 60 * 60);
		$historic = new WP_Query;
		$historic->init();
		$historic->parse_query('monthnum=' . date('n',$now) . '&day=' . date('j',$now));
		$historic_posts = $historic->get_posts();
		$no_history = true;

		// Build widget display
		echo $before_widget;

		// Widget title
		echo $before_title . $instance["title"] . $after_title;
		?>
		<ul class="r34otd">
			<?php
			if (count($historic_posts)) {
				foreach ($historic_posts as $hpost) {
					// Skip this year's posts
					if (substr($hpost->post_date,0,4) == date('Y')) { continue; }
					$no_history = false;
					?>
					<li>
						<div class="r34otd-headline"><a href="<?php echo get_permalink($hpost->ID); ?>" rel="bookmark"><?php echo $hpost->post_title; ?></a></div>
						<div class="r34otd-dateline"><?php echo date(get_option('date_format'),strtotime($hpost->post_date)); ?></div>
					</li>							
					<?php
				}
			}
			if ($no_history) {
				echo '<li>' . $instance['no_posts_message'] . '</li>';
			}
			?>
		</ul>
		<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	
	function form($instance) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>">
				<?php _e( 'Title' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" /><br />
				<small>Defaults to "<?php echo $this->default_title; ?>"</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("no_posts_message"); ?>">
				<?php _e( 'No Posts Message' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("no_posts_message"); ?>" name="<?php echo $this->get_field_name("no_posts_message"); ?>" type="text" value="<?php echo esc_attr($instance["no_posts_message"]); ?>" /><br />
				<small>Message to display if no posts are found.<br />
				Defaults to "<?php echo $this->default_no_posts_message; ?>"</small>
			</label>
		</p>
		<?php
	}

}

add_action('widgets_init', create_function('', 'return register_widget("R34OnThisDay");'));

?>