# CustomizerBuilder

Build Wordpress Customizer Panels, Section and Controls with a fluid interface.

### Installation


#### Composer

```shell
$ composer require symbioticwp/customizer-builder
```

#### Requirements

* [PHP](http://php.net/manual/en/install.php) >= 7.0

### Setup


Enable dynamic class include ([PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/))
via composer autoloader. (Hint: Check first if you don't already have the following 
snippet in your theme)

````php
<?php
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}
````

Always use the [customize_register](https://developer.wordpress.org/reference/hooks/customize_register/) 
hook to add your customizer fields.


#### Example
functions.php
```php
<?php
use Symbiotic\Customizer\CustomizerBuilder;

add_action("customize_register", function($wp_customize)
{
	$builder = new CustomizerBuilder($wp_customize);

	$builder->newPanel("post_types", "Post Types", function() use ($builder) {
		$builder->addSection("Posts", "Header", function() use ($builder) {
			$builder->addTextBox("post_title", "Post Title");
			$builder->addSelect("post_style", "Post Style", [
				"default" => esc_attr__("Default"),
				"grid" => esc_attr__("Grid"),
				"masonry" => esc_attr__("Masonry"),
			]);
		});
		$builder->addSection("Portfolio", "Header", function() use ($builder) {
			$builder->addTextBox("portfolio_title", "Portfolio Title");
			$builder->addSelect("portfolio_style", "Portfolio Style", [
				"default" => esc_attr__("Default"),
				"grid" => esc_attr__("Grid"),
				"masonry" => esc_attr__("Masonry"),
			]);
		});
	});
});
```

Now you can use the field values in your theme

```php
<h1><?= get_theme_mod('post_title') ?></h1>
<?php get_template_part('template-parts/post/loop', get_theme_mod('post_style')); ?>
```

### How-To


### Disclaimer

This repo is a fork and was originally created by [SjorsO/wordpress-customizer-builder](https://github.com/SjorsO/wordpress-customizer-builder)

### Changelog

##### 1.0.0

* [Select2](https://github.com/select2/select2) Field added 
* Added Namespacing 
* Added composer.json
* Prio => 0