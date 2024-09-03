<?php
// DEBUG: serve images.
if (preg_match('/\.(?:svg|js|css|png|jpe?g|webp|avif|gif)$/', $_SERVER['REQUEST_URI'])) {
	return false;
}

$app_path    = rtrim(dirname($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR);
$path        = rtrim($_SERVER['REQUEST_URI'], DIRECTORY_SEPARATOR);
$images_path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];

// Default configuration.
$images        = glob("$images_path/*.{jpg,png,gif,webp,avif}", GLOB_BRACE);
$lazy_loading  = true;
$preview       = $images[0] ?? null;
$theme         = 'default';
$theme_variant = '';
$title         = 'My photos';

// Configuration overrides.
@include("$images_path/config.php");

$images_count = count($images);
$theme_path   = "$app_path/themes/$theme";
$icons_path   = "$theme_path/icons.svg";
$style_path   = "$theme_path/style.css";
$preview_url  = $preview ?
                rtrim($_SERVER['REQUEST_URI'], DIRECTORY_SEPARATOR) .
                DIRECTORY_SEPARATOR .
                basename($preview) : '';
?>
<!DOCTYPE html>
<html data-theme-variant="<?= $theme_variant ?>">
<head>
	<title><?= $title ?></title>
	<meta charset="utf-8">
	<meta name="generator" content="https://github.com/cisoun/Expo">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta property="og:image" content="<?= $preview_url ?>" />
	<link rel="stylesheet" type="text/css" href="<?= $style_path ?>">
</head>
<body>
	<div id="modal">
		<img id="image" src="" />
		<div id="controls">
			<a><svg><use href="<?= $icons_path ?>#previous"/></svg></a>
			<a><svg><use href="<?= $icons_path ?>#close"/></svg></a>
			<a><svg><use href="<?= $icons_path ?>#next"/></svg></a>
		</div>
	</div>
	<div id="container">
	<div id="grid">
		<?php foreach ($images as $i): ?>
		<img src="<?= $path . '/' . basename($i) ?>" <?= getimagesize($i)[3] ?> <?php if($lazy_loading): ?>loading="lazy"<?php endif ?>/>
		<?php endforeach; ?>
	</div>
	</div>
	<script type="text/javascript">
	let current, touchX;
	const show     = e => image.src = (current = e).src;
	const next     = _ => show(current.nextElementSibling     ?? grid.firstElementChild);
	const previous = _ => show(current.previousElementSibling ?? grid.lastElementChild);
	const reveal   = e => e.srcElement.classList.add('visible');
	const toggle   = e => {
		modal.classList.toggle('visible');
		show(e.srcElement);
	}
	Array.from(grid.children).map(e => {
		e.onclick = toggle;
		e.onload  = reveal;
	});
	document.onkeydown = e => {
		if      (e.keyCode == 37) previous();
		else if (e.keyCode == 39) next();
		else if (e.keyCode == 27) modal.classList.toggle('visible', false);
	}
	controls.children[0].onclick = e => { e.stopPropagation(); previous(); }
	controls.children[1].onclick = e => toggle(e);
	controls.children[2].onclick = e => { e.stopPropagation(); next(); }
	image.onclick               = e => toggle(e);
	modal.ontouchstart          = e => touchX = e.changedTouches[0].clientX;
	modal.ontouchend            = e => {
		if      (e.changedTouches[0].clientX - touchX > +50) previous();
		else if (e.changedTouches[0].clientX - touchX < -50) next();
	}
	</script>
</body>
</html>