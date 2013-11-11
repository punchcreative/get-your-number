<?php

/**
 * function used during development
 * http://wordpress.stackexchange.com/questions/145/how-do-you-debug-plugins
*/
function tj( $code ) {

    ?>
    <style>
        .tj_debug { word-wrap: break-word; white-space: pre; text-align: left; position: relative; background-color: rgba(0, 0, 0, 0.8); font-size: 11px; color: #a1a1a1; margin: 10px; padding: 10px; margin: 0 auto; width: 80%; overflow: auto; -moz-box-shadow:0 10px 40px rgba(0, 0, 0, 0.75); -webkit-box-shadow:0 10px 40px rgba(0, 0, 0, 0.75); -moz-border-radius: 5px; -webkit-border-radius: 5px; text-shadow: none; }
    </style>
    <br /><pre class="tj_debug">

    <?php
    if ( is_null( $code ) || is_string($code) || is_int( $code ) || is_bool($code) || is_float( $code ) ) :
        var_dump( $code );

    else :
        print_r( $code );

    endif;

    echo '</pre><br />';

}

function tj_log( $code ) {

    if ( is_null( $code ) || is_string($code) || is_int( $code ) || is_bool($code) || is_float( $code ) ) :
        $code = var_export( $code, true );

    else :
        $code = print_r( $code, true );

    endif;

    error_log( $code );

}

?>