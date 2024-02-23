<?php
/**
 * Lite bar template
 *
 * @version 2.4.8
 * @since   2.4.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="wcv-admin-lite-bar" class="wcv-dismiss-container">
    <div class="wcv-admin-lite-bar-msg">
        <p>
            <?php
            echo wp_kses(
                $message,
                array(
                    'a' => array(
                        'href'   => array(),
                        'target' => array(),
                    ),
                )
            );
            ?>
        </p>
    </div>
</div>
