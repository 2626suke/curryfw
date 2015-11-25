<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
{foreach from=$metas key=attr item=meta}
{foreach from=$meta key=key item=content}
<meta {$attr}="{$key}" content="{$content}" />
{/foreach}
{/foreach}
{foreach from=$javascripts item=js}
<script type="text/javascript" src="{$request.base_path}/js/{$js}"></script>
{/foreach}
{foreach from=$stylesheets item=css}
<link rel="stylesheet" type="text/css" href="{$request.base_path}/css/{$css}" />
{/foreach}
<title>{if $page_title != ''}{$page_title}{/if}</title>
</head>
<body>

{$inner_contents}

</body>
</html>