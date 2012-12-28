<?php get_header(); ?>

            <!-- Page (left column) -->
            <div id="page">
                      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div id="page-title">

                    <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
                    
                    <p class="date">
                        <?php the_time('l, m. j. Y'); ?> &nbsp;&ndash;&nbsp; 
                        Category: <?php the_category(', ') ?>                                                            </p>
             </div> <!-- /page-title -->
                
                <div id="page-content">
                
                    <p><!-- <img src="<?php bloginfo('stylesheet_directory'); ?>/tmp/image.gif" width="200" height="150" alt="" class="f-left" /> -->

<?php if (is_search() OR is_archive()) { the_excerpt(); } else { the_content('Read the rest of this entry &raquo;'); } ?></p>
            
                </div> <!-- /page-content -->

  <?php endwhile; ?>

			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>

	<?php else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
            </div> <!-- /page -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>