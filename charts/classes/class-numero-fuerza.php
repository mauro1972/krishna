<?php
/**
 * Numero de fuerza
 */

class Numero_Fuerza extends Charts {

    function __construct() {
    
    }
    
    function get_nf($post_id) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        //print_r($chart_data);
        
        $lv_num = $chart_data[0]['lv_full'];
        $vd_num = $chart_data[0]['vd_full'];
        
        if ( strpos($lv_num, '/') ) {
            
            $lv_array = explode('/', $lv_num);
            $lv_num = $lv_array[0];
        }
        
        if ( strpos($vd_num, '/') ) {
            
            $vd_array = explode('/', $vd_num);
            $vd_num = $vd_array[0];
        } 
        
        $total = $lv_num + $vd_num;
        
        $total = $this->one_digit_converter($total);
        
        //save the number in db
        foreach($chart_data as $data) {
            $data['nf_full'] =  $total;
        }
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
        // Add chart content data.
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($total); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['nf'] = $interpretation->get_interpretation_id($total);
            }
            update_post_meta($post_id, 'chart_content', $content);            
            
        }        
        
    }
}