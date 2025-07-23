<?php
// Series grid shortcode - displays all series in a grid format
function series_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'columns' => '3',
        'orderby' => 'name',
        'order' => 'ASC',
    ), $atts, 'series_grid');
    
    $series_terms = get_terms(array(
        'taxonomy' => 'series',
        'hide_empty' => true,
        'orderby' => $atts['orderby'],
        'order' => $atts['order']
    ));
    
    $output = '';
    
    if (!empty($series_terms) && !is_wp_error($series_terms)) {
        // Use CSS Grid with custom properties for responsive columns
        $output .= '<div class="series-grid">';
        
        foreach ($series_terms as $series) {
            // Check ACF field for title in image
            $series_title_in_image = get_field('series_title_in_image', 'series_' . $series->term_id);
            
            // Series image
            $series_image_id = get_term_meta($series->term_id, 'series_image', true);
            $has_image = false;
            
            if ($series_image_id) {
                $series_image_url = wp_get_attachment_image_url($series_image_id, 'medium');
                if ($series_image_url) {
                    $has_image = true;
                }
            }
            
            // Determine card class based on image and title state
            $card_class = 'kcg-card';
            if ($has_image && $series_title_in_image) {
                $card_class .= ' image-only';
            } elseif ($has_image && !$series_title_in_image) {
                $card_class .= ' image-with-overlay';
            } else {
                $card_class .= ' no-image';
            }
            
            $output .= '<article class="' . $card_class . '">';
            $output .= '<a href="' . esc_url(get_term_link($series)) . '">';
            
            // Add image if available
            if ($has_image) {
                $output .= '<img src="' . esc_url($series_image_url) . '" alt="' . esc_attr($series->name) . '" class="series-image" />';
            }
            
            // Add title overlay or content based on state
            if ($has_image && !$series_title_in_image) {
                // Image with light overlay and title
                $output .= '<div class="series-overlay">';
                $output .= '<h3 class="series-title">' . esc_html($series->name) . '</h3>';
                $output .= '</div>';
            } elseif (!$has_image) {
                // Normal card content with title
                $output .= '<div class="series-content">';
                $output .= '<h3 class="series-title">' . esc_html($series->name) . '</h3>';
                $output .= '</div>';
            }
            // If image-only (has_image && series_title_in_image), show nothing extra
            
            $output .= '</a>';
            $output .= '</article>';
        }
        
        $output .= '</div>';
    } else {
        $output .= '<p class="series-grid-empty">No series found.</p>';
    }
    
    return $output;
}
add_shortcode('series_grid', 'series_grid_shortcode');
?>
