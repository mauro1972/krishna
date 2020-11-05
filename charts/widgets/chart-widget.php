<?php

// Register and load the widget
function charts_widget() {
    register_widget( 'charts_widget' );
}
add_action( 'widgets_init', 'charts_widget' );
 
// Creating the widget 
class charts_widget extends WP_Widget {
 
function __construct() {
    
    parent::__construct(

        // Base ID of your widget
        'charts_widget', 

        // Widget name will appear in UI
        __('Charts Widget', 'charts_widget_domain'), 

        // Widget description
        array( 'description' => __( 'Formuario para la creación de cartas.', 'charts_widget_domain' ), ) 
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
        

        // This is where you run the code and display the output
        // if we are in a carta post type, load post id from curreent page.
        $post_id = get_the_id();
        
        $this->pdf_button($post_id);
        
        $this->get_chart_form();
        
        $this->get_chart_numbers($post_id);
        $this->get_create_chart_button($post_id);
        
        $this->get_save_content_to_chart($post_id);
        
        echo $args['after_widget'];
    }
         
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'charts_widget_domain' );
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
    
    protected function get_chart_form() {
        $post_id = get_the_id();
        ?>

        <form method="post" id="chart-form">
        
            <input value="<?php echo get_post_meta($post_id, 'nombre', true); ?>" name="name" type="text" class="chart-form__text chart-form__name" placeholder="Nombre">
            <input value="<?php echo get_post_meta($post_id, 'segundo_nombre', true); ?>" name="second_name" type="text" class="chart-form__text chart-form__second_name" placeholder="Segundo Nombre">
            <input value="<?php echo get_post_meta($post_id, 'apellido_paterno', true); ?>" name="father_lastname" type="text" class="chart-form__text chart-form__father_lastname" placeholder="Apellido Paterno">
            <input value="<?php echo get_post_meta($post_id, 'apellido_materno', true); ?>" name="mother_lastname"type="text" class="chart-form__text chart-form__mother_lastname" placeholder="Apellido Materno">
            <input value="1972-10-16" name="birth_date" type="date" value="<?php echo date("Y-m-d");?>" class="chart-form__birth_date">
            <input class="chart-form__submit" type="submit" value="Crear Carta">
        
        </form>



        <?php
    }
    
    function get_chart_numbers( $post_id ) {
        
        $chart_numbers = get_post_meta( $post_id, 'chart_numbers' );
        if ( ! is_array( $chart_numbers ) ) {
            return '';
        }

        echo '<div id="chart-data-wrapper" data-id="'. $post_id .'" >';
        foreach ( $chart_numbers as $data ){
            if ( is_array( $data ) ) {
                foreach( $data as $key => $value ) { 

                    if ( in_array( $key , array('name', 'secondName', 'fatherLastname', 'motherLastname')) ) {
                    ?>
                    <div id="<?php echo $key; ?>" class="string-box clear">
                        <!--<div class="string-box__section"><?php //echo $data[$key]['string']; ?></div>-->
                        <?php if ( is_array( $data[$key]['data'] ) ) { ?>
                            <?php foreach ($data[$key]['data'] as $letter_key => $letter) { ?>
                            
                                <div id="letter-id-<?php echo $letter_key; ?>" class="string-box__letter <?php echo $letter['type'] ?>"><span class="button button-switch"><?php echo $letter['letter'];  ?></span></div>
                        
                            <?php } ?>
                            <?php } ?>
                    </div> 
                    <?php
                    } else if ($key == 'birthDate') {
                        echo '<div class="birth-date">';
                        echo $data[$key]['day'] .'/';
                        echo $data[$key]['month'] .'/';
                        echo $data[$key]['year'];
                        echo '</div>';
                    } else {
                        
                        echo $key == 'na_full' ? '<div class="chart-data">Numero del Alma: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'pe_full' ? 'Personalidad Externa: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'lv_full' ? 'Leccion de vida: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'vd_full' ? 'Via del Destino: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'nf_full' ? 'Numero de Fuerza: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'emi' ? 'Edad mas Importante: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'firstVocal' ? 'Primera Vocal: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'numAstr' ? 'Numero Astral: '. $data[$key] .'<br>' : '';
                        
                        echo $key == 'sex_num' ? 'Numerologia Sexual: '. $data[$key] .'<br></div>' : '';
                        
                    
                    }
                }
            }
        }

        echo '</div>';

    }
    public function pdf_button($post_id) {
        
        global $wp;

        if (isset( $wp->query_vars['post_type'] ) && $wp->query_vars['post_type'] == 'carta') {
            
            // get current url with query string.
            $current_url =  home_url( $wp->request ); 

            $html = '<div class="button-wrapper"><a class="button pdf-button" href="'. $current_url .'?pdf=' . $post_id .'">PDF</a></div>';

            echo $html;          
            
        }

    }
    
    function get_create_chart_button($post_id) {
        ?>
        <span class="button button-create-chart">Calcular Números</span>
        <?php
        
    }
    
    function get_save_content_to_chart($post_id) {
        ?>
        <span data-id="<?php echo $post_id; ?>" class="button button-save-chart">Guardar Contenido</span>
        <?php
    }
} 