<?php

class Personalidad_Externa extends Charts {
    
    function __construct() {
        
    }
    
    function get_pe( $post_id ) {
  
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        
        // Cargamos los datos del nombre completo.
        $name = $chart_data[0]['name']['data'];
        $second_name = $chart_data[0]['secondName']['data'];
        $lastName = $chart_data[0]['fatherLastname']['data'];
        
        $name_sum = $this->section_to_number($name, 'cons');
        
        $second_name_sum = $this->section_to_number($second_name, 'cons'); 
        
        $last_name_sum = $this->section_to_number($lastName, 'cons');  
        
        $total = $name_sum + $second_name_sum + $last_name_sum;
        
        $total = $this->one_digit_converter($total);
        
        //save the number in db
        foreach($chart_data as $data) {
            $data['pe_full'] =  $total;
        }        
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
        // Add chart content data.
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($total); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['pe'] = $interpretation->get_interpretation_id($total);
            }
            update_post_meta($post_id, 'chart_content', $content);            
            
        }        
    }
    
}