<?php

/**
* Prepares the content for the chart.
*/

class Chart_Content {
    
    public $post_id;
    
    function __construct() {
        
        $this->post_id = '';
        
    }
    
    function chart_content_filter() {
        add_filter('the_content', array($this, 'chart_content'));
    }
    
    function get_post_id() {
        return $this->post_id;
    }
    
    function chart_content($content) {
        
        global $post;
        
        if ( $post->post_type == 'interpretaciones' && is_main_query() ) {
    
            //return $post->ID;
        }
            
        if ( $post->post_type == 'carta' && is_main_query() ) {
            
            if(function_exists('pf_show_link')){
                //echo pf_show_link();
            }
            
            if ($content != '') {
                return $content;
            }
            
            $this->post_id = $post->ID;
            
            $content = $this->save_chart_content($post->ID, $content);
            return $content;
            
        }

        if ( $post->post_type == 'pronostico' && is_main_query() ) {
            if ( $content != '') {
                return $content;
            }
            $pro = new Pronostico_Anual();
            return $pro->pronostico_anual_content( $post->ID );
        }
        
        return $content;
    }
    
    /**
    * Main Content for the chart..
    */
    function get_main_content($chart_numbers) {
        
        $na_full = isset( $chart_numbers[0]['na_full'] ) ? $chart_numbers[0]['na_full'] : '';
        $pe_full = isset( $chart_numbers[0]['pe_full'] ) ? $chart_numbers[0]['pe_full'] : '';
        $vd_full = isset( $chart_numbers[0]['vd_full'] ) ? $chart_numbers[0]['vd_full'] : '';
        $lv_full = isset( $chart_numbers[0]['lv_full'] ) ? $chart_numbers[0]['lv_full'] : '';
        $nf_full = isset( $chart_numbers[0]['nf_full'] ) ? $chart_numbers[0]['nf_full'] : '';
        
        $html = '<h2 class="chart-title">';
        $html .= $chart_numbers[0]['name']['string'];
        $html .= ' '. $chart_numbers[0]['secondName']['string'];
        $html .= ' '. $chart_numbers[0]['fatherLastname']['string'];
        $html .= '</h2>';
        
        $html .= '<h3>Fecha de Nacimiento: ';
        $html .= $chart_numbers[0]['birthDate']['day'];
        $html .= ' - '. $chart_numbers[0]['birthDate']['month'];
        $html .= ' - '. $chart_numbers[0]['birthDate']['year'];
        $html .= '</h3>';
        
        $html .= "<table class='numbers-table'>";
        $html .= "<tr>";
        $html .= "<td>$na_full N.A.</td>";
        $html .= "<td>$pe_full P.E.</td>";
        $html .= "<td>$vd_full P.E.</td>";
        $html .= "<td>$lv_full L.V.</td>";
        $html .= "<td>$nf_full N.F.</td>";
        $html .= "</tr>";
        $html .= "</table>";
        
        return $html;
    }
    
    /**
    *
    */
    function load_chart_section($post_id, $section, $chart_numbers, $content ) {
        
        // Load Saved content data
        $chart_content = get_post_meta($post_id, 'chart_content');
        
        $section_number = isset( $chart_numbers[0][$section .'_full'] ) ? $chart_numbers[0][$section .'_full'] : '';
        
        // Section Title
        $content = $content . $this->get_section_title($section, $section_number);
        
        // Section Definition
        $content = $content . $this->get_definition_by_section($section, 0);
        
        if ( isset( $chart_content[0][$section] ) ) {
            $content_post = get_post($chart_content[0][$section]);
            $content = $content . $content_post->post_content;
        } else {
            $content = $content . $this->get_int_by_number($section_number, $section, $post_id);
        }  
        
        return $content;
    }
    
    /**
    * TODO: this function dont work when called from
    * section class, fix that later.
    */
    function save_int_id_for_section( $post_id, $section, $number ) {
        
        $meta_id = '';
        
        $counter = $this->interpretation_counter($number); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            
            foreach($chart_content as $content) {
                
                $content['na_full'] = $this->get_interpretation_id($total);
                
            }
            
            $meta_id = update_post_meta($post_id, 'chart_content', $content);            
            
        }  
        
        return $meta_id;
    }
    
    /**
    *
    */
    function interpretation_counter($number) {
        
        $int_query = new WP_Query( array(
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                array( 
                    'key' => 'numero', 
                    'value' => $number 
                ) 
            )
        )); 

        $counter=  $int_query->post_count; 
        
        wp_reset_query();
        
        return $counter;
    }

    function get_interpretation_by_ID( $post_id ) {

        $post = get_post( $post_id );
        
        return $post->post_content;        
    }
    
    /**
    *
    */
    function get_interpretation_id($number) {
        
        $int_ID = '';
  
        $int_query = new WP_Query( array(
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                array( 
                    'key' => 'numero', 
                    'value' => $number 
                ) 
            )
        )); 
        
        while ( $int_query->have_posts() ) :

            $int_query->the_post();
        
            $int_ID = get_the_ID();
        
        endwhile;
        wp_reset_query();
        
        return $int_ID;
    }
    
    /**
    * Carga interpretacion por el field numero
    */
    function get_int_by_number($number, $section, $post_id) {
        
        $links = '';
        $meta_value = '';
        $data = array();
        $content = '';
        $body = '';
    
        $int_query = new WP_Query( array(
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                array( 
                    'key' => 'numero', 
                    'value' => $number 
                ) 
            )
        ));


        while ( $int_query->have_posts() ) :

        $int_query->the_post();

        $counter=  $int_query->post_count; 

        if ( $counter == 1 ) {

            $body .= get_the_content();
            $content .= apply_filters('the_content', $body);


        } else {
            /* list of links */

            $links .= '<li><span class="button button-hollow button-section-content" data-section="'. $section .'" data-id="'. get_the_ID() .'">'. get_the_title() .'</span></li>';


        }


        endwhile;

        if( $links ) {
            $content .= '<ul class="simple-list int-list">';
            $content .= $links;
            $content .= '</ul>';
        }



            wp_reset_query();



        return $content;
    }

    public function get_inter_options( $section = NULL, $number = NULL ) {
        
    } 

    function get_definition_by_section_options( $section, $number ) {

        $def_query = new WP_Query( array(
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                'relation' => 'AND',
                array( 
                    'key' => 'tipo', 
                    'value' => $section 
                ),
                array(
                    'key' => 'numero',
                    'value' => $number,
                ), 
            )
        )); 
        $options = '';
        ob_start();
        ?>
        <select class="select-inter">
        <option value="">Elegir Interpretación</option>
        <?php  
              
        while ( $def_query->have_posts() ) {
            $def_query->the_post();
            ?>
            <option class='inter-option' value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
            <?php
        }
        wp_reset_query();

        ?>
        </select>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Given a section and number search for definition.
     * @param $order: specify the sub-section value, if it is present.
     */
    function get_definition_by_section( $section = NULL , $number = NULL, $order = NULL ) {
        
        $def= '';
        
        $def_query = new WP_Query( array (
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                'relation' => 'AND',
                array( 
                    'key' => 'tipo', 
                    'value' => $section ? $section : 'na', // By defect we use na type.
                ),
                array(
                    'key' => 'numero',
                    'value' => $number ? $number : 0, // By defautl we usr the 0 value( type definition).
                ), 
            )
        ));

        $totalpost = $def_query->found_posts;

        if ( $totalpost > 1) {
            /*$pro = new Pronostico_Anual();
            $inter_id = $pro->get_pro_section_value( 847, 'cua', 5, 'firstq' );
            if ( $inter_id ) {
                $inter_post = get_post( $inter_id );
                return $inter_post->post_content;
            } else {*/
                return 'Mas de una interpretación para este numero. <a class="btn btn--select-inter" href="#"  data-order="'. $order .'" data-number="'. $number .'" data-tipo="'. $section .'">Seleccionar Interpretacion</a>';
            /*}*/
            
        } else {
            while ( $def_query->have_posts() ) :
        
                $def_query->the_post();
   
                $def = get_the_content();
                $def = apply_filters('the_content', $def);  
            
            endwhile;
            wp_reset_query();
            
            return $def ? $def : 'Se Necesita <a class="btn add-inter" data-order="'. $order .'" data-tipo="'. $section .'" data-number="'. $number .'" href="#">crear</a> la Interpretacion para este numero y sección.';
        }

    }
    
    function get_section_title($section, $number) {
        $content = '';
        if ($section == 'na') {
         $content .= '<h2>Número del Alma '. $number .'</h2>';
        } else if($section == 'lv') {
          $content .= '<h2>Lección de Vida '. $number .'</h2>';  
        } else if($section == 'pe') {
          $content .= '<h2>Personalidad Externa '. $number .'</h2>';  
        } else if($section == 'nf') {
          $content .= '<h2>Número de Fuerza '. $number .'</h2>';  
        } else if($section == 'vd') {
          $content .= '<h2>vía del Destino '. $number .'</h2>';  
        }        
        
        return $content;
    }
    
    function save_chart_content($post_id, $content) {
        
        $chart_numbers = get_post_meta($post_id, 'chart_numbers');

        $chart_content = get_post_meta($post_id, 'chart_content');
        
        // Caratula
        $content = $this->get_definition_by_section('car', 0);

        // Carta Intro
        $content = $content . $this->get_main_content($chart_numbers);

        // Numero del Alma content.
        $content = $this->load_chart_section($post_id, 'na', $chart_numbers, $content );

        // Leccion de Vida
        $content = $this->load_chart_section($post_id, 'lv', $chart_numbers, $content );

        // Personalidad Externa
        $content = $this->load_chart_section($post_id, 'pe', $chart_numbers, $content );

        // Numero de Fuerza
        $content = $this->load_chart_section($post_id, 'nf', $chart_numbers, $content );

        // Vía del Destino, new code testing it
        $content = $this->load_chart_section($post_id, 'vd', $chart_numbers, $content );

        // Balance de Vida
        $chart_bv = new Balance_Vida($post_id);
        $content = $content . $chart_bv->display_bv();

        // Desafios
        $chart_des = new Desafios();
        $content = $content . $chart_des->display_des( $post_id );
        

        // Pinaculos
        $chart_pin = new Pinaculos();
        $content = $content . $chart_pin->display_pin( $post_id );

        // Edad Mas Importante
        $emi = new Edad_Mas_Importante();
        $content = $content . $emi->get_emi_display( $post_id );
        
        // La 1° Vocal del Nombre
        $misc = new Misc_Numbers( $post_id );
        $content = $content . $misc->get_first_vocal_display();
        
        // Numerología Sexual
        $content = $content . $misc->get_sex_num_display();
        
        // Numerología Astrologica
        $content = $content . $misc->get_astrological_num_display( $post_id );
        
        // Natalicio
        $content = $content . $misc->get_nat_num_display();
        
        // Pie
        $content = $content . $this->get_definition_by_section('pie', 0);
        
        //Load filetr tags
        $chart = new Charts();
        
        $content = $chart->tag_replace( $content, $post_id );


        return $content;        
    }
}