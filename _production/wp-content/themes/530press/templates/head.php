<!doctype html>
<html class="no-js" <?php html_tag_schema(); ?> <?php language_attributes(); ?>>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title itemprop="name"><?php wp_title('|', true, 'right'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- DNS prefetch -->
  <link href="//www.google-analytics.com" rel="dns-prefetch">
  <link href="//ajax.googleapis.com" rel="dns-prefetch">

  <!-- Bookmark Icons -->
  <link rel="apple-touch-icon" sizes="57x57" href="lib/images/favicons/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="114x114" href="lib/images/favicons/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="72x72" href="lib/images/favicons/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="144x144" href="lib/images/favicons/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="60x60" href="lib/images/favicons/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="120x120" href="lib/images/favicons/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="76x76" href="lib/images/favicons/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="152x152" href="lib/images/favicons/apple-touch-icon-152x152.png">
  <link rel="icon" type="image/png" href="lib/images/favicons/favicon-196x196.png" sizes="196x196">
  <link rel="icon" type="image/png" href="lib/images/favicons/favicon-160x160.png" sizes="160x160">
  <link rel="icon" type="image/png" href="lib/images/favicons/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="lib/images/favicons/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="lib/images/favicons/favicon-32x32.png" sizes="32x32">
  <meta name="msapplication-TileColor" content="#000000">
  <meta name="msapplication-TileImage" content="lib/images/favicons/mstile-144x144.png">

  <?php wp_head(); ?>

  <!--[if (lt IE 9) & (!IEMobile)]>
    <link rel="stylesheet" href="lib/styles/css/ie-min.css" />
  <![endif]-->

  <!-- Above the fold styles -->
  <style>

    #body {
      background-image: url(<?php echo get_bloginfo('template_directory');?>/lib/images/min/head_bg.png);
      background-size: 1200px;
      background-repeat: no-repeat;
      background-position: 50% -7%;
    }

  </style>

  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">
</head>
