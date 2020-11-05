<?php
/**
 * Via del Destino
 */

class Via_Destino extends Charts {

    function __construct() {
    
    }
    
    function get_vd($post_id) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        //print_r($chart_data);
        
        $na_num = $chart_data[0]['na_full'];
        $pe_num = $chart_data[0]['pe_full'];
        
        if ( strpos($na_num, '/') ) {
            
            $na_array = explode('/', $na_num);
            $na_num = $na_array[0];
        }
        
        if ( strpos($pe_num, '/') ) {
            
            $pe_array = explode('/', $pe_num);
            $pe_num = $pe_array[0];
        } 
        
        $total = $na_num + $pe_num;
        
        $total = $this->one_digit_converter($total);
        
        //save the number in db
        foreach($chart_data as $data) {
            $data['vd_full'] =  $total;
        }
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
        // Add chart content data.
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($total); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['vd'] = $interpretation->get_interpretation_id($total);
            }
            update_post_meta($post_id, 'chart_content', $content);            
            
        }        
        
    }
}