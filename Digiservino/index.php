<?php
// This file is required for WordPress to recognize the theme.
// The actual homepage is handled by front-page.php.
get_header();
?>

<div class="py-20 text-center">
    <?php 
    if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif; 
    ?>
</div>

<?php get_footer(); ?>