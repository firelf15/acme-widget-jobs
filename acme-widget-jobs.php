<?php
/*
Plugin Name: Acme Recent Jobs Widget
Version: 0.1-alpha
Description: Creates a widget to display the 5 most recent Jobs
Author: Metal Toad Media
Author URI: http://www.metaltoad.com
Plugin URI: https://github.com/metaltoad/redacted
Text Domain: acme-widget-jobs
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Adds ACME_Recent_Jobs_Widget widget.
 */
class ACME_Recent_Jobs_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'acme_recent_jobs_widget', // Base ID
      __( 'ACME Recent Jobs', 'text_domain' ), // Name
      array( 'description' => __( 'ACME Recent Jobs Widget', 'text_domain' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    $hide_recent_jobs = array( '/job-board/', '/add-new-job/' );
    if ( in_array( $_SERVER["REQUEST_URI"], $hide_recent_jobs ) ) {
      // do nothing
    } else {
      echo $args['before_widget'];
      if ( ! empty( $instance['title'] ) ) {
        echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
      }
// WP_Query
      $acme_recent_jobs = new WP_Query();
      $recent_jobs      = array(
        'post_type'  => 'ext_job',
        'show_posts' => 5,
      );
      $acme_recent_jobs->query( $recent_jobs );
      if ( $acme_recent_jobs->have_posts() ):
        ?>
        <ul>
          <?php $i = 1;
          while ( $i < 6 && $acme_recent_jobs->have_posts() ):
            $acme_recent_jobs->the_post(); ?>
            <li><a
                href="<?php the_permalink(); ?>"><?php the_title(); ?></a>, <?php the_field( 'acf_employer_name' ); ?>
            </li>
            <?php $i ++;
          endwhile; ?>
        </ul>
      <?php endif;
      // Restore original Post Data
      wp_reset_postdata();
      echo $args['after_widget'];
    }
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    ?>
    <p>
      <label
        for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
             name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
             value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public
  function update(
    $new_instance, $old_instance
  ) {
    $instance          = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
  }

} // class ACME_Recent_Jobs_Widget

// register ACME_Recent_Jobs_Widget widget
function register_acme_recent_jobs_widget() {
  register_widget( 'ACME_Recent_Jobs_Widget' );
}

add_action( 'widgets_init', 'register_acme_recent_jobs_widget' );
