<?php

function krishna_register_widget() {
  register_widget('krishna_widget');
}

add_action( 'widgets_init', 'krishna_register_widget' );

class Krishna_Widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'krishna_widget',
      __('Krishna Widget', 'html5blank'),
      array('description' => __('Herramientas para el calculo de la Carta numerologica', 'html5blank'), )
    );
  }

  public function widget($args, $instance) {
    $title = apply_filters( 'widget_title', $instance['title'] );
    echo $args['before_widget'];
    //if title is present
    if ( ! empty( $title ) ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }

    // Load PDF buttons.
    if(function_exists('pf_show_link')) {
      echo '<div class="pdf-wrapper">';
      echo pf_show_link();
      echo '</div>';
    }

    // Load cartas form.
    $obj = new Krishna_Widget;
    echo $obj->krishna_form();

    echo $args['after_widget'];
  }

  public function form($instance) {
    if ( isset( $instance[ 'title' ] ) )
    $title = $instance[ 'title' ];
    else
    $title = __( 'Default Title', 'html5blank' );
  ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>

    <?php
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }


  public function krishna_form() {

    global $post;

    $numeros = get_post_meta($post->ID,'numbres');

    $secciones = array(
      'na' => 'Numero del Alma',
      'lv' => 'Leccion de Vida',
      'vd' => 'Vía del Destino',
      'pe' => 'Personalidad Extarena',
      'nf' => 'Número de fuerza'
    );

    foreach($secciones as $key => $seccion) { ?>
      <div class="section section-number" data-section="<?php echo $key; ?>">
        <em><?php echo $seccion; ?>: </em><?php echo $numeros[0][0][$key .'_full']; ?>
      </div>
    <?php   }

    ?>
    <div class="section-form">
      <input class="widefat" type="text" placeholder="Buscar interpretaciones">
      <div class="results-wrapper"></div>
    </div>

    <div class="int-form">
      <input class="int-form__title widefat" type="text">
      <textarea class="int-form__body widefat" name="" id="" cols="30" rows="10"></textarea>
      <select class="int-form__seccion inline-input" name="" id="">
        <option value="0">Elegir Sección</option>
        <?php foreach($secciones as $key => $seccion) { ?>
          <option value="<?php echo $key ?>"><?php echo $seccion; ?></option>
        <?php } ?>
      </select>
      <input placeholder="Ingresar Número..." class="int-form__number inline-input" type="text">

      <div class="btn-wrapper">
        <span class="btn btn-submit" data-action="create">Crear Interpretación</span>
        <span class="btn btn-submit" data-action="load">Incorporar a la Carta</span>
      </div>

    </div>
    <?php
  }
}
