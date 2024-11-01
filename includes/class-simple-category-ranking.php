<?php
/**
 * The Simple Category ranking.
 */
class SimpleCategoryRanking {
	/**
	 * Register widget function
	 */
	public static function register_widget() {
		register_widget( 'SCR_Widget' );
	}

    public function get_post_views($postID){
       $count_key = 'scr_views_count';
       $count = get_post_meta($postID, $count_key, true);
       if($count==''){
          delete_post_meta($postID, $count_key);
          add_post_meta($postID, $count_key, '0');
          return '0';
      }
      return $count;
  }

  public function set_post_views($postID) {
    global $post;
    if('publish' === get_post_status( $post ) 
        && is_single() && !is_preview() && !scr_is_bot()){
        $postID = $post->ID;
    $count_key = 'scr_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
      $count = 0;
      delete_post_meta($postID, $count_key);
      add_post_meta($postID, $count_key, $count);
  }else{
      $count++;
      update_post_meta($postID, $count_key, $count);
  }
}
}
}

/**
 * Simple Category Ranking widget.
 */
class SCR_Widget extends WP_Widget{

	private $defaults = array(
		'title' => '人気記事',
		'limit' => '5',
		'show_views' => false,
		'show_top' => false
	);



	/**
	 * Widget constructor
	 */
	public function __construct() {
		parent::__construct(
			'scr',
			'Simple Category Ranking',
			array(
				'classname'		=>	'simple-category-ranking',
				'description'	=>	__( 'Popular post on your blog in same category.', 'simple-category-ranking' )
			)
		);
	}

    public function get_plugin_dir($path = '') {
        return plugins_url('simple-category-ranking/' . $path);
    }


	/**
	 * widget output
	 * @param  array $args     this is output element
	 * @param  array $instance settings
	 * @return widget output html element
	 */
	public function widget($args,$instance){
        /**
         * @var string $name
         * @var string $id
         * @var string $description
         * @var string $class
         * @var string $before_widget
         * @var string $after_widget
         * @var string $before_title
         * @var string $after_title
         * @var string $widget_id
         * @var string $widget_name
         */
        extract( $args, EXTR_SKIP );

        if ( is_single() || is_category() || $instance['show_top']) {

        	echo "\n" . $before_widget . "\n";


        	if(!empty($instance['title'])) {
        		$title = apply_filters('widget_title', $instance['title'] );
        	}

        	if (!empty($title)) {
        		echo $before_title . $title . $after_title;
        	} else {
        		echo $before_title . '人気記事' . $after_title;
        	}

        	echo $this->scr_ranking_output($instance);

        	echo "\n" . $after_widget . "\n";
        }
    }


    public function scr_ranking_output($instance){

    	$args = array(
    		'meta_key'=> 'scr_views_count',
    		'orderby' => 'meta_value_num',
    		'order' => 'DESC',
    		'posts_per_page' => $instance['limit']
    	);

    	$atts = array();
    	if(is_single()){
    		$atts['cat'] = wp_get_post_terms(get_the_ID(),'category',array('fields' => 'ids'));
    	}elseif(is_category()){
    		$atts['cat'] = get_the_category()[0]->cat_ID;
    	}else{
    		$atts['cat'] = get_terms('category',array( 'fields' => 'ids'));
    	}
    	$args = wp_parse_args( $atts, $args );
    	$my_query = new WP_Query( $args );

    	if($my_query->have_posts()) : ?>

    		<ul class="scr-list">

    			<?php while ($my_query->have_posts()) : 
                    $my_query->the_post(); 
                    ?>
                    <li class="scr-list-item">

                       <div class="scr-list-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if( has_post_thumbnail() ): ?>
                              <?php the_post_thumbnail('thumbnail'); ?>
                              <?php else:
                                $noimage = $this->get_plugin_dir('images/noimage.jpg');?>
                                <img src="<?php echo $noimage ;?>">
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="scr-list-text">
                      <a href="<?php the_permalink(); ?>">
                         <?php the_title(); ?>
                     </a>
                 </div>
             </li>
         <?php endwhile; ?>
     </ul>
     <?php wp_reset_postdata();
 else:
    echo apply_filters( 'scr_no_data', "<p class=\"scr-no-data\">" . __('Sorry. No data .', 'simple-category-ranking') . "</p>" );
endif;
}



public function form($instance) {
  $instance = wp_parse_args($instance,$this->defaults);
  ?>
  <p>
     <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('タイトル','simple-category-ranking'); ?>
 </label>
 <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
 name="<?php echo $this->get_field_name('title'); ?>"
 value="<?php echo esc_attr( $instance['title'] ); ?>">
</p>
<p>
  <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('記事表示件数'); ?></label>
  <input type="text" class="widefat" id="<?php echo $this->get_field_id('limit'); ?>"
  name="<?php echo $this->get_field_name('limit'); ?>"
  value="<?php echo esc_attr( $instance['limit'] ); ?>">
</p>
<!-- <p>
  <input type="checkbox" class="checkbox" <?php echo ($instance['show_views']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" /> <label for="<?php echo $this->get_field_id( 'show_views' ); ?>"><?php _e('view数を表示', 'simple-category-ranking'); ?></label><br />
</p> -->
<p>
  <input type="checkbox" class="checkbox" <?php echo ($instance['show_top']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'show_top' ); ?>" name="<?php echo $this->get_field_name( 'show_top' ); ?>" /> <label for="<?php echo $this->get_field_id( 'show_top' ); ?>"><?php _e('TOPページにも表示', 'simple-category-ranking'); ?></label><br />
</p>
<?php
}

public function update($new_instance, $old_instance) {
   $instance = $old_instance;
   $instance['title'] = strip_tags($new_instance['title']);
   $instance['limit'] = is_numeric($new_instance['limit']) ? $new_instance['limit'] : 5;
   // $instance['show_views'] = isset( $new_instance['show_views'] );
   $instance['show_top'] = isset( $new_instance['show_top'] );
   return $instance;
}


}
