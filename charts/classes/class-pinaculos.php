<?php
/**
* Calculo de Pinaculos.
*/

class Pinaculos extends Charts {

    function __construct() {
        parent::__construct();
    }
    
    function get_pinaculos( $post_id ) {
        
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

        $pin[1] = $this->one_digit_number( abs( $day + $month ) );
        
        $pin[2] = $this->one_digit_number( abs( $day + $year ) );
        
        $pin[3] = $this->one_digit_number( abs( $pin[1] + $pin[2] ) );
        
        $pin[4] = $this->one_digit_number( abs( $month + $year ) );
        
        // 4. Guardamos los datos en la DB
        foreach($chart_data as $data ) {
            $data['pin'] = $pin;
        }

        $metaid = update_post_meta($post_id, 'chart_numbers', $data);

    }

	function pin_double( $post_id) {

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
		
		$double = ( abs( $day + $month ) == 11 ) ? '11/' : '';
        $pin[1] = $double . $this->one_digit_number( abs( $day + $month ) );
        
		$double = ( abs( $day + $year ) == 11 ) ? '11/' : '';
        $pin[2] = $double . $this->one_digit_number( abs( $day + $year ) );
        
		$double = ( abs( $this->one_digit_number($pin[1]) + $this->one_digit_number($pin[2]) ) == 11 ) ? '11/' : '';
        $pin[3] = $double . $this->one_digit_number( abs( $this->one_digit_number($pin[1]) + $this->one_digit_number($pin[2]) ) );
        
		$double = ( abs( $month + $year ) == 11 ) ? '11/' : '';
        $pin[4] = $double . $this->one_digit_number( abs( $month + $year ) );	
	
		return $pin;
	
	}
    
    function display_pin( $post_id ) {
		
        $html = '';
        $chart_data = get_post_meta( $post_id, 'chart_numbers');
        
        /*$pin = isset( $chart_data[0]['pin'] ) ? $chart_data[0]['pin'] : array();*/
		
		$pin = 	$this->pin_double( $post_id);	

        if ( empty( $pin ) ) {
            $this->get_pinaculos( $post_id );
        }
        $lv = isset( $chart_data[0]['lv_full'] ) ?  $chart_data[0]['lv_full'] : 0;
        $lv = $this->one_digit_number($lv);
        
        $edad_1 = 36 - $lv;
        $edad_2 = $edad_1 + 9; 
        $edad_3 = $edad_2 + 9; 
        $edad_4 = $edad_3 + 9; 
        
        $html = '<h2>Pináculos</h2>';
        if ( isset( $pin[1] ) ) {

            // Primer Pinaculo
            $html .= "<h3>1° Pináculo n° $pin[1]</h3>";
            $html .= "<p>Abarca desde los 0 hasta los $edad_1 años, y dice que:</p>";
            $html .= $this->get_pin_by_number($pin[1]);
            // Segundo Pinaculo.
            $html .= "<h3>2° Pináculo n° $pin[2]</h3>";
            $html .= "<p>Abarca desde los $edad_1 hasta los $edad_2 años, y dice que:</p>";
            $html .= $this->get_pin_by_number($pin[2]);

            // Tercer Pinaculo.
            $html .= "<h3>3° Pináculo n° $pin[3]</h3>";
            $html .= "<p>Abarca desde los $edad_2 hasta los $edad_3 años, y dice que:</p>";
            $html .= $this->get_pin_by_number($pin[3]);

            // Cuarto Pinaculo.
            $html .= "<h3>4° Pináculo n° $pin[4]</h3>";
            $html .= "<p>Abarca desde los $edad_3 años al resto de tu vida, y dice que:</p>";
            $html .= $this->get_pin_by_number($pin[4]); 
            
        }

        return $html;
        
    }

    function edades_pinaculos( $chart_data = NULL, $age = NULL ) {
        $lv = isset( $chart_data[0]['lv_full'] ) ?  $chart_data[0]['lv_full'] : 0;
        $lv = $this->one_digit_number($lv);
        $out = array();
        $out[1] = 36 - $lv;
        $out[2] = $out[1] + 9; 
        $out[3] = $out[2] + 9; 
        $out[4] = $out[3] + 9;

        if ( $age < $out[1] ) {
            $pin = 1;  
        } elseif ( $out[1] <=  $age && $age < $out[2]) {
            $pin = 2;
        } elseif ( $out[2] <= $age && $age < $out[3]) {
            $pin = 3;
        } else {
            $pin = 4;
        }
        return $pin;
    }

    function get_pin_by_number( $number ) {
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
                    'value' => 'pin' 
                ), 
            )
        )); 
        
        while ( $int_query->have_posts() ) :

            $int_query->the_post();
        
            $int_ID[] = get_the_ID();
        
        endwhile;
        wp_reset_query();

        // Load Interpretation post by Id.
        if ( isset( $int_ID[0] ) ){
            $post_des = get_post( $int_ID[0] );
            $des_content = apply_filters('the_content', $post_des->post_content );
            return $des_content;
        }

        return 'Cargar Interpratcion para este numero';
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
                $sum += $val; 
            }
        }
        
        return $sum;
    }    
}
