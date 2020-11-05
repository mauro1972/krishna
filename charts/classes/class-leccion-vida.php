<?php

/**
 * dadsad dasd assadas
 */

class Leccion_Vida extends Charts {

    function __construct() {
    
    }
    
    function get_lv( $post_id ) {
        
       $chart_data = get_post_meta($post_id, 'chart_numbers');
        
        $day = $chart_data[0]['birthDate']['day'];
        $month = $chart_data[0]['birthDate']['month'];
        $year = $chart_data[0]['birthDate']['year'];
        
        $year_num = $this->year_to_num($year);
        
        $total = intval($day) + intval($month) + $year_num;
        
        $total = $this->one_digit_converter($total);
        
        //save the number in db
        foreach($chart_data as $data) {
            $data['lv_full'] =  $total;
            $data['nat'] = $day;
        }
        
        $metaid = update_post_meta($post_id, 'chart_numbers', $data);
        
        // Add chart content data.
        $interpretation = new Chart_Content();
        $counter = $interpretation->interpretation_counter($total); 
        
        if ( $counter == 1 ) {
 
            $chart_content = get_post_meta($post_id, 'chart_content');
            foreach($chart_content as $content) {
                $content['lv'] = $interpretation->get_interpretation_id($total);
                
                
            }
            update_post_meta($post_id, 'chart_content', $content);            
            
        }        
    }
    
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