<!-- Start Review card -->
<div class="<?php echo $card_classes ?>"> <?php

	if($ekit_review_card_top_right_logo == 'yes') { ?>
        <!-- Top right logo -->
        <div class="ekit-review-card--top-right-logo">
            <?php
                $migrated = isset( $settings['__fa4_migrated']['controls_section_top_right_logo_icons'] );
                $is_new = empty( $controls_section_top_right_logo_icon );
                if ( $is_new || $migrated ) :
                    \Elementor\Icons_Manager::render_icon( $controls_section_top_right_logo_icons, [ 'aria-hidden' => 'true'] );
                else : ?>
                    <i class="<?php echo esc_attr( $controls_section_top_right_logo_icon ); ?>" aria-hidden="true"></i>
                <?php endif;
            ?>
        </div> <?php
	} ?>

    <!-- Start Thumbnail -->
    <div class="ekit-review-card--thumbnail <?php echo($thumbnail_badge ? 'ekit-review-card--thumbnail-badge' : '') ?>">
        <div>
            <img class="thumbnail" src="<?php echo $this->get_user_thumbnail($item->user->image_url) ?>" alt="Yelp Thumbnail"> <?php

            if($thumbnail_badge) { ?>
                <div class="badge">
                    <i aria-hidden="true" class="fab fa-yelp"></i>
                </div> <?php
            } ?>
        </div>
    </div>
    <!-- End Thumbnail -->

    <h5 class="ekit-review-card--name">
		<?php echo empty($item->user->name) ? esc_html__('Anonymous', 'elementskit') : $item->user->name; ?>
    </h5>
    <p class='ekit-review-card--date small muted'><?php echo date('d M, Y', $time); ?></p>

    <!-- Start Rating stars -->
    <div class="ekit-review-card--stars">
        <?php echo $this->get_stars_rating($item->rating) ?>
    </div>
    <!-- End Rating stars -->

    <p class="ekit-review-card--comment">
        <?php echo $format_comment
            ? $this->get_formatted_text($item->text, true)
            : esc_html( $item->text );
        ?>
    </p>

	<?php

    if($ekit_review_card_posted_on == 'yes') { ?>

        <div class="ekit-review-card--posted-on">
            <?php 
                $migrated = isset( $settings['__fa4_migrated']['ekit_yelp_review_posted_on_icons'] );
                $is_new = empty( $ekit_yelp_review_posted_on_icon );
                if ( $is_new || $migrated ) :
                    \Elementor\Icons_Manager::render_icon( $ekit_yelp_review_posted_on_icons, [ 'aria-hidden' => 'true'] );
                else : ?>
                    <i class="<?php echo esc_attr( $ekit_yelp_review_posted_on_icon ); ?>" aria-hidden="true"></i>
                <?php endif;
            ?>
            <div>
                <p class="small muted">
                    <?php echo esc_html__('Posted on', 'elementskit')?>
                </p>
                <h5 class='text-bold'><?php echo esc_html__('Yelp', 'elementskit')?></h5>
            </div>
        </div> <?php

    } ?>

</div>
<!-- End Review card -->
