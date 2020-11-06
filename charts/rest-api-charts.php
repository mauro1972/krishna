<?php

add_action('rest_api_init', 'charts_rest_api');

function charts_rest_api() {

    register_rest_route('charts-json/v1', 'chart', array(
        'methods' => 'GET',
        'callback' => 'createChart'
    ));
    
    register_rest_route('charts-json/v1', 'chart-data', array(
        'methods' => 'GET',
        'callback' => 'updateChartData'
    ));  
    
    register_rest_route('charts-json/v1', 'calculate-numbers', array(
        'methods' => 'GET',
        'callback' => 'calculateNumbers'
    ));
    
    register_rest_route('charts-json/v1', 'chart-content', array(
        'methods' => 'GET',
        'callback' => 'saveContentData'
    )); 
    
    register_rest_route('charts-json/v1', 'save-chart-content', array(
        'methods' => 'GET',
        'callback' => 'saveChartContent'
    ));
    
}

function saveChartContent( $data ) {

    if ( $data['action'] == 'reset-pro-content' ) {

        $pro_id = str_replace( 'post-', '', $data['proId'] );
        $post_id = $pro_id;
        
        // buttons controller.
        update_post_meta( $pro_id, 'show_buttons', true );

        $post = get_post($post_id);

   
        $my_post = array();
        $my_post['ID'] = $post->ID;
        $my_post['post_content'] = '';
        $response['post'] = wp_update_post( $my_post );
        $response['response'] = 'SUCCESS';
        
        echo json_encode( $response );
        die(); 
    }

    if ( $data['action'] == 'save-pro-content' ) {

        $pro_id = str_replace( 'post-', '', $data['proId'] );
        $post_id = $pro_id;
    
        $post = get_post($post_id);

        // Get data from carta.
        $data_pronostico = get_post_meta( $pro_id, 'pronostico_anual');
        $data_chart = get_post_meta( $data_pronostico[0]['chartID'], 'chart_numbers');
        $birth_day = $data_chart[0]['birthDate']['day'];
        $birth_month = date("F", mktime(0, 0, 0, $data_chart[0]['birthDate']['month'], 10));
        $year = $data_pronostico[0]['year'];

        // Get Pronostico anual data.
        $pro_content = get_post_meta( $pro_id, 'pronostico_anual_content');
        
        // buttons controller.
        update_post_meta( $pro_id, 'show_buttons', false );

        $chart_content = new Chart_Content();
        $pro = new Pronostico_Anual();

        ob_start();
        ?>
        <h1><?php echo $post->post_title; ?></h1>
        <p>Desde el <?php echo $birth_day .'de '. $birth_month .' '. $year; ?> hasta el <?php echo $birth_day .'de '. $birth_month .' '. ( $year + 1 ); ?></p>
		<div class="pro-intro pa-intro">
			<?php echo $chart_content->get_definition_by_section('pa', 0); ?>
		</div>        
        <?php
        foreach ( $pro_content[0] as $tipo => $datos ) {
            //$new_content = 'Definicion del Año Personal';
            if ( $tipo == 'py'){
                ?>
                <h2>Tú Año Personal es el <?php echo $datos['value']; ?></h2>
                <div class="pro-box py-box">
                    <?php echo $pro->split_number( $pro_id, $datos['value'], $tipo, NULL, false ); ?>
                </div>                
                <?php
            }
            ?>
       
            <?php
            if ( $tipo == 'firstq' ) {
                ?>
            <h2>Cuatrimestres.</h2>
			<div class="pro-intro cua-intro">
				<?php echo $chart_content->get_definition_by_section('cua', 0); ?>
			</div>                 
			    <h3 class="pro-title">Primer Cuatrimestre <?php echo $datos['value'];?>.</h3>
                    <div class="pro-box py-box">
                        <?php echo $pro->split_number( $pro_id, $datos['value'], 'cua', $tipo, false ); ?>
                    </div>                
                <?php
            }
            if ( $tipo == 'secondq' ) {
                ?>
            
			    <h3 class="pro-title">Segundo Cuatrimestre <?php echo $datos['value'];?>.</h3>
                    <div class="pro-box py-box">
                        <?php echo $pro->split_number( $pro_id, $datos['value'], 'cua', $tipo, false ); ?>
                    </div>                
                <?php
            }

            if ( $tipo == 'thirdq' ) {
                ?>
            
			    <h3 class="pro-title">Tercer Cuatrimestre <?php echo $datos['value'];?>.</h3>
                    <div class="pro-box py-box">
                        <?php echo $pro->split_number( $pro_id, $datos['value'], 'cua', $tipo, false ); ?>
                    </div>                
                <?php
            }
            
            if ( $tipo == 'des' ) {
            ?>
		        <h2>Desafíos</h2>
                    <div class="pro-intro des-intro">
                        <?php echo $chart_content->get_definition_by_section('des', 0); ?>
                    </div>            
            <?php
                foreach ( $datos as $order => $num ) {
                    if ( is_numeric( $order ) ) {
                    ?>
                        <h3 class="pro-title"><?php echo $order; ?>° Desafío: <strong><?php echo $num['value']; ?></strong></h3>
                        <div class="pro-box des-box">
                            <?php echo $pro->split_number( $pro_id, $datos[$order]['value'], $tipo, $order, false ); ?>
                        </div>                
                    <?php
                    }
                }    
            }

            if ( $tipo == 'pin' ) {
            ?>
            <h2>Pináculos</h2>
                <div class="pro-intro pin-intro">
                    <?php echo $chart_content->get_definition_by_section('pin', 0); ?>
                </div>  
                <h3 class="pro-title">En esta etapa de tu vida Tú Pináculo es el <?php echo $datos['value']; ?>:</h3>								
				<div class="pro-box pin-box">
					<?php echo $pro->split_number( $pro_id, $datos['value'], $tipo, NULL, false ); ?>
				</div>	          
            <?php    
            }
        }

        // Save post content.
     
        $post->content = '';
        $post->content = ob_get_clean();
        
        $my_post = array();
        $my_post['ID'] = $post->ID;
        $my_post['post_content'] = $post->content;
        $my_post['post_status'] = 'draft';
        $response['post'] = wp_update_post( $my_post );
        $response['response'] = 'SUCCESS';
        
        echo json_encode( $response );
        die();        

    }

    if ( $data['action'] == 'save-inter-id' ) {

        $pro_id = str_replace( 'post-', '', $data['pro_id'] );

        $response['response'] = "SUCCESS";        
        $response['pro_id'] = $pro_id;

        $pro = new Pronostico_Anual();

        if ( 'des' == $data['tipo'] ) {
            $pro->set_pro_section_inter( $pro_id, $data['tipo'], '', $data['order'], $data['inter_id'] );
        } elseif ( 'cua' == $data['tipo']) {
            $pro->set_pro_section_inter( $pro_id, $data['tipo'], $data['number'], $data['order'], $data['inter_id'] );
        } else {
            $response['eco'] = $pro->set_pro_section_inter( $pro_id, $data['tipo'], $data['number'], '', $data['inter_id'] ); 
        }
        // Load all options for a given tipo/number combination.
        echo json_encode( $response );
        die();         
    }

    if ( $data['action'] == 'load-options' ) {
        $response['response'] = 'SUCCESS';
        $response['tipo'] = $data['tipo'];
        $response['number'] = $data['number'];
        $response['order'] = $data['order'];
        // Load all options for a given tipo/number combination.
        $inter = new Chart_Content();
        $response['html'] = $inter->get_definition_by_section_options( $data['tipo'], $data['number'] );
        echo json_encode( $response );
        die();         
    }
    // Creates new interpretation.
    if ( $data['action'] == 'create-inter' ) {
        $title = wp_strip_all_tags( $data['title'] );
        $content = $data['content'];


        $new_post = array(
            'post_type' => 'interpretaciones',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_content' => $content,
        );

        $new_post_id = wp_insert_post( $new_post );

        if ( $new_post_id ) {
            // Saves tipo and number fields.
            update_field( 'tipo', $data['tipo'], $new_post_id );
            update_field( 'numero', $data['number'], $new_post_id );

            // Saves inter id in pronostico content table. 
            $pro_id = str_replace( 'post-', '', $data['pro_id'] );
            $tipo = $data['tipo'];
            $number = $data['number'];
            $order = $data['order'];
            $pro = new Pronostico_Anual();
            $pro->set_pro_section_inter( $pro_id, $tipo, $number, $order, $new_post_id );

            $response['response'] = "SUCCESS";

        } else {
            $response['response'] = 'ERROR';
            $resposne['message'] = "Ups. algo salio mal y no se pudo crear la interpretación";
        }
        $response['response'] = "SUCCESS";
        echo json_encode( $response );
        die();        
    }

    // Delete Interpretation from pronostico postmeta table.
    if ( $data['action'] == 'remove-inter' ) {
        // Load pronostico postmeta.
        $pro_id = str_replace( 'post-', '', $data['pro_id']);
        $pro_content = get_post_meta( $pro_id, 'pronostico_anual_content');

        $tipo = $data['tipo'];
        $number = $data['number'];
        $order = $data['order'];

        if ( $tipo == 'des' ) {
            $pro_content[0][$tipo][$order]['inter'] = '';
        } elseif ( $tipo == 'cua' && $order != '' ) {
            $pro_content[0][$order]['inter'][$number] = '';
        } else {
            $pro_content[0][$tipo]['inter'][$number] = '';
        }
    
        update_post_meta( $pro_id, 'pronostico_anual_content', $pro_content[0] );        

        $response['response'] = "SUCCESS";
        $response['action'] = 'remove-inter';

        echo json_encode( $response );
        die();        
    } 
    
    $post_id = $data['post_id'];
    
    $post = get_post($post_id);
    
    $content = $post->content;
    
    $chart = new Chart_Content();
    $content .= $chart->save_chart_content($post_id, $content);
    
    // edit post content
    $post->content = $content;
    
    $my_post = array();
    $my_post['ID'] = $post->ID;
    $my_post['post_content'] = $content;
    wp_update_post( $my_post );
    
    echo json_encode( $response );
    die();
}

function saveContentData($data) {
    
    $contentData = $data['contentData'];
    
    $post_id = str_replace('post-', '', $contentData['postID']);
    $int_id = $contentData['intID'];
    $section = $contentData['section'];
        
    $chart_content = get_post_meta($post_id, 'chart_content');
    
    foreach($chart_content as $content) {
        $content[$section] = $int_id;
    } 
    
    $meta_id = update_post_meta($post_id, 'chart_content', $content);
    
    return $meta_id;
}

function calculateNumbers($data) {
    
    $postID = $data['postID'];
    
    add_post_meta($postID, 'chart-content', array(), true);
    
    $na = new Numero_Alma();
    $na_number = $na->get_na($postID);
    
    $pe = new Personalidad_Externa();
    $pe->get_pe( $postID);
    
    $lv = new Leccion_Vida();
    $lv->get_lv( $postID);
    
    $vd = new Via_Destino();
    $vd->get_vd( $postID );
    
    $nf = new Numero_Fuerza();
    $nf->get_nf( $postID );
    
    $bv = new Balance_Vida();
    $bv->get_bv($postID);
    
    $des = new Desafios();
    $des->get_desafios($postID);
    
    $pin = new Pinaculos();
    $pin->get_pinaculos($postID);
    
    $emi = new Edad_Mas_Importante();
    $emi->get_emi($postID);
    
    $misc = new Misc_Numbers($postID);
    
    $misc->get_first_vocal();
    
    $misc->get_sex_num();
    
    
    return $postID;
    
}

function updateChartData($data) {
    
    $chart_data = $data['chartData'];
    
    $index = str_replace('letter-id-', '', $chart_data['letterIndex']);
    
    $section = $chart_data['letterSection'];
     
    $chart = get_post_meta($chart_data['postId'], 'chart_numbers');
    
    $type = $chart[0][$section]['data'][$index]['type'];
    
    // switch letter type
    foreach ($chart as $data) {
        $data[$section]['data'][$index]['type'] = ($type == 'cons') ? 'vocal' : 'cons';
    }
        
    $metaId = update_post_meta($chart_data['postId'], 'chart_numbers', $data);
    
    return $type;
}

function createChart($data) {
    
    if ( $data['action'] == 'create-pronostico' ) {
        $chart_id = $data['chartID'];
        $year = $data['year'];
        $pro = new Pronostico_Anual();
        $pro_id = $pro->create_pronostico( $chart_id, $year );
        $path = get_post_permalink( $pro_id );
        $response['path'] = $path;
        echo json_encode( $response );
        die(); 
    }
    
    $form_data = $data['formData'];
    
    // create chart post type.
    $my_post = array(
        'post_title' => wp_strip_all_tags( $form_data['name'] .' '. $form_data['fatherLastname']  ),
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'carta'
    );
    
    $post_id = wp_insert_post( $my_post );
    
    // save chart data in options table.
    $chart_mauro = new charts($form_data);
    $metaId = $chart_mauro->save_personal_data($post_id) ;
    
    $permalink = get_post_permalink($post_id);
    
    $post = array(
        'id' => $post_id,
        'permalink' => $permalink,
    );
    
    return $post;
    
}
