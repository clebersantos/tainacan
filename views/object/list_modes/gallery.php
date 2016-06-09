<div id="gallery-viewMode" class="col-md-12 no-padding">
    <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++;
        $columns_count = ( $countLine % 4);
        //if ($columns_count == 1) { echo "<div class='row' style='margin: 0'>"; }
        ?>
        <div class="col-md-3 gallery-view-container">
            <div class="row">
                <div class="item-thumb">
                    <a href="<?php echo get_collection_item_href($collection_id); ?>"
                       onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                        <?php echo get_item_thumb_image(get_the_ID()); ?>
                    </a>
                </div>
            </div>
            <div class="row title-container">
                <h4 class="item-display-title">
                    <a href="<?php echo get_collection_item_href($collection_id); ?>"
                       onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                        <?php the_title(); ?>
                    </a>
                </h4>
            </div>

            <?php // if ($columns_count == 0) { echo "</div>"; } // closes .row div ?>
        </div>
    <?php endwhile; ?>
</div>
