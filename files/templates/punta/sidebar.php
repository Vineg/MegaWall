            <hr class="noscreen" />

            <!-- Sidebar (right column) -->
            <div id="aside">

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Top Sidebar") ) : ?>
            
                <h4 class="hx-style01"><span>Featured articles</span></h4>

                <ul>
<?php include (TEMPLATEPATH . '/config.php'); $my_query = new WP_Query('showposts=4&cat='.$featured); while ($my_query->have_posts()) : $my_query->the_post(); if ( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); $do_not_duplicate = $post->ID; ?>

                    <li><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a><br /><span class="smaller low">(<?php the_time('l, m. j. Y'); ?> &ndash; <?php comments_number('No Comments', '1 Comment', '% Comments'); ?>)</span></li>

<?php endwhile; ?>
                </ul>

<?php endif; ?>
                
	<div class="halfright">

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Middle Right Sidebar") ) : ?>

		<h4 class="hx-style01"><span>Categories</span></h4>
		<ul>
			<?php wp_list_categories('title_li='); ?>
		</ul>


<?php wp_list_bookmarks('title_before=<h4 class="hx-style01"><span>&title_after=</span></h4>&category_before=&category_after='); ?>

<?php endif; ?>
	</div>
	<div class="halfleft">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Middle Left Sidebar") ) : ?>
		<h4 class="hx-style01"><span>Archives</span></h4>
		<ul>
			<?php wp_get_archives(); ?>
		</ul>

		<h4 class="hx-style01"><span>Meta</span></h4>
		<ul>
<?php wp_register(); ?>

					<li><?php wp_loginout(); ?></li>

					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>

					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>

					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>

					<?php wp_meta(); ?>
		</ul>
<?php endif; ?>
	</div>

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Bottom Sidebar") ) : ?>

<?php endif; ?>

            </div> <!-- /aside -->