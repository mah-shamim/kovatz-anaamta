<?php 
namespace Elementor;
// todo: this file must be spilted into multiple view files for better maintance 
?>


<?php
                 ob_start(); ?>
                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>">
                            <?php if($ekit_blog_posts_title_trim !='' || $ekit_blog_posts_title_trim > 0):
                                echo \ElementsKit_Lite\Utils::trim_words(get_the_title(), $ekit_blog_posts_title_trim);
                            else:
                                the_title();
                            endif; ?>
                    </a>
                </h2>
                <?php $title_html = ob_get_clean();
            $meta_data_html = '';
            if ( 'yes' == $ekit_blog_posts_meta ):
                ob_start(); ?>
                <?php if($ekit_blog_posts_meta == 'yes' && $ekit_blog_posts_meta_select != '') : ?>
                <div class="post-meta-list">
                    <?php foreach($ekit_blog_posts_meta_select as $meta): ?>
                        <?php if($meta == 'author'): ?>
                            <span class="meta-author">
                                <?php if( 'yes' == $ekit_blog_posts_author_image): ?>
                                    <span class="author-img">
                                        <?php echo get_avatar( get_the_author_meta( "ID" )); ?>
                                    </span>
                                <?php else: ?>

                                    <?php
                                        // new icon
                                        $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_author_icons'] );
                                        // Check if its a new widget without previously selected icon using the old Icon control
                                        $is_new = empty( $settings['ekit_blog_posts_meta_author_icon'] );
                                        if ( $is_new || $migrated ) {
                                            // new icon
                                            Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_author_icons'], [ 'aria-hidden' => 'true'] );
                                        } else {
                                            ?>
                                            <i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_author_icon']); ?>" aria-hidden="true"></i>
                                            <?php
                                        }
                                    ?>

                                <?php endif; ?>
                                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author-name"><?php the_author_meta('display_name'); ?></a>
                            </span>
                        <?php endif; ?>
                        <?php if($meta == 'date'): ?>
                            <span class="meta-date">

                                <?php
                                    // new icon
                                    $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_date_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $settings['ekit_blog_posts_meta_date_icon'] );
                                    if ( $is_new || $migrated ) {
                                        // new icon
                                        Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_date_icons'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_date_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>

                                <span class="meta-date-text">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </span>
                            </span>
                        <?php endif; ?>
                        <?php if($meta == 'category'): ?>
                            <span class="post-cat">

                                <?php
                                    // new icon
                                    $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_category_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $settings['ekit_blog_posts_meta_category_icon'] );
                                    if ( $is_new || $migrated ) {
                                        // new icon
                                        Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_category_icons'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_category_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>

                                <?php echo get_the_category_list( ' | ' ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if($meta == 'comment'): ?>
                            <span class="post-comment">

                                <?php
                                    // new icon
                                    $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_comment_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $settings['ekit_blog_posts_meta_comment_icon'] );
                                    if ( $is_new || $migrated ) {
                                        // new icon
                                        Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_comment_icons'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_comment_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>

                                <a href="<?php comments_link(); ?>"><?php echo esc_html( get_comments_number() ); ?></a>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php
                $meta_data_html .= ob_get_clean();
            endif;


            $column_size = self::format_colname($column_size);
            // $ekit_blog_posts_column = self::format_colname($ekit_blog_posts_column);
			$ekit_blog_posts_column = $ekit_blog_posts_enable_carousel === 'yes' ? 'swiper-slide' : self::format_colname($ekit_blog_posts_column);
            ?>
            <div class="<?php echo esc_attr( $ekit_blog_posts_column ); ?>">

                <?php if ( 'elementskit-blog-block-post' == $ekit_blog_posts_layout_style ): ?>
                    <div class="<?php echo esc_attr( $ekit_blog_posts_layout_style ); ?>">
                        <div class="row no-gutters">
                            <?php if ( 'yes' == $ekit_blog_posts_feature_img && has_post_thumbnail() ): ?>
                                <div class="<?php echo esc_attr( $column_size.' '.$img_order ); ?>">
                                    <a href="<?php the_permalink(); ?>" class="elementskit-entry-thumb">
                                        <img src="<?php the_post_thumbnail_url( esc_attr( $ekit_blog_posts_feature_img_size_size ) ); ?>" alt="<?php the_title(); ?>">
                                    </a><!-- .elementskit-entry-thumb END -->
                                </div>
                            <?php endif; ?>

                            <div class="<?php echo esc_attr( $column_size.' '.$content_order ); ?>">
                                <div class="elementskit-post-body <?php echo esc_attr($highlight_border); ?>">
                                    <div class="elementskit-entry-header">
                                        <?php if ( 'yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                        <?php endif; ?>

                                            <?php if ('after_content' != $ekit_blog_posts_title_position ): ?>
                                                <?php echo $meta_data_html;  ?>
                                            <?php endif; ?>

                                            <?php if ('yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                            <?php endif; ?>

                                            <?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                            <?php endif; ?>
                                    </div><!-- .elementskit-entry-header END -->

                                    <?php if ( 'yes' == $ekit_blog_posts_content ): ?>
                                        <div class="elementskit-post-footer">
                                            <?php if($ekit_blog_posts_content_trim !='' || $ekit_blog_posts_content_trim > 0): ?>
                                                <p><?php echo \ElementsKit_Lite\Utils::trim_words(get_the_excerpt(), $ekit_blog_posts_content_trim); ?></p>
                                            <?php else: ?>
                                                <?php the_excerpt(); ?>
                                            <?php endif; ?>
                                            <?php if ( 'after_content' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo $meta_data_html;  ?>
                                            <?php endif; ?>
                                        </div><!-- .elementskit-post-footer END -->
                                    <?php endif; ?>
                                </div><!-- .elementskit-post-body END -->
                            </div>
                        </div>
                    </div><!-- .elementskit-blog-block-post .radius .gradient-bg END -->
                <?php else: ?>
                    <div class="<?php echo esc_attr( $ekit_blog_posts_layout_style ); ?>">
                        <div class="elementskit-entry-header">
                            <?php if ( 'elementskit-post-image-card' == $ekit_blog_posts_layout_style && 'yes' == $ekit_blog_posts_feature_img && has_post_thumbnail() ): ?>
                                <a href="<?php the_permalink(); ?>" class="elementskit-entry-thumb">
                                    <img src="<?php the_post_thumbnail_url( esc_attr( $ekit_blog_posts_feature_img_size_size ) ); ?>" alt="<?php the_title(); ?>">
                                </a><!-- .elementskit-entry-thumb END -->
                                <?php if('yes' == $settings['ekit_blog_posts_floating_date']) : ?>
                                <?php if($ekit_blog_posts_floating_date_style == 'style1'): ?>
                                    <div class="elementskit-meta-lists">
                                        <div class="elementskit-single-meta"><span class="elementskit-meta-wraper"><strong><?php echo get_the_date( 'd' );?></strong><?php echo get_the_date( 'M' );?></span></div>
                                    </div>
                                <?php elseif($ekit_blog_posts_floating_date_style == 'style2'): ?>
                                    <div class="elementskit-meta-lists elementskit-style-tag">
                                        <div class="elementskit-single-meta <?php echo esc_attr($settings['ekit_blog_posts_floating_date_triangle_position_alignment']); ?>"><span class="elementskit-meta-wraper"><strong><?php echo get_the_date( 'd' );?></strong><?php echo get_the_date( 'M' );?></span></div>
                                    </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if('yes' == $settings['ekit_blog_posts_floating_category']) : ?>
                                <div class="elementskit-meta-categories">
                                    <span class="elementskit-meta-wraper">
                                        <span><?php echo get_the_category_list( '</span><span>' ); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ( 'elementskit-post-card' == $ekit_blog_posts_layout_style):
                                    if('yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>

                                        <?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
                                            <span class="elementskit-border-hr"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ( 'after_content' != $ekit_blog_posts_title_position ): ?>
                                        <?php echo $meta_data_html; ?>
                                    <?php endif; ?>

                                    <?php if ( 'yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>

                                        <?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
                                            <span class="elementskit-border-hr"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>

                                        <?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
                                            <span class="elementskit-border-hr"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                        </div><!-- .elementskit-entry-header END -->

                        <div class="elementskit-post-body <?php echo esc_attr($highlight_border); ?>">
                            <?php if ( 'elementskit-post-image-card' == $ekit_blog_posts_layout_style):
                                        if ('yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                        <?php endif; ?>

                                        <?php if ( 'after_content' != $ekit_blog_posts_title_position ): ?>
                                        <?php echo $meta_data_html;  ?>
                                        <?php endif; ?>

                                        <?php if ( 'yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                        <?php endif; ?>

                                        <?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
                                        <?php echo \ElementsKit_Lite\Utils::kses($title_html);  ?>
                                        <?php endif; ?>
                                <?php endif; ?>
                            <?php if ( 'yes' == $ekit_blog_posts_content ): ?>
                                <?php if($ekit_blog_posts_content_trim !='' || $ekit_blog_posts_content_trim > 0): ?>
                                        <p><?php echo \ElementsKit_Lite\Utils::trim_words(get_the_excerpt(), $ekit_blog_posts_content_trim); ?></p>
                                    <?php else: ?>
                                        <?php the_excerpt(); ?>
                                    <?php endif; ?>
                            <?php endif; ?>
                            <?php if ( 'after_content' == $ekit_blog_posts_title_position ): ?>
                                    <?php echo $meta_data_html;  ?>
                                <?php endif; ?>
                            <?php
                            if($ekit_blog_posts_read_more == 'yes'):
                                $btn_text = $settings['ekit_blog_posts_btn_text'];
                                $btn_class = ($settings['ekit_blog_posts_btn_class'] != '') ? $settings['ekit_blog_posts_btn_class'] : '';
                                $btn_id = ($settings['ekit_blog_posts_btn_id'] != '') ? 'id='.$settings['ekit_blog_posts_btn_id'] : '';
                                $icon_align = $settings['ekit_blog_posts_btn_icon_align'];
                                
                                // Reset Whitespace for this specific widget
                                $btn_class .= ' whitespace--normal';
                                ?>
                                <div class="btn-wraper">
                                    <?php if($icon_align == 'right'): ?>
                                        <a href="<?php the_permalink(); ?>" class="elementskit-btn <?php echo esc_attr( $btn_class ); ?>" <?php echo esc_attr($btn_id); ?>>
                                            <?php echo esc_html( $btn_text ); ?>
                                            <?php if($settings['ekit_blog_posts_btn_icons__switch'] === 'yes'): 

                                                // new icon
                                                $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_btn_icons'] );
                                                // Check if its a new widget without previously selected icon using the old Icon control
                                                $is_new = empty( $settings['ekit_blog_posts_btn_icon'] );
                                                if ( $is_new || $migrated ) {
                                                    // new icon
                                                    Icons_Manager::render_icon( $settings['ekit_blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                                } else {
                                                    ?>
                                                    <i class="<?php echo esc_attr($settings['ekit_blog_posts_btn_icon']); ?>" aria-hidden="true"></i>
                                                    <?php
                                                }
                                                
                                                endif; ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if($icon_align == 'left'): ?>
                                        <a href="<?php the_permalink(); ?>" class="elementskit-btn <?php echo esc_attr( $btn_class ); ?>" <?php echo esc_attr($btn_id); ?>>
                                        <?php if($settings['ekit_blog_posts_btn_icons__switch'] === 'yes'): 
                                                // new icon
                                                $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_btn_icons'] );
                                                // Check if its a new widget without previously selected icon using the old Icon control
                                                $is_new = empty( $settings['ekit_blog_posts_btn_icon'] );
                                                if ( $is_new || $migrated ) {
                                                    // new icon
                                                    Icons_Manager::render_icon( $settings['ekit_blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                                } else {
                                                    ?>
                                                    <i class="<?php echo esc_attr($settings['ekit_blog_posts_btn_icon']); ?>" aria-hidden="true"></i>
                                                    <?php
                                                }
                                                
                                            endif; ?>
                                            <?php echo esc_html( $btn_text ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div><!-- .elementskit-post-body END -->
                    </div>
                <?php endif; ?>

            </div>