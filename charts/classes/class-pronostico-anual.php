<?php
/**
 * Pronostico Anual.
 */
class Pronostico_Anual extends Charts {

	private $postID;

	private $chart_numbers;

	private $year;

    function __construct( $post_ID = NULL, $chart_numbers = NULL, $year = NULL ) {
		$this->postID = $post_ID;
		$this->chart_numbers = $chart_numbers;
		$this->year = $year;

		$this->des = new Desafios();
		$this->pin = new Pinaculos();
	}

	public function create_pronostico_post( $data ) {
		$title = 'Pronostico Anual '. $data['name'] .' '. $data['lastname'] .' año '. $data['year'];
		// create chart post type.
		$my_post = array(
			'post_title' => wp_strip_all_tags( $title ),
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'pronostico',
		);

		$post_id = wp_insert_post( $my_post );

		// Load personal data in the DB.
		if ( is_numeric( $post_id) ) {
			update_field( 'carta', $data['chartID'], $post_id );
			update_field( 'pro_year', $data['year'], $post_id );
			// Save desafios values.
			$des = new Desafios();
			$data['des'] = $des->get_desafios( $data['chartID'] );
			// Save pinaculo value.
			$chart_data = get_post_meta( $data['chartID'], 'chart_numbers');
			$pin = new Pinaculos();
			$pin_value = $pin->edades_pinaculos( $chart_data, $data['age'] );
			$data['pin'] = $pin_value;
			$meta_id = add_post_meta($post_id, 'pronostico_anual', $data, true );
			$content = $this->set_pro_content( $post_id );
			$meta_content = add_post_meta($post_id, 'pronostico_anual_content', $content, true );
		}

		return $post_id;
	}
    
    public function create_pronostico( $post_id, $year ) {
        // Cargamos los datos de la carta necesarios.

        $chart_numbers = get_post_meta($post_id, 'chart_numbers');
        $pronostico_year = $year;
        $current_year = date('Y');
        $birth_year = $chart_numbers[0]['birthDate']['year'];
        $last_birthday = $current_year .'-'. $chart_numbers[0]['birthDate']['month'] .'-'. $chart_numbers[0]['birthDate']['day'];
        
        $data['year'] = $pronostico_year;
        
        // Age Calculation.
        // Tomamos como año relativo el del pronostico.
        // Verificamos que el año del pronostico sea mayor al de nacimiento.
        if ( $birth_year > $pronostico_year ) {
            $data['age'] = 'No se puede calcular para años previos al nacimiento.';
        } 
        // Calculamos la edad en el alo del pronostico
        $data['age'] = $pronostico_year - $birth_year;
        
        $data['lastname'] = ucfirst( $chart_numbers[0]['fatherLastname']['string'] );
        $data['name'] = ucfirst( $chart_numbers[0]['name']['string'] );
        $data['chartID'] = $post_id; 
        
        // Pronostico Values
        $data['py'] = $this->personal_year( $chart_numbers[0]['birthDate']['day'], $chart_numbers[0]['birthDate']['month'], $pronostico_year );

		$data['quarters'] = $this->quarters_number( $birth_year, $data['age'] );
        
        // Creamos el Pronostico.
        $pronostico_ID = $this->create_pronostico_post( $data );
         
        return $pronostico_ID;
    }


	public function get_birthday_year( $chart_numbers ) {
		echo 'pronostico year: '. $this->year;
		echo '<br>current year '. date('Y');

		$time = time();
		//$time = $time->format('U');

		$out = array();
		$birthdate = $chart_numbers[0]['birthDate'];
		$day = $birthdate['day'];
		$month = $birthdate['month'];
		$birthday_year_value = $birthdate['year'];
		$current_year = date('Y');
		$last_birthday = $current_year .'-'. $month .'-'. $day;
		$year = $this->year;

		
		//Create a DateTime object.
		$dateTime = new DateTime((string)$last_birthday);
		
		//Format it into a Unix timestamp.
		$my_last_birthdate = $dateTime->format('U');
	
		//echo '<br>mi ultimo cumpleaños fue en el: ';
	
		if ( $my_last_birthdate > $time ) {

			$birthday_year = $current_year - 1;
			
		} else {
			
			$birthday_year = $current_year;
	
		}/*
		// si el año de pronostico ingresado en el formulario es arbitario, se calcula con ese año.
		$current_year = ( $this->year != date('Y') ) ? $this->year : $current_year;
		$birthday_year = ( $this->year != date('Y') ) ? $this->year : $birthday_year;
		$age = $birthday_year - $birthday_year_value;

		echo '<br>edad: '. $age;
        */
		$out['age'] = 47;
        
		$out['year'] = $current_year;

		$out['py'] = $this->personal_year( $day, $month, $year );

		$out['quarters'] = $this->quarters_number( $birthday_year, 47 );

		return $out;
	}
	/**
	 * @param int $year año de nacimeinto.
	 * @param int birthday_year ultimo año del cumpleaños. 
	 */
	public function personal_year( $day, $month, $year, $birthday_year = NULL ) {

		$chart = new Charts();
		$dm = (int)$day + (int)$month;
		$year = $this->one_digit_number_year( $year );
		$sum = (int)$day  +  (int)$month + $year;
		$py = $chart->one_digit_converter( $sum );
		return $py;
	}
    
    public function one_digit_number_year( $year ) {
        $sum = 0;
        $year_array = str_split( (string)$year, 2 );
        if ( $year_array[0] == 20 ) {
            $last_digits_array = str_split( (string)$year_array[1] );
            foreach ( $last_digits_array  as $digit ) {
                $sum += (int)$digit;
            }
            $sum = 20 + $sum;
        } else {
            $year_digits_array = str_split( (string)$year );
             foreach ( $year_digits_array  as $digit ) {
                $sum += (int)$digit;
            }
        }
        
        return $sum;
    }

	public function quarters_number( $birthday_year, $age ) {

		// Primer Cuatrimestre.
		$first_cuarter = $birthday_year + $age;
		$first_cuarter_number = $this->one_digit_converter( $first_cuarter );
		//echo '<br>Primer cuatrimestre: '. $first_cuarter_number;

		$out['firstq'] = $first_cuarter_number;
		
		// Segundo Cuatrimestre.
		$lv = $this->chart_numbers[0]['lv_full'];
		$lv = $this->get_bigger_number( $lv );
		$second_quarter = $birthday_year + $lv;
		//echo '<br>Segundo Cuatrimestre: '. $this->one_digit_converter( $second_quarter );
		$out['secondq'] = $this->one_digit_converter( $second_quarter );

		// Third Quarter.
		$na = $this->chart_numbers[0]['na_full'];
		$na = $this->get_bigger_number( $na );
		$third_quarter = $birthday_year + $na;
		//echo '<br>Tercer cuatrimestre: '. $this->one_digit_converter( $third_quarter );
		$out['thirdq'] = $this->one_digit_converter( $third_quarter );

		return $out;
	}

	public function get_bigger_number( $num ) {
		$pos = strpos($num, '/');
		if ($pos !== false) {
			$num_array = explode('/', $num);
			return $num_array[0];
		} else {
			return $num;
		}
	}

	public function get_pro_by_number( $number = NULL ) {
		$int_ID = array();

		$int_query = new WP_Query( array(
			'post_type' => array( 'interpretaciones' ),
			'meta_query' => array(
				array(
					'key' => 'tipo',
					'value' => 'cua',
				),
				array(
					'key' => 'numero',
					'value' => $number,
				),
			),
		));

		while( $int_query->have_posts() ) {
			$int_query->the_post();
			$int_ID[] = get_the_ID();
		}
		wp_reset_query();
		
		if ( isset ( $int_ID[0] ) ) {
			$post_cua = get_post( $int_ID[0] );
			$cua_content = $post_cua->post_content;
			return $cua_content;
		} else {
			return 'Crear Interpretación para este Valor de Cuatrimestre.';
		}
	}

	public function get_related_chart( $pro_id = NULL, $pro_year = NULL ) {
		$chart = get_field( 'carta', $pro_id );
		$year = get_field( 'pro_year', $pro_id );

		if ( $year == $pro_year ) {
			return false;
		}

		$title = $chart[0]->post_title;
		$link = get_post_permalink( $chart[0]->ID );


		$out = sprintf( '<a  href="%s">%s %s</a>', $link, $title, $year );
		return $out;
	}
	/**
	 * Creates the arrar to save interpretations.
	 */
	public function set_pro_content( $pro_id ) {
		$pro_data = get_post_meta( $pro_id, 'pronostico_anual');

		$inter['py']['value'] = $pro_data[0]['py'];
		$inter['py']['inter'] = '';
		$inter['firstq']['value'] = $pro_data[0]['quarters']['firstq'];
		$inter['firstq']['inter'] = '';
		$inter['secondq']['value'] = $pro_data[0]['quarters']['secondq'];
		$inter['secondq']['inter'] = '';
		$inter['thirdq']['value'] = $pro_data[0]['quarters']['thirdq'];
		$inter['thirdq']['inter'] = '';
		$inter['pin']['value'] = $pro_data[0]['pin'];
		$inter['pin']['inter'] = '';					

		$inter['des'][1]['value'] = $pro_data[0]['des']['1'];
		$inter['des'][1]['inter'] = ''; 
		$inter['des'][2]['value'] = $pro_data[0]['des']['2'];
		$inter['des'][2]['inter'] = ''; 		
		$inter['des'][3]['value'] = $pro_data[0]['des']['3'];
		$inter['des'][3]['inter'] = ''; 
		$inter['des'][4]['value'] = $pro_data[0]['des']['4'];
		$inter['des'][4]['inter'] = ''; 

		// Creates an array to save interpretations separated by each number.
		foreach ( $inter as $tipo => $data ) {
			if ( $tipo != 'des' ) {
				$value = $data['value'];
				$value_array = explode( '/', $value );
				$inter_array = array();
				foreach( $value_array as $number ) {
					$inter_array[$number] = '';
				}
				$inter_array[$value] = '';
				$inter[$tipo]['inter'] = $inter_array;			
			}
		}

		return $inter;
	}

	public function get_pro_section_value( $pro_id = NULL, $tipo = NULL, $number = NULL, $order = NULL ) {
		$pro_content = get_post_meta($pro_id, 'pronostico_anual_content');
		if ( $tipo == 'des' ) {
			if ( isset ( $pro_content[0]['des'][$order]['inter'] ) ) {
				if ( is_numeric ($pro_content[0]['des'][$order]['inter'] ) ) {
					$inter_id = $pro_content[0]['des'][$order]['inter'];
					return $inter_id ? $inter_id : false;
				}
			} else {
				return false;			
			}
		} elseif ( $tipo == 'cua' ) {
			if ( isset ( $pro_content[0][$order]['inter'][$number] ) ) {
				if ( is_numeric ($pro_content[0][$order]['inter'][$number] ) ) {
					$inter_id = $pro_content[0][$order]['inter'][$number];
					return $inter_id ? $inter_id : false;
				}
			
			} else {
				return false;			
			}

		} else {
			if ( is_numeric( $pro_content[0][$tipo]['inter'][$number] ) )  {
				$inter_id = $pro_content[0][$tipo]['inter'][$number];
				return $inter_id ? $inter_id : false;
			} else {
				return false;
			}

		}

		return 'no value';
	}

	/**
	 * Saves the interpretation ID in the content variable.
	 */
	public function set_pro_section_inter( $post_id = NULL, $tipo = NULL, $number = NULL, $order = NULL, $inter_id = NULL ) {
		$pro_content = get_post_meta($post_id, 'pronostico_anual_content');
		// For des section we need to pass two values. we use an array.
		foreach( $pro_content as $content ) {
			if ( $tipo == 'des' ) {
				$content['des'][$order]['inter'] = $inter_id;
			} elseif( $tipo == 'cua' ) {
				$content[$order]['inter'][$number] = $inter_id;
			} else {
				$content[$tipo]['inter'][$number] = $inter_id;
			}
		}

		return update_post_meta( $post_id, 'pronostico_anual_content', $content );
	}

	public function pronostico_anual_content( $post_id = NULL ) {
		$chart_content = new Chart_Content();
        $show_buttons = get_post_meta( $pro_id, 'show_buttons' );

		// Get data from carta.
		$data_pronostico = get_post_meta( $post_id, 'pronostico_anual');
		$data_chart = get_post_meta( $data_pronostico[0]['chartID'], 'chart_numbers');
		$birth_day = $data_chart[0]['birthDate']['day'];
		$birth_month = date("F", mktime(0, 0, 0, $data_chart[0]['birthDate']['month'], 10));
		$year = $data_pronostico[0]['year'];

		$data_pronostico = get_post_meta( get_the_ID(), 'pronostico_anual');
		ob_start();
		?>
		<p>Desde el <?php echo $birth_day .' de '. $birth_month .' '. $year; ?> hasta el <?php echo $birth_day .' de '. $birth_month .' '. ( $year + 1 ); ?></p>
		<div class="pro-intro pa-intro">
			<?php echo $chart_content->get_definition_by_section('pa', 0); ?>
		</div>
		<h2>Año Personal <?php echo $data_pronostico[0]['py']; ?>.</h2>
			<div class="pro-box py-box">
				<?php echo $this->split_number( $post_id, $data_pronostico[0]['py'], 'py' ); ?>
			</div>

		<h2>Cuatrimestres</h2>
			<div class="pro-intro cua-intro">
				<?php echo $chart_content->get_definition_by_section('cua', 0); ?>
			</div>
			<h3 class="pro-title">Primer Cuatrimestre.</h3>
				<div class="pro-box py-box">
					<?php echo $this->split_number( $post_id, $data_pronostico[0]['quarters']['firstq'], 'cua', 'firstq' ); ?>
				</div>
			<h3 class="pro-title">Segundo Cuatrimestre.</h3>
				<div class="pro-box py-box">
					<?php echo $this->split_number( $post_id, $data_pronostico[0]['quarters']['secondq'], 'cua', 'secondq' ); ?>
				</div>
			<h3 class="pro-title">Tercer Cuatrimestre.</h3>
				<div class="pro-box py-box">
					<?php echo $this->split_number( $post_id, $data_pronostico[0]['quarters']['thirdq'], 'cua', 'thirdq' ); ?>
				</div>
		<h2>Desafíos</h2>
			<div class="pro-intro des-intro">
				<?php echo $chart_content->get_definition_by_section('des', 0); ?>
			</div>
			<?php 
				foreach ( $data_pronostico[0]['des'] as $order => $num ) {
					?>
					<h3 class="pro-title"><?php echo $order; ?>° Desafío: <strong><?php echo $num; ?></strong></h3>
					<div class="pro-box des-box">
						<?php echo $this->split_number( $post_id, $data_pronostico[0]['des'][$order], 'des', $order ); ?>
					</div>					
					<?php
				} 
			?>
		<h2>Pináculos</h2>
			<div class="pro-intro pin-intro">
				<?php echo $chart_content->get_definition_by_section('pin', 0); ?>
			</div>
			
			<h3 class="pro-title">En esta etapa de tu vida Tú Pináculo es el <?php print_r($data_pronostico[0]['pin']); ?>:</h3>								
				<div class="pro-box pin-box">
					<?php echo $this->split_number( $post_id, $data_pronostico[0]['pin'], 'pin' ); ?>
				</div>	
	
		<?php
		
		return ob_get_clean();
	}

	public function split_number( $post_id, $number = NULL, $tipo = NULL, $order = NULL, $buttons = true ) {
		$number_parts[] = $number;
		
		$buttons = $this->buttons( $post_id, $buttons );

		if ( strpos( $number, '/' ) ) {
			$numberArray = explode( '/', $number );
			foreach ( $numberArray as $part ) {
				$number_parts[] = $part;
			}
		}
		ob_start();
		foreach ( $number_parts as $num ) {
			?>
			<div class="number-box" data-order="<?php echo $order; ?>" data-tipo="<?php echo $tipo; ?>" data-number="<?php echo $num; ?>">
				<div class="number-box__header">
					<h4>Significado para <?php echo $num; ?>:</h4>
					<?php echo $buttons; ?>
				</div>
				<div class="number-box__content">
					<?php if ( $this->get_pro_section_value( $post_id, $tipo, $num, $order ) ) {
						$inter_id = $this->get_pro_section_value( $post_id, $tipo, $num, $order ); 

						if ( is_numeric( $inter_id ) ) {
							$post_inter = get_post( $inter_id );
							echo $post_inter->post_content;
						} else {
							echo 'Cargar Contenido para este número.';
						}

					} else {
						echo 'Cargar el Sgnificado para este número';
					}
					?>
				</div>
			</div>
			<?php

		}
		return ob_get_clean();
	} 

	public function buttons( $post_id, $buttons ) {
		$post = get_post( $post_id );

		if ( $post->post_content == '' && $buttons ) {
			ob_start();
			?>
				<div class="number-box__buttons">
					<a href="" class="btn btn--select-inter" >Cargar</a>
					<a href="" class="btn add-inter" >Nuevo</a>
					<a href="" class="btn remove-inter" >Borrar</a>
				</div>
			<?php
			return ob_get_clean();
		} else {
			ob_start();
			?>

			<?php
			return ob_get_clean();
		}
	}
}
