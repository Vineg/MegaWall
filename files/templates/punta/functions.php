<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'name' => 'Top Sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="hx-style01"><span>',
        'after_title' => '</span></h4>',
    ));
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'name' => 'Middle Left Sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="hx-style01"><span>',
        'after_title' => '</span></h4>',
    ));
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'name' => 'Middle Right Sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="hx-style01"><span>',
        'after_title' => '</span></h4>',
    ));
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'name' => 'Bottom Sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="hx-style01"><span>',
        'after_title' => '</span></h4>',
    ));
remove_filter('term_description','wpautop');
?>