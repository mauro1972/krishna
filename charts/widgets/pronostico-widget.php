<?php

// Register and load the widget
function pronostico_widget() {
    register_widget( 'pronostico_widget' );
}
add_action( 'widgets_init', 'pronostico_widget' );
 
// Creating the widget 
class pronostico_widget extends WP_Widget {
 
function __construct() {
    
    parent::__construct(

        // Base ID of your widget
        'pronostico_widget', 

        // Widget name will appear in UI
        __('Pronostico Widget', 'pronostico_widget_domain'), 

        // Widget description
        array( 'description' => __( 'Formuario para la creación de Pronosticos.', 'pronostico_widget_domain' ), ) 
        );
    }
 
    // Creating widget front-end

    public function widget( $args, $instance ) {
        
        if( !is_user_logged_in() ) {
            return;
        }
        
        $title = apply_filters( 'widget_title', $instance['title'] );

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $this->get_pronostico_form();
        // This is where you run the code and display the output
        // if we are in a carta post type, load post id from curreent page.
        /*$post_id = get_the_id();*/
        
        /*$this->pdf_button($post_id);
        
        $this->get_chart_form();
        
        $this->get_chart_numbers($post_id);
        $this->get_create_chart_button($post_id);
        
        $this->get_save_content_to_chart($post_id);*/
        
        echo $args['after_widget'];
    }
         
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'pronostico_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
     
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
    
    protected function get_pronostico_form() {
        $post_id = get_the_id();
        if ( isset( $_POST['chart-id'] ) ) {
            if ( ! isset( $_POST['create_pronostico_page_field'] ) 
                || ! wp_verify_nonce( $_POST['create_pronostico_page_field'], 'create_pronostico_page' ) 
            ) {
                //print 'Sorry, your nonce did not verify.';
            
            } else {
                $chart_ID = $_POST['chart-id'];
                $pronostico_year = $_POST['pronostico-year'];
                $this->create_pronostico( $chart_ID, $pronostico_year );
            }
        }
        ?>

        <form method="POST" id="pronostico-form" name="create-pronostico">
            <label for="pronostico-year">Año del Pronostico</label>
            <input name="pronostico-year" value="<?php echo date('Y'); ?>" id="pronostico-year" type="text" class="widefat"></input>
            
            <label for="choice-chart">
            <select class='widefat' id="choice-chart" name="chart-id" type="text">
                <option value="">Elegir Carta</option>
                <?php  foreach( $this->ramya_get_cartas() as $ID => $title ) { ?>
                    <option value="<?php echo $ID; ?>"><?php echo $title; ?></option>
                <?php } ?>
            </select>
            </label>
            <?php wp_nonce_field( 'create_pronostico_page', 'create_pronostico_page_field' ); ?>
            <input class="pronostico-form__submit" type="submit" value="Crear Pronostico">
        
        </form>



        <?php
    }
    /*------------------------------------*\
        Create Pronostico Functions.
        TODO: move this code to an independent class.
    \*------------------------------------*/
    function create_pronostico( $post_id, $year ) {
        
        // Cargamos los datos de la carta necesarios.
        /*$chart_numbers = get_post_meta($post_id, 'chart_numbers');
        $pronostico = new Pronostico_Anual( $post_id, $chart_numbers, $year );

        // Calculamos los datos para el pronostico anual.

        // Generamos el pronostico con los datos anterioes.
        $data = $pronostico->get_birthday_year($chart_numbers);
        $data['lastname'] = ucfirst( $chart_numbers[0]['fatherLastname']['string'] );
        $data['name'] = ucfirst( $chart_numbers[0]['name']['string'] );
        $data['chartID'] = $post_id;
        $pronostico_ID = $pronostico->create_pronostico_post( $data );*/

        //echo $pronostico_ID;
        // Imprimimos el ID/Link del reciente pronostico creado.
    }
    /*------------------------------------*\
        Pronosticos Queries.
    \*------------------------------------*/
    function ramya_get_cartas() {
        $args = array(
            'post_type' => 'carta',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
            $options[get_the_ID()] = get_the_title();
        endwhile; endif;

        wp_reset_postdata();

        return $options;
    }    
    
} 