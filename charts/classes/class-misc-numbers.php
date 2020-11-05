<?php

/**
* Coleccion de numeros que requieren un calculo mínimo.
*
*/

class Misc_Numbers extends Charts {

    protected $post_id;    
    
    function __construct($post_id = NULL) {
        $this->post_id = $post_id;
    }
    
    function get_chart_data() {
        
        $postID = $this->post_id;
        $chart_data = get_post_meta($postID, 'chart_numbers');
        
        return $chart_data;
    }
    
    function get_first_vocal() {
       
        $postID = $this->post_id;
        
        $chart_data = get_post_meta($postID, 'chart_numbers');
        
        $nombre = $chart_data[0]['name']['data'];
        
        foreach($nombre as $key => $letter) {
            if ($letter['type'] == 'vocal') {
                $first_vocal = ucfirst( $letter['letter'] );
                break;
            }
        }
        
        foreach ( $chart_data as $data ) {
            $data['firstVocal'] = $first_vocal;
        }  
        
        $metaid = update_post_meta($this->post_id, 'chart_numbers', $data);
        
        // Carga ID of post interpretacion
        
        // Carga la interpretacion
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($first_vocal);
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($postID, 'chart_content');
            foreach( $chart_content as $content ) {
                if ( is_array( $content ) ) {
                    $content["firstVocal"] = $interpretation->get_interpretation_id($first_vocal);
                }
            }
            if ( $content ) {
                update_post_meta($postID, 'chart_content', $content);
            }
                        
         }
    
        return $first_vocal;
    }
    
    function get_first_vocal_display() {
        
        $post_id = $this->post_id;

        $this->get_first_vocal();
        
        $chart_data = $this->get_chart_data();
        
        $chart_content = get_post_meta($post_id, 'chart_content');
        
        $first_vocal = $chart_data[0]['firstVocal'];
        
        // Add Definicion content
        $interpretacion = new Chart_Content();
        $def = $interpretacion->get_definition_by_section('firstVocal', 0);
        $def = $this->tag_replace( $def, $post_id );
        
       
        
        // Add significado content
        if ( isset( $chart_content[0]['firstVocal'] ) ) {
            $content_post = get_post($chart_content[0]['firstVocal']);
            $sig = $content_post->post_content;
        } else {
            $sig = $interpretacion->get_int_by_number($first_vocal, 'firstVocal', $post_id);
        }
        
            
        return "<h2>Tu Primera Vocal es: $first_vocal</h2>" .   $def . $sig;
    }
    
    function get_nat_num_display() {
        
        $post_id = $this->post_id;
        
        $chart_data = $this->get_chart_data();
        
        $chart_content = get_post_meta($post_id, 'chart_content');
        
        $nat = isset( $chart_data[0]['nat'] ) ? $chart_data[0]['nat'] : '';
        
        $nat = intval($nat); 
        
        // Add Definicion content.
        $interpretacion = new Chart_Content();
        $def = $interpretacion->get_definition_by_section('nat', 0);
        $def = $this->tag_replace( $def, $post_id );
        
        // Add significado content
        if ( isset( $chart_content[0]['nat'] ) ) {

            $content_post = get_post($chart_content[0]['nat']);
            $sig = $content_post->post_content;
        } else {

            $sig = $interpretacion->get_int_by_number($nat, 'nat', $post_id);
        }
        
        
        return "<h2>Tu Natalicio es: $nat</h2>" .   $def . $sig;
    }
    
    function get_sex_num($post_id = '') {
        
        if(!isset($post_id)) {
            $post_id = $this->post_id;
        }

        $chart_data = $this->get_chart_data();
        
        $birthDate = $chart_data[0]['birthDate'];
        
        $day = $this->one_digit_converter( $birthDate['day'] );
        $day = $this->one_digit_number($day);
        
        // Numerologia Astrologica, dia de nacimiento reducido a un numero.
        foreach ($chart_data as $data) {
            $data['numAstr'] = $day;
        }
        
        $month = $this->one_digit_converter($birthDate['month']);
        $month = $this->one_digit_number($month);
        
        $year = $this->one_digit_converter($this->year_to_num($birthDate['year']) );
        
        $year = $this->one_digit_number($year);
        
        
        $sex_num = $day + $month + $year;
        $sex_num = $this->one_digit_converter($sex_num);
        $sex_num = $this->one_digit_number($sex_num);
        
        $data['sex_num'] = $sex_num;
        
        $metaid = update_post_meta($this->post_id, 'chart_numbers', $data);
        
        // Carga la interpretacion
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($sex_num);
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['sex_num'] = $interpretation->get_interpretation_id($sex_num);
            }
            update_post_meta($post_id, 'chart_content', $content);            
         }
        
        return $sex_num;
    }
    
    function get_sex_num_display() {
        $post_id = $this->post_id;

        $this->get_sex_num();
        
                
        $chart_data = $this->get_chart_data();
        
        $chart_numbers = get_post_meta($post_id, 'chart_numbers');
        
        $chart_content = get_post_meta($post_id, 'chart_content');


        $sex_num = $chart_data[0]['sex_num'];
        
        // Add Definicion content
        $interpretacion = new Chart_Content();
        $def = $interpretacion->get_definition_by_section('sex_num', 0);
        $def = $this->tag_replace( $def, $post_id );
        
       
        
        // Add significado content
        
        /*$sig = $interpretacion->get_int_by_number($sex_num, 'sex_num', $post_id);*/
        
        
        if ( isset( $chart_content[0]['sex_num'] ) ) {
            $content_post = get_post($chart_content[0]['sex_num']);
            $sig = $content_post->post_content;
        } else {
            $sig = $interpretacion->get_int_by_number($sex_num, 'sex_num', $post_id);
        }
        
        $html = "<h2>Numerología Sexual</h2>";
        
        return $html . $def . $sig;
    }
    
    function get_astrological_num_display( $post_id ) {
        
        $chart_data = $this->get_chart_data();
        $chart_content = get_post_meta($post_id, 'chart_content');
        
        $num_astr = $chart_data[0]['numAstr'];
        
        $astro = $this->num_to_astro($num_astr);
        
        // Add Definicion content
        $interpretacion = new Chart_Content();
        $def = $interpretacion->get_definition_by_section('numAstr', 0);
        $def = $this->tag_replace( $def, $post_id );
           
        // Add significado content
        if ( isset( $chart_content[0]['numAstr'] ) ) {
            $content_post = get_post($chart_content[0]['numAstr']);
            $sig = $content_post->post_content;
        } else {
            //$sig = $interpretacion->get_int_by_number($num_astr, 'numAstr', $post_id);
            $sig = $interpretacion->get_definition_by_section('numAstr', $num_astr);
        }        
        
        $html = "<h2>Numerología Astrológica $num_astr - $astro</h2>";
        
        return $html . $def . $sig;
    }
    
    /**
    * TODO: this function is repeated, centarlized! 
    *hjksjkss
    */
    function year_to_num($year) {
        $sum = 0;
        
        $year_arr = str_split($year, 2);
    
        if ( $year_arr[0] == 20 ) {
            
            $year_decimal = str_split($year_arr[1]);
            
            $sum = $year_arr[0] + $year_decimal[0] + $year_decimal[1];
            
        } else {
           $year_arr = str_split($year);
            
            foreach ($year_arr as $val) {
                $sum += $val; 
            }
        }
        
        return $sum;
    }
}