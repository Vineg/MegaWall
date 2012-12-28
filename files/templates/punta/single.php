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
                

<?php the_content('Read the rest of this entry &raquo;'); ?>

<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>

	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
            
                </div> <!-- /page-content -->

<?php comments_template(); ?>

  <?php endwhile; else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
            </div> <!-- /page -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>