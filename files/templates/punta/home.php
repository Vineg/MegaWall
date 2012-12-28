<?php get_header(); include (TEMPLATEPATH . '/config.php'); ?>

    <div id="content" class="box">

        <div class="box">
        
            <!-- Top story (left column) -->
            <div id="topstory">
                
                <div id="topstory-title">

<?php $my_query = new WP_Query('showposts=1&cat='.$featured); while ($my_query->have_posts()) : $my_query->the_post(); $do_not_duplicate = $post->ID; ?>
                
                    <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
                    
                    <p class="date">
                        <?php the_time('l, m. j. Y'); ?> &nbsp;&ndash;&nbsp; 
                        Category: <?php the_category(', ') ?> &nbsp;&ndash;&nbsp;
                        <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?> 
                    </p>
<?php endwhile; ?>
                </div> <!-- /topstory-title -->
                
                <div id="topstory-perex">
                
                    <?php if ( get_post_meta($post->ID, 'topstory_image', true) ) { ?><p><img src="<?php bloginfo('template_url'); ?>/topstory-images/<?php print get_post_meta($post->ID, "topstory_image", $single = true); ?>" width="200" height="150" alt="" class="f-left" /><?php } ?></p>
<?php the_excerpt(); ?>
            
                </div> <!-- /topstory-perex -->
            
            </div> <!-- /topstory -->

            <hr class="noscreen" />

            <!-- Previous articles (right column) -->
            <div id="aside">
            
                <h4 class="hx-style01"><span>Previous articles</span></h4>

                <ul class="ul-list box">

<?php $my_query = new WP_Query('showposts=4&offset=1&cat='.$featured); while ($my_query->have_posts()) : $my_query->the_post(); if ( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); $do_not_duplicate = $post->ID; ?>

                    <li><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a><br /><span class="smaller low">(<?php the_time('l, m. j. Y'); ?> &ndash; <?php $cat = get_the_category(); $cat = $cat[0]; print $cat->cat_name; ?> &ndash; <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?>)</span></li>

<?php endwhile; ?>
                </ul>
                
                <p class="t-center nom"><a href="<?php print get_category_link(1);?>" class="ico-more">More articles</a></p>
            
            </div> <!-- /aside -->

        </div> <!-- /box -->                                    

<?php if (function_exists('get_flickrrss')) : ?>
        <hr class="noscreen" />

        <!-- Photos -->
        <h4 class="hx-style01"><span>Photos</span></h4>
        
        <div id="photos" class="box">
<?php get_flickrrss(); ?>
        </div> <!-- /photos -->           

        <p class="t-center"><a href="#" class="ico-more">More photos</a></p>

<?php endif; ?>
        
        <hr class="noscreen" />

        <!-- Other informations -->
        <h4 class="hx-style01"><span>More Articles</span></h4>

        <div class="box">
        
<?php /* THIS IS WHERE YOU INPUT THE ARRAY OF CATEGORY IDs */
	$cats = array(5,3,4,4,5,3);

      /* DO NOT EDIT BELOW */
       
	foreach($cats as $key => $catz)
	{
?>
            <div class="col30<?php
if (($key + 1) % 3 == 0)
{
    print " fix";
}
else if (($key + 1) % 3 == 2)
{
    print " margin";
}
?>">

                <h4><?php print(get_category_parents($catz, TRUE, '')); ?></h4>
            
                <p class="bb box"><img src="<?php bloginfo('template_url'); ?>/cat-images/cat-<?php print $catz; ?>.gif" width="100" height="75" alt="" class="f-left" />
                <?php print category_description($catz); ?></p>

                <ul class="ul-list box">

<?php /* EDIT THE showposts= NUMBER BELOW FOR MORE POSTS IN EACH CAT */

$my_query = new WP_Query('cat='.$catz.'&showposts=4'); while ($my_query->have_posts()) : $my_query->the_post(); if ( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); $do_not_duplicate = $post->ID; ?>
                    <li><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a><br /><span class="smaller low">(<?php the_time('l, m. j. Y'); ?> &ndash; <?php $cat = get_the_category(); $cat = $cat[0]; print $cat->cat_name; ?> &ndash; <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?>)</span></li>

<?php endwhile; ?>
                </ul>
                
                <p class="t-center"><a href="<?php print get_category_link($catz); ?>" class="ico-more">More from this category</a></p>
                
            </div> <!-- /col30 -->

            <hr class="noscreen" />

<?php if (($key + 1) % 3 == 0) { ?><div class="clear"></div><?php } ?>

<?php } ?>

        </div> <!-- /box -->

    </div> <!-- /content -->

<?php get_footer(); ?>