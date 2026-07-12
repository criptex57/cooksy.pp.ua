<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="section">
  <div class="container">
    <h1><?php bloginfo('name'); ?></h1>
    <p><?php bloginfo('description'); ?></p>
  </div>
</section>
<?php
get_footer();
