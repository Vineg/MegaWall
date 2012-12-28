<?php print "<?xml version=\"1.0\" ?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
    <!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/main-msie.css" /><![endif]-->
    <link rel="stylesheet" media="screen,projection" type="text/css" href="<?php bloginfo('template_url'); ?>/<?php include (TEMPLATEPATH . '/config.php'); print $color; ?>.css" />
    <link rel="stylesheet" media="print" type="text/css" href="<?php bloginfo('template_url'); ?>/print.css" />

    <title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>


<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
</head>

<body>

<div id="main">

    <!-- Header -->
    <div id="header" class="box">

        <!-- Your logo -->
        <h1 id="logo"><a href="<?php print get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
        
        <!-- Your slogan -->
        <?php if ($tagline == 1) { ?><p id="slogan"><?php bloginfo('description'); ?></p><?php } ?>

        <hr class="noscreen" />

        <!-- Hidden navigation -->
        <p class="noscreen noprint"><em>Quick links: <a href="#content">content</a>, <a href="#nav">navigation</a>, <a href="#search">search</a>.</em></p>

        <hr class="noscreen" />

        <!-- Search -->
        <div id="search">
            <form action="<?php bloginfo('url'); ?>/" method="get">
                <div>
                    <span class="noscreen">Fulltext:</span>
                    <input type="text" size="30" value="<?php the_search_query(); ?>" name="s" id="search-input" />
                    <input type="submit" value="Search" id="search-submit" />
            	</div>
            </form>
        </div> <!-- /search -->
        
    </div> <!-- /header -->

    <hr class="noscreen" />

    <!-- Navigation -->
    <div id="nav" class="box">
    
        <h3 class="noscreen">Navigation</h3>        

        <ul>
<li<?php if ( is_home() or is_archive() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { print ' class="current_page_item"'; } ?>><a href="<?php print get_option('home'); ?>">Home</a></li>
           <?php wp_list_pages('title_li=&depth=1'); ?>
        </ul>
    
    </div> <!-- /nav -->
    
    <hr class="noscreen" />

    <!-- Main content -->
    <div id="content" class="box">

        <div class="box">