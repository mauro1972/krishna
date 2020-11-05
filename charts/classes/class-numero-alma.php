<?php
/**
 * Suma las vocales sdel nombre completo.
 * saves it in db
 */

class Numero_Alma extends Charts  {
    
    function __construct() {

     parent::__construct();
        
    }
    
    
    function get_na($post_id) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        
        // Cargamos los datos del nombre completo.
        $name = $chart_data[0]['name']['data'];
        $second_name = $chart_data[0]['secondName']['data'];
        $lastName = $chart_data[0]['fatherLastname']['data'];
        
        $name_sum = $this->section_to_number($name, 'vocal');
        
        $second_name_sum = $this->section_to_number($second_name, 'vocal'); 
        
        $last_name_sum = $this->section_to_number($lastName, 'vocal');
        
        $total = $name_sum + $second_name_sum + $last_name_sum;
        
        $total = $this->one_digit_converter($total);
        
        //save the number in db
        foreach($chart_data as $data) {
            $data['na_full'] =  $total;
        }
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
        // Add chart content data.
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($total); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['na'] = $interpretation->get_interpretation_id($total);
            }

            if ( $content ) {
                update_post_meta($post_id, 'chart_content', $content);
            }
                        
            
        }
          
        return $total;

    }
    
    
}