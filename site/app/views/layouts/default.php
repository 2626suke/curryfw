<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<?php foreach ($metas as $attr => $meta) { ?>
<?php   foreach ($meta as $key => $content) { ?>
<meta <?php echo $attr; ?>="<?php echo $key; ?>" content="<?php echo $content; ?>" />
<?php   } ?>
<?php } ?>
<?php foreach ($javascripts as $js) { ?>
<script type="text/javascript" src="<?php echo $request['base_path']; ?>/js/<?php echo $js; ?>"></script>
<?php } ?>
<?php foreach ($stylesheets as $css) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $request['base_path']; ?>/css/<?php echo $css; ?>" />
<?php } ?>
<title><?php if ($page_title != '') { echo $page_title; } ?></title>
</head>
<body>

<?php echo $inner_contents; ?>

</body>
</html>
