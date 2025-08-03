<?php
/**
 * Plugin Name: Related Posts by Tags
 * Description: Displays related posts by tags below the post content.
 * Version: 1.0
 * Author: Cryptoball cryptoball7@gmail.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Related_Posts_By_Tags {

    public function __construct() {
        add_filter( 'the_content', [ $this, 'append_related_posts' ] );
    }

    public function append_related_posts( $content ) {
        if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }

        global $post;

        $related_posts = $this->get_related_posts( $post->ID );

        if ( ! $related_posts->have_posts() ) {
            return $content;
        }

        ob_start();
        echo '<div class="related-posts-by-tags">';
        echo '<h3>' . esc_html( apply_filters( 'rpt_related_posts_title', 'Related Posts' ) ) . '</h3>';
        echo '<ul>';
        while ( $related_posts->have_posts() ) {
            $related_posts->the_post();
            echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
        wp_reset_postdata();

        $related_output = ob_get_clean();

        return $content . $related_output;
    }

    private function get_related_posts( $post_id ) {
        $tags = wp_get_post_tags( $post_id, [ 'fields' => 'ids' ] );

        if ( empty( $tags ) ) {
            return new WP_Query(); // Empty query object
        }

        $args = apply_filters( 'rpt_query_args', [
            'post__not_in'        => [ $post_id ],
            'tag__in'             => $tags,
            'posts_per_page'      => 5,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true, // performance
            'orderby'             => 'rand', // can be filtered
        ] );

        return new WP_Query( $args );
    }
}

new Related_Posts_By_Tags();
