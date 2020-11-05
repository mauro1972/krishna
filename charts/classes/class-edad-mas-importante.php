<?php
/**
* Funciones relacionadas a La edad mas iportante.
*/

class Edad_Mas_Importante extends Charts {
    
    function __construct() {
        
    }
    
    function get_emi($post_id) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        $name = strlen( $chart_data[0]['name']['string'] );
        $second_name = strlen( $chart_data[0]['secondName']['string'] );
        $lastname = strlen( str_replace( ' ', '', $chart_data[0]['fatherLastname']['string'] ) );
        $motherLastname = strlen( str_replace( ' ', '', $chart_data[0]['motherLastname']['string'] ) );
        
        $total = $name + $second_name + $lastname + $motherLastname;
        
        $emi = $this->one_digit_converter($total);
        
        // Saves emi to DB
        foreach($chart_data as $data) {
            $data['emi'] = $emi;
        }
        $meta_id = update_post_meta($post_id, 'chart_numbers', $data);
        
        return $emi;
    }
    
    function get_emi_display( $post_id ) {
        
        $emis = $this->get_emi($post_id);

        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        $emi_full = $chart_data[0]['emi'];
        
        $emi_array = explode('/', $emi_full);
        
        $emi = $emi_array[0];
        
        $emi_minor = end($emi_array);
        
        $name = $chart_data[0]['name']['string'];
        $secondName = $chart_data[0]['secondName']['string'];
        $fatherLastname = $chart_data[0]['fatherLastname']['string'];
        $motherLastname = $chart_data[0]['motherLastname']['string'];
                
        $html = '<h2>La Edad más Importante.</h2>';
        // Add chart content data.
        $interpretation = new Chart_Content();
        $def = $interpretation->get_definition_by_section('emi', 0);
        
        // Replace comodines con el valor real.
        $def = str_replace('%nombre%', $name, $def);
        
        $def = str_replace('%segundoNombre%', $secondName, $def);
        
        $def = str_replace('%apellido%', $fatherLastname, $def);
        
        $def = str_replace('%apellidoMaterno%', $motherLastname, $def);
        
        $def = str_replace('%emiFull%', $emi_full, $def);
        
        $def = str_replace('%emi%', $emi, $def);
        
        $def = str_replace('%2emi%', (2 *$emi), $def);
        
        $def = str_replace('%3emi%', (3 *$emi), $def);
        
        $def = str_replace('%4emi%', (4 *$emi), $def);
        
        $def = str_replace('%emi_minor%', $emi_minor, $def);
        
        $html .= $def;
        
        return $html;
    }
}