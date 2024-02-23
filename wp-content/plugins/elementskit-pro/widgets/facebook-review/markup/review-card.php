<?php 

    $user_thumbnail = !empty($item->reviewer->id)
        ? Elementor\ElementsKit_Widget_Facebook_Review_Handler::get_user_profile_image_url($item->reviewer->id, $pg_tok)
        : $handler_url . esc_html__('assets/images/profile-placeholder.jpg', 'elementskit');

?>

<!-- Start Review card -->
<div class="<?php echo esc_attr($card_classes) ?>"> <?php

	if($ekit_review_card_top_right_logo == 'yes') { ?>
        <!-- Top right logo -->
        <div class="ekit-review-card--top-right-logo">
            <?php \Elementor\Icons_Manager::render_icon( 
                    $controls_section_top_right_logo_icons, [ 
                        'aria-hidden' => 'true'
                    ] 
            )?>
        </div> <?php
	} ?>

    <!-- Start Thumbnail -->
    <div class="ekit-review-card--thumbnail <?php echo($thumbnail_badge ? esc_html__('ekit-review-card--thumbnail-badge', 'elementskit') : '') ?>">
        <div>
            <img class="thumbnail" src="<?php echo $user_thumbnail ?>" />
            <?php if($thumbnail_badge) { ?>
                <div class="badge">
                    <img src="<?php echo $handler_url . esc_html__('assets/svg/fb-logo-f.svg', 'elementskit') ?>">
                </div> <?php
            } ?>
        </div>
    </div>
    <!-- End Thumbnail -->

    <h5 class="ekit-review-card--name">
		<?php echo empty($item->reviewer->name) ? esc_html__('Anonymous', 'elementskit') : $item->reviewer->name; ?>
    </h5>
    <p class='ekit-review-card--date small muted'><?php echo date('d M, Y', $time ); ?></p>

    <!-- Start Rating stars -->
    <div class="ekit-review-card--stars">
        <i class='icon icon-star-1'></i>
        <i class='icon <?php echo esc_attr($star_icon) ?>'></i>
        <i class='icon <?php echo esc_attr($star_icon) ?>'></i>
        <i class='icon <?php echo esc_attr($star_icon) ?>'></i>
        <i class='icon <?php echo esc_attr($star_icon) ?>'></i>
    </div>
    <!-- End Rating stars -->

    <?php
        if( isset($ekit_review_card_align_center) && $ekit_review_card_align_center === 'yes' ) {
            $comment_text_align = ' ekit-review-card-align-center';
        }
    ?>

    <p class="ekit-review-card--comment <?php echo esc_attr($comment_text_align); ?>">
        <?php 
        
        $txt = empty($item->review_text) ? '' : esc_html__($item->review_text, 'elementskit');

        echo $format_comment
            ? $this->get_formatted_text($txt, true)
            : $txt;
        ?>
    </p>

	<?php

    if($ekit_review_card_posted_on == 'yes') { ?>

        <div class="ekit-review-card--posted-on">
            <?php \Elementor\Icons_Manager::render_icon( 
                $ekit_fb_review_posted_on_icons, [ 
                    'aria-hidden' => 'true'
                ]
            )?>
            <div>
                <p class="small muted"><?php echo esc_html__('Posted on', 'elementskit')?></p>
                <h5 class='text-bold'><?php echo esc_html__('Facebook', 'elementskit')?></h5>
            </div>
        </div> <?php

    } ?>

</div>
<!-- End Review card -->
