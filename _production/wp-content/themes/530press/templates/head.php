<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title itemprop="name"><?php wp_title('|', true, 'right'); ?></title>

  <!-- DNS prefetch -->
  <link href="//www.google-analytics.com" rel="dns-prefetch">
  <link href="//ajax.googleapis.com" rel="dns-prefetch">

  <!-- Bookmark Icons -->
  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-57x57.png">

  <link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-60x60.png">

  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-72x72.png">

  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-76x76.png">

  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-114x114.png">

  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-120x120.png">

  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-144x144.png">

  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-152x152.png">

  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/apple-touch-icon-180x180.png">

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/favicon-32x32.png" sizes="32x32">

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/favicon-194x194.png" sizes="194x194">

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/favicon-96x96.png" sizes="96x96">

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/android-chrome-192x192.png" sizes="192x192">

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/favicon-16x16.png" sizes="16x16">

  <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/manifest.json">

  <meta name="msapplication-TileColor" content="#00aba9">

  <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/lib/images/favicons/mstile-144x144.png">

  <meta name="theme-color" content="#ffffff">


  <?php wp_head(); ?>

  <!--[if (lt IE 9) & (!IEMobile)]>
    <link rel="stylesheet" href="lib/styles/css/ie-min.css" />
  <![endif]-->

  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">


</head>
