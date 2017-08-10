<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.0
 */ ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?><?php wpex_schema_markup( 'html' ); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<!-- Begin Body -->
<body <?php body_class(); ?>>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45035437-1', 'auto');
  ga('send', 'pageview');

</script>
<?php wpex_outer_wrap_before(); ?>

<div id="outer-wrap" class="clr">

	<?php wpex_hook_wrap_before(); ?>

	<div id="wrap" class="clr">

		<?php wpex_hook_wrap_top(); ?>

		<?php wpex_hook_main_before(); ?>

		<main id="main" class="site-main clr"<?php wpex_schema_markup( 'main' ); ?>>

			<?php wpex_hook_main_top(); ?>