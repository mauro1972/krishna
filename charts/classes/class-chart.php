<?php

class Charts {
    
    public $chart_data;
    public $dayOfbirth;
    public $monthOfbirth;
    public $yearOfBirth;
    public $ltn;
    public $vocals;
    
    
    function __construct( $data = array()) {
        
        foreach($data as $key => $value ){
            $this->chart_data[$key] = $value;
        }
        
        $this->ltn =  $this->letter_to_number();
        $this->vocals = $this->vocals();
    }
    
    function chart_data() {
        
        $this->birth_date_converter();
    }
    
    function tag_replace( $def, $post_id ) {
        
        $chart_data = get_post_meta($post_id, 'chart_numbers');
        
    
        // Load values
        $name = $chart_data[0]['name']['string'];
        $secondName = $chart_data[0]['secondName']['string'] ? $chart_data[0]['secondName']['string'] : '';
        $fatherLastname = str_replace( ' ', '', $chart_data[0]['fatherLastname']['string'] );
        $motherLastname = str_replace( ' ', '', $chart_data[0]['motherLastname']['string'] );
        
        // Emi
        $emi_full = $chart_data[0]['emi'];
        $emi_array = explode('/', $emi_full);
        $emi = $emi_array[0];
        $emi_minor = end($emi_array);
        
        // First vocla
        $first_vocal = $chart_data[0]['firstVocal'];
        
        // Numero sexual
        $sex_num = isset( $chart_data[0]['sex_num'] ) ? $chart_data[0]['sex_num'] : '';
        
        // Numerologia Astral
        $numAstr = isset( $chart_data[0]['numAstr'] ) ? $chart_data[0]['numAstr'] : '';
        
        // Natalicio
        $nat = isset( $chart_data[0]['nat'] ) ? $chart_data[0]['nat'] : '';
        
        //numero del alma
        $na = isset( $chart_data[0]['na_full'] ) ? $chart_data[0]['na_full'] : '';
        
        //leccion de vida
        $lv = isset( $chart_data[0]['lv_full'] ) ? $chart_data[0]['lv_full'] : '';
        
        //personalidad externa
        $pe = isset( $chart_data[0]['pe_full'] ) ? $chart_data[0]['pe_full'] : '';
        
        //via del destino
        $vd = isset( $chart_data[0]['vd_full'] ) ? $chart_data[0]['vd_full'] : '';
        
        //numero de fuerza
        $nf = isset( $chart_data[0]['nf_full'] ) ? $chart_data[0]['nf_full'] : '';        
    
        
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
        
        $def = str_replace('%firstVocal%', $first_vocal, $def);
        
        $def = str_replace('%sex_num%', $sex_num, $def);
        
        $def = str_replace('%numAstr%', $numAstr, $def);
        
        //planeta num_to_astro($num)
        $planet = $this->num_to_astro($numAstr);
        $def = str_replace('%planeta%', $planet, $def);
        
        $def = str_replace('%nat%', $nat, $def);
        
        $def = str_replace('%na%', $na, $def);
        
        $def = str_replace('%lv%', $lv, $def);
        
        $def = str_replace('%pe%', $pe, $def);
        
        $def = str_replace('%vd%', $vd, $def);
        
        $def = str_replace('%nf%', $nf, $def);        
        
        return $def;
    }
     
    
    function birth_date_converter() {
        
        $birth_date = $this->chart_data['birthDate'];
        
        $birth_date_array = explode('-', $birth_date);
        
        $this->dayOfBirth = $birth_date_array[2];
        $this->monthOfBirth = $birth_date_array[1];
        $this->yearOfBirth = $birth_date_array[0];
        
    }
    
    function letter_to_number() {
        
        $codigo = array(
            "a" => 1,
            "b" => 2,
            "c" => 3,
            "d" => 4,
            "e" => 5,
            "f" => 6,
            "g" => 7,
            "h" => 8,
            "i" => 9,
            "j" => 10,
            "k" => 11,
            "l" => 12,
            "m" => 13,
            "n" => 14,
            "ñ" => 14,
            "o" => 15,
            "p" => 16,
            "q" => 17,
            "r" => 18,
            "s" => 19,
            "t" => 20,
            "u" => 21,
            "v" => 22,
            "w" => 23,
            "x" => 24,
            "y" => 25,
            "z" => 26,
        );
        
        return $codigo;
        
    }
    
    function vocals() {
        
        $vocals = array('a', 'e', 'i', 'o', 'u');
        
        return $vocals;
        
    }
    
    function string_to_numbers( $string ) {
        
        if ( $string == '' || $string == ' ') {
            return array();
        }

        $strArr = str_split( strtolower( $string ) );
        
        foreach ( $strArr as $key => $letter ){
            
            if ( isset( $letter ) && ( $letter != ' ' && $letter != '' ) ) {
                
                if ( $this->ltn[$letter] > 9 ) {
                    
                    $num = str_split( $this->ltn[$letter] );
                    $sum = 0;
                    
                    foreach ( $num as $digit ) {
                        
                        $sum += $digit;
                        
                    }
                    
                    if ( $sum == 10 ) {
                        $sum = 10 .'/'. 1;
                    }
                }
            }
                   
            $out[] = array(
                'letter' => $letter,
                'type' => in_array( $letter, $this->vocals) ? 'vocal' : 'cons',
                'value' => ($this->ltn[$letter] < 10) ? array($letter => $this->ltn[$letter]) : array($letter => $this->ltn[$letter] .'/'. $sum ),
            );
                
        }
        
        return $out;   
    } 
    
    
    public function one_digit_converter($num) {
        
        $output = 0;
        
        $num = (int)$num;
        
        if ( $num  <= 9 ) {
            return $num;
        }
        
        if ( $num == 20 ) {
            return $num;
        }
        
        if ( $num <= 99 ){
            
            $two_digits_array = str_split( (string)$num );

            foreach ( $two_digits_array as $value ) {
                $output = $output + $value;
            }            
            
            // si es menor a 78 debe aparecer incluido en la salida
            if ( $num <= 78 ) {
                  
                return $num . '/' . $this->one_digit_converter($output);
                
            } else {
                
                return $this->one_digit_converter($output);

            }
        } else {
            
            /*$two_digits_array = str_split( (string)$num, 2 );
            
            if ( $two_digits_array[0] == 20 ) {
               $two_digits_array = str_split( (string)$two_digits_array[1] ); 
                $output = 20;
            }
            
            foreach ( $two_digits_array as $value ) {
                $output = $output + $value;
            }  
            
            return $this->one_digit_converter($output);*/
            // split the number in an array of two digits elements each.
            $sum = 0;
            $num_array = str_split( (string)$num, 2 );
            
            // number 20 is an special case
            if ( $num_array[0] == 20 ) {
                
                $num_array_1 = str_split( (string)$num_array[1] );
                
                foreach ( $num_array_1 as $digit ) {
                    $sum += (int)$digit; 
                }
                
                $sum += 20;
                return $this->one_digit_converter( $sum );
                
            } else {
                
                $sum = $num_array[0] + $num_array[1];
            
                return $this->one_digit_converter( $sum );
                
            }
            
        }
    }
    
    function one_digit_number( $num ) {
        
        if ( $num == 20 ) {
            return 2;
        }
        
        $pos = false;

        $pos = strpos( $num, '/' );

        if ($pos !== false) {
            $num_array = explode('/', $num);
            $one_digit = end($num_array);
            return $one_digit;

        } else if ($num > 9) {
            $num = $this->one_digit_converter($num);
            
            $one_digit = $this->one_digit_number($num);
            //$one_digit = $num;
        } else {
            $one_digit = $num;
        }

        return $one_digit;        
    }
    
    function section_to_number($section, $type = NULL) {
        $na_num = 0;
        foreach ($section as $section_data ) {

            if ( $section_data['type'] == $type ) {
                
                foreach ($section_data['value'] as $number ) {
                    
                    $pos = strpos($number, '/');

                    if ($pos !== false) {

                      $num_array = explode('/', $number);
                      $digit = end($num_array);

                    } else {

                      $digit = $number;

                    }

                    $na_num += $digit;                    
                    
                }

            }
        }
        
        return $na_num;
    }    
    
    /*
    * this problably goes in separate class.
    */
    
    function save_personal_data($post_id) {
        
        $personal_data = array();

        
        foreach ($this->chart_data as $data_key => $string ) {
            
            if ( $data_key != 'birthDate' && ( isset( $string ) && $string != ' ' ) ) {
                
                $personal_data[$data_key] = array(
                    'string' => $string,
                    'data' => $this->string_to_numbers( $string ),
                );
                
            } else {
                $this->birth_date_converter();
                $personal_data['birthDate'] = array(
                    'day' => $this->dayOfBirth,
                    'month' => $this->monthOfBirth,
                    'year' => $this->yearOfBirth,
                );             
            }
        }

        // Load personal data in the DB.
        $metaId = add_post_meta($post_id, 'chart_numbers', $personal_data, true );
        
        return $metaId;        
        
    }
    
    function num_to_astro($num) {

       $astro = array(
           '1' => 'sol',
           '2' => 'Luna',
           '3' => 'Jupiter',
           '4' => 'Urano',
           '5' => 'Mercurio',
           '6' => 'Venus',
           '7' => 'Neptuno',
           '8' => 'Saturno',
           '9' => 'Marte',
       ); 
       
       if ( isset( $astro[$num]) ) {
        return $astro[$num];
       }
    }
}

