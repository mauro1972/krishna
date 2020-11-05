<?php
/**/
require_once( 'classes/class-chart-content.php' );
require_once( 'widgets/chart-widget.php' );
require_once( 'widgets/pronostico-widget.php' );
require_once( 'classes/class-chart.php' );
require_once( 'classes/class-numero-alma.php' ); 
require_once( 'classes/class-personalidad-externa.php' ); 
require_once( 'classes/class-leccion-vida.php' );
require_once( 'classes/class-via-destino.php' );
require_once( 'classes/class-numero-fuerza.php' );
require_once( 'classes/class-balance-vida.php' );
require_once( 'classes/class-edad-mas-importante.php' );
require_once( 'classes/class-desafios.php' );
require_once( 'classes/class-pinaculos.php' );
require_once( 'classes/class-misc-numbers.php' );
require_once( 'rest-api-charts.php' ); 

require_once( 'classes/class-pronostico-anual.php' ); 

if ( ! function_exists( 'charts_scripts' ) ) :

  function charts_scripts() {
    
    wp_enqueue_style('chartstyle', get_stylesheet_directory_uri() .'/charts/css/style.css', array(), microtime(). 'all');

    wp_enqueue_script( 'charts', get_stylesheet_directory_uri() . '/charts/js/chartsController.js', array( 'jquery' ), microtime(), true );

    //wp_enqueue_script( 'pro', get_stylesheet_directory_uri() . '/charts/js/interpretacionController.js', array( 'jquery' ), microtime(), true );


    wp_localize_script('charts', 'chartsData', array(
      'root_url' => home_url( '' ) ,
      'nonce' => wp_create_nonce('wp_rest')
    ));

  }

endif;

add_action( 'wp_enqueue_scripts', 'charts_scripts' );

function add_type_attribute($tag, $handle, $src) {
  // if not your script, do nothing and return original $tag
  if ( 'charts' !== $handle ) {
      return $tag;
  }
  // change the script tag by adding type="module" and return it.
  $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
  return $tag;
}

add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);

$chart_content = new Chart_Content();
$chart_content->chart_content_filter(); 

    





