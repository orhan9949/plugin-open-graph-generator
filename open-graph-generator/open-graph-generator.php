<?php
/*
Plugin Name: Open Graph Generator
Plugin URI: ----
Author: Orkhan Chichitov
Author URI: ----
Description: Open Graph Generator Plugin for WordPress
Version: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Open_Graph_Generator {

    /**
     * Constructor.
     */
    public function __construct() {

        add_action( 'save_post_cryptocurrency', [ $this, 'generate_image_cryptocurrency' ], 5, 2 );

        add_action( 'saved_post_tag', [ $this, 'generate_image_post_tag' ], 1, 4 );

        add_filter( 'rank_math/opengraph/twitter/image', [ $this, 'rank_math_opengraph_image' ] );

        add_filter( 'rank_math/opengraph/facebook/image', [ $this, 'rank_math_opengraph_image' ] );

    }

    /**
     * Check page cryptocurrency
     *
     * @param $post
     * @return string|void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function check_page_cryptocurrency( $post ) {

        $rank_math_facebook_image_id = get_post_meta( $post->ID,'rank_math_facebook_image_id', true );

        if ( $rank_math_facebook_image_id ) return false;

        if ( get_the_post_thumbnail_url() ) return false;

        if ( !file_exists( $this->check_save_img_path( $post->ID, $post->post_type ) ) ) {

            $this->generate_image_cryptocurrency( $post->ID, $post );

        }

        return $this->check_save_img_path( $post->ID, $post->post_type );

    }

    /**
     * Check page post tag
     *
     * @param $term_id
     * @return string|void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function check_page_post_tag( $term_id ) {

        $rank_math_facebook_image_id = get_term_meta( $term_id,'rank_math_facebook_image_id', true );

        if ( $rank_math_facebook_image_id ) return false;

        if ( !file_exists( $this->check_save_img_path( $term_id, 'post_tag') ) ) {

            $this->generate_image_post_tag( $term_id );

        }

        return $this->check_save_img_path( $term_id, 'post_tag' );

    }

    /**
     * Rank math opengraph image
     *
     * @param $image
     * @return mixed|string|void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function rank_math_opengraph_image( $image ) {

        global $post;

        if( is_singular( 'cryptocurrency' ) ) {

            $image = $this->check_page_cryptocurrency( $post ) ?? $image;

        }

        if( is_tag() ) {

            global $term_id;

            $image = $this->check_page_post_tag( $term_id ) ?? $image;

        }

        return $image;

    }

    /**
     * Generate save img path
     *
     * @param $id
     * @param $type
     * @return string
     */
    public function generate_save_img_path( $id, $type ) {

        $upload_dir = wp_upload_dir();

        return sprintf( '%s/open-graph/%s-%s.webp', $upload_dir['basedir'], $type, $id );

    }

    /**
     * Check save img path
     *
     * @param $id
     * @param $type
     * @return string
     */
    public function check_save_img_path( $id, $type ) {

        $upload_dir = wp_upload_dir();

        return sprintf( '%s/open-graph/%s-%s.webp', $upload_dir['baseurl'], $type, $id );

    }

    /**
     * Allow picture overwrite
     *
     * @param $image
     * @return bool
     */
    public function allow_picture_overwrite( $image ) {

        $file_time_live = time() - filemtime( $image );

        if ( $file_time_live > 60 ) return true;

        return false;

    }
    /**
     * Generate image post tag
     *
     * @param $term_id
     * @param $tt_id
     * @param $update
     * @param $args
     * @return void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function generate_image_post_tag( $term_id, $tt_id = '', $update = '', $args = '' ) {

        $allow_picture_overwrite = $this->allow_picture_overwrite( $this->generate_save_img_path( $term_id,'post_tag' ) );

        if ( !$allow_picture_overwrite ) return;

        $term_data = get_term_by( 'id', $term_id, 'post_tag',ARRAY_A );

        $data = [
            'image'         => sprintf( "%simages/og-tag.png", plugin_dir_path( __FILE__ ) ),
            'title'         => sprintf( "%s\nnews", $term_data["name"] ),
            'save_img_path' => $this->generate_save_img_path( $term_id,'post_tag' )
        ];

        $this->generate_image( $data );

    }

    /**
     * Generate image cryptocurrency
     *
     * @param $post_id
     * @param $post
     * @return void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function generate_image_cryptocurrency( $post_id, $post ) {

        $allow_picture_overwrite = $this->allow_picture_overwrite( $this->generate_save_img_path( $post_id, $post->post_type ) );

        if ( !$allow_picture_overwrite ) return;

        $data = [
            'image'         => sprintf( "%simages/og-cryptocurrency.png", plugin_dir_path( __FILE__ ) ),
            'title'         => sprintf( "%s\nprice", $post->post_title ),
            'save_img_path' => $this->generate_save_img_path( $post_id, $post->post_type )
        ];

        $this->generate_image( $data );

    }

    /**
     * Generate image
     *
     * @param $data
     * @return void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function generate_image( $data ) {

        $width  = 1200;

        $height = 630;

        $setFontSize = 92;

        if ( mb_strlen( $data['title'] ) > 19 ){

            $data['title'] = str_replace( ["\r", "\n"], ' ', $data['title'] );

            $setFontSize = $setFontSize - ( mb_strlen( $data['title'] ) - 10 );

        }

        $image = new Imagick( $data['image'] );

        $draw = new ImagickDraw();

        $draw->setFont( plugin_dir_path( __FILE__ ).'fonts/TTHoves-Bold.ttf' );

        $draw->setFontSize( $setFontSize );

        $draw->setFillColor( new ImagickPixel( 'white' ) );

        $draw->setTextInterlineSpacing( 4 );

        $text = mb_strtolower( $data['title'] );

        list( $wrapped_text, $text_height ) = $this->word_wrap_imagick( $image, $draw, $text, ( $width / 2 ) - 70 );

        $draw->annotation( 64, ( $height / 2 ) - ( $text_height / 4 ), $wrapped_text );

        $image->drawImage( $draw );

        $image->resizeImage( $width, $height, Imagick::FILTER_LANCZOS, 1 );

        $image->setImageFormat( 'webp' );

        $image->setImageCompressionQuality( 99 );

        $image->writeImage( $data['save_img_path'] );

        $image->clear();

        $image->destroy();

    }

    /**
     * Word wrap imagick
     *
     * @param $imagick
     * @param $draw
     * @param $text
     * @param $max_width
     * @return array
     */
    public function word_wrap_imagick( $imagick, $draw, $text, $max_width ) {

        $words = explode( " ", $text );

        $lines = [];

        $line = "";

        $metrics = $imagick->queryFontMetrics( $draw, "A" );

        $line_height = $metrics['textHeight'];

        foreach ( $words as $word ) {

            $test_line = $line . ( empty( $line ) ? "" : " " ) . $word;

            $metrics = $imagick->queryFontMetrics( $draw, $test_line );

            if ( $metrics['textWidth'] > $max_width && !empty( $line ) ) {

                $lines[] = $line;

                $line = $word;

            } else {

                $line = $test_line;

            }
        }

        $lines[] = $line;

        $wrapped_text = implode( "\n", $lines );

        $text_height = count( $lines ) * $line_height;

        return [ $wrapped_text, $text_height ];

    }

}

new Open_Graph_Generator;

