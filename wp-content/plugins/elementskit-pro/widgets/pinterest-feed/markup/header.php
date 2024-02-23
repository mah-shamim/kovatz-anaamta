<?php 
    $thumbnail = empty($user['thumbnail']) 
        ? $handler_url . esc_html__('assets/images/profile-thumbnail.png', 'elementskit')
        : $user['thumbnail']; 

    $followers = !empty($user['followers'])
        ? $user['followers']
        : 0
?>

<!-- Start feed header -->
<div class="ekit-feed-header ekit-feed-header-pinterest">
    <!-- Start header left -->
    <div class="header-left">
        <!-- Start thumbnail -->
        <div class="ekit-feed-header--thumbnail">
            <img src="<?php echo esc_url($thumbnail); ?>" alt="Profile thumbnail">
        </div>
        <!-- End thumbnail -->
        <div>
            <h4 class='ekit-feed-header--name'>
                <?php echo esc_html($user['title']) ?>
            </h4>

            <!-- Start followers -->
            <div class='ekit-feed-header--desc'>
                <p>
                    <?php echo esc_html($followers) ?>
                    <?php echo esc_html__(' Followers', 'elementskit') ?>
                </p>
            </div>
            <!-- End followers -->

        </div>
    </div>
    <!-- End header left -->
    <div class="header-right">
        <div class="ekit-feed-header--actions">
            <a href="<?php echo esc_url($user['link']) ?>" target="_" class="btn btn-primary btn-pill">
                <div class='circle'>
                    <i class='icon icon-pinterest'></i>
                </div>
                <span>Follow</span>
            </a>
        </div>
    </div>
</div>
<!-- End feed header -->