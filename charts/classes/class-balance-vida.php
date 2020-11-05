<?php

/**
* Return Balance de Vida...
*/

class Balance_Vida extends Charts {
    
    protected $bv_data;
    
    function __construct($post_id = NULL) {
        
        $this->bv_data = $this->bv_data($post_id);
        
    }
    
    function bv_data($post_id) {
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        $bv_data = isset( $chart_data[0]['bv_full'] ) ? $chart_data[0]['bv_full'] : '';
        
        return $bv_data;
    }
    
    
    function get_bv( $post_id ) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        for ($i = 1; $i < 10; $i++) {
            $bv[$i] = 0;
        }
        
        // Cargamos los datos del nombre completo.
        $name = $chart_data[0]['name']['data'];
        $second_name = $chart_data[0]['secondName']['data'];
        $lastName = $chart_data[0]['fatherLastname']['data'];
        
        $full_name = array_merge($name, $second_name);
        $full_name = array_merge($full_name, $lastName);
        
        foreach ($full_name as $data ) {
            foreach( $data['value'] as $string_number) {
                $number_array = explode('/', $string_number);
                $number = end($number_array);
                $bv[$number] = 1 + $bv[$number];
            }
        }
        
        // Save bv values to DB
        foreach ($chart_data as $data) {
            $data['bv_full'] = $bv;
        }
        $this->bv_data = $data['bv_full'];
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
    }
    
    function display_bv() {
        $bv_array = $this->bv_data;
        
        $html = '<h2>Balance de Vida</h2>';
        
        if ( isset( $bv_array ) && ! empty($bv_array ) ) { 

            $html .= "<table class='chart-table table'>";
            $html .= "<tr><td>1 = $bv_array[1]</td><td>4 = $bv_array[4]</td><td>7 = $bv_array[7]</td></tr>";
            $html .= "<tr><td>2 = $bv_array[2]</td><td>5 = $bv_array[5]</td><td>8 = $bv_array[8]</td></tr>";
            $html .= "<tr><td>3 = $bv_array[3]</td><td>6 = $bv_array[6]</td><td>9 = $bv_array[9]</td></tr>";
            $html .= "</table>";
            return $html;
        } else {
            return '';
        }
        
        
    }
}