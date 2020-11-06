<?php
/**
* Calculo de Desafios.
*/

class Desafios extends Charts {

    function __construct() {
        parent::__construct();
    }
    
    function get_desafios( $post_id ) {
        
        $chart_data = get_post_meta( $post_id, 'chart_numbers');
        
        // 1. Cargamos datos sobre la fecha de nacimiento.
        $day = $chart_data[0]['birthDate']['day'];
        $month = $chart_data[0]['birthDate']['month'];
        $year = $chart_data[0]['birthDate']['year']; 
        
        // 2. Reducimos los numros a un digito.
        $day = $this->one_digit_number( $this->one_digit_converter($day) );
        $month = $this->one_digit_number( $this->one_digit_converter($month) );
        $year = $this->one_digit_number( $this->year_to_num($year) );
        
        // 3. Calculo de los desafios
        
        // 3.1 Restamos dia a mes.
        if ( is_numeric( $day ) && is_numeric( $year ) && is_numeric( $month ) ) {
            $des[1] = abs( $day - $month );
        
            $des[2] = abs( $day - $year );

            $des[3] = abs( $des[1] -$des[2] );

            $des[4] = abs( $month - $year ); 
            
        }  else {
            return;
        }

        
        if ($des[3] != $des[4]) {
            $des[5] = abs( $des[3] - $des[4] );
        }
        
        // 4. Guardamos los datos en la DB
        foreach($chart_data as $data ) {
            $data['des'] = $des;
        }
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);

        return $des;

    }
    
    function display_des( $post_id ) {

        $html = '';
        
        $chart_data = get_post_meta( $post_id, 'chart_numbers');
        
        $des = isset( $chart_data[0]['des'] ) ? $chart_data[0]['des'] : array();

        if ( ! isset( $des[1] ) && empty( $des[1] ) ) {
            $this->get_desafios( $post_id );
        }
        
        if ( isset( $des[1] ) ) {

            $html = '<h2>Desafíos</h2>';
            
            // Section Definition
            $content = new Chart_Content();
            $html = $html . $content ->get_definition_by_section('des', -1);

            //Primer Desafio.
            $html .= "<h3>1° Desafío n° $des[1]</h3>";
            $html .= "<p>Abarca desde 0 hasta los 9 años, e indica que:</p>";
            $html .= $this->get_des_by_number( $des[1], 1 );
            
            //Segundo desafío.
            $html .= "<h3>2° Desafío n° $des[2]</h3>";
            $html .= "<p>Abarca desde 9 hasta los 18 años, e indica que:</p>";
            $html .= $this->get_des_by_number($des[2], 2 );
            
            //Tercer desafío.
            $html .= "<h3>3° Desafío n°  $des[3]</h3>";
            $html .= "<p>Abarca desde 18 hasta los 27 años, e indica que:</p>";
            $html .= $this->get_des_by_number($des[3], 3 );
            
            //Cuarto y quinto desafío.
            
            if (isset($des[5]) && !empty($des[5])) {
            //cuarto desafío.
            $html .= "<h3>4° Desafío n° $des[4]</h3>";
            $html .= "<p>Abarca desde los 27 hasta los 36 años, e indica que:</p>";
            $html .= $this->get_des_by_number( $des[4], 4 );

            // quinto desafío.
            $html .= "<h3>5° Desafío n° $des[5]</h3>";
            $html .= "<p>Abarca a partir de los 36 años, e indica que:</p>";
            $html .= $this->get_des_by_number( $des[5], 5 );
            } else {
            //cuarto desafío.
            $html .= "<h3>4° Desafío n° $des[4].</h3>";
            $html .= "<p>Abarca a partir de los 27 años, e indica que:</p>";
            $html .= $this->get_des_by_number($des[4]); 
            }

        
        }

        // Removed repeated interpretation numbers from array.
        /*if ( is_array( $des ) ) {
            $des_ints = array_unique($des);

            foreach( $des_ints as $des_number ) {
                $html .= "<h3>Interpretacion para el numero $des_number</h3>"; 
                $html .= $this->get_des_by_number( $des_number );
            }
        }*/

        return $html;        
        
    }
    
    function year_to_num($year) {
        $sum = 0;
        $year_arr = str_split($year, 2);
    
        if ( $year_arr[0] == 20 ) {
            
            $year_decimal = str_split($year_arr[1]);
            
            $sum = $year_arr[0] + $year_decimal[0] * $year_decimal[1];
            
        } else {
           $year_arr = str_split($year);
            
            foreach ($year_arr as $val) {
                if ( is_numeric( $val) ) {
                    $sum += $val;
                } else {
                    $sum = $val;
                }
                 
            }
        }
        
        return $sum;
    }
    
    function get_des_by_number( $number, $des_order = NULL ) {
        $int_ID = array();
  
        $int_query = new WP_Query( array(
            'post_type' => array('interpretaciones'), 
            'meta_query' => array( 
                array( 
                    'key' => 'numero', 
                    'value' => $number 
                ), 
                array( 
                    'key' => 'tipo', 
                    'value' => 'des' 
                ), 
            )
        )); 

        $totalpost = $int_query->found_posts;        
        
        if ( $totalpost > 1 ) {
        
            return 'Mas de una interpretación para este numero. <a class="btn btn--select-inter" href="#" data-order="'. $des_order .'" data-number="'. $number .'" data-tipo="des">Seleccionar Interpretacion</a>';
        } else {
            while ( $int_query->have_posts() ) :

                $int_query->the_post();
            
                $int_ID[] = get_the_ID();
            
            endwhile;
            
    
            // Load Interpretation post by Id.
            if ( isset( $int_ID[0] ) ){
                $post_des = get_post( $int_ID[0] );
                //$des_content = apply_filters('the_content', $post_des->post_content );
                $des_content = $post_des->post_content;
                return apply_filters( 'the_content', $des_content );
            }
            wp_reset_query();
            return '<a href="#" class="btn add-inter" data-order="'. $des_order .'" data-tipo="des" data-number="'. $number .'" >Cargar</a> Interpretacion para este numero';
        }

    }    
}