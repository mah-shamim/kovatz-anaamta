<?php
$rating         = intval($page_Info['rating']);
$page_url       = esc_url('https://yelp.com/') . $page_Info['pgt'] . esc_html__('/reviews', 'elementskit' );
$page_thumbnail = !empty($page_Info['picture']) ? $page_Info['picture'] : $handler_url . esc_html__('assets/images/gift-box.png', 'elementskit' );
?>

<div class='ekit-review-card ekit-review-card-yelp ekit-review-card-overview'>

    <!-- Start Top right logo -->
	<?php

	if($ekit_review_card_top_right_logo == 'yes') { ?>
        <div class="ekit-review-card--top-right-logo">
            <img src="<?php echo esc_url( $handler_url . 'assets/svg/yelo-logo-circle.svg' ); ?>" alt="Yelp right logo">
        </div> <?php

	} ?>
    <!-- End Top right logo -->


    <!-- Start Overview Image -->
    <div class="ekit-review-card--image">
        <img class="thumbnail" src="<?php echo esc_url( $page_thumbnail ); ?>" alt="Yelp Thumbnail">
    </div>
    <!-- Start Overview Image -->


    <h5 class="ekit-review-card--name">
		<?php echo esc_html( $page_Info['pg_name'] ); ?>
    </h5>

    <span class="ekit-review-card--average">
        <?php echo empty($page_Info['rating']) ? '' : $page_Info['rating'] ?>
    </span>

    <!-- Start Rating stars -->
    <div class="ekit-review-card--stars">
        <?php echo $this->get_stars_rating($rating); ?>
    </div>
    <!-- End Rating stars -->

    <p class="ekit-review-card--desc small muted">
        <?php echo esc_html__('Based on ', 'elementskit')?>
        <?php echo intval($page_Info['count']) ?>
        <?php echo esc_html__(' reviews', 'elementskit')?>
    </p>

    <div class="ekit-review-card--actions">
        <a href="<?php echo esc_url( $page_url ); ?>" target='_' class="btn">
            <?php echo esc_html__('See all reviews', 'elementskit')?>
        </a>
        <a href="<?php echo esc_url( $page_url ); ?>" target='_' class="btn">
            <?php echo esc_html__('Write a review', 'elementskit')?>
        </a>
    </div>

</div>