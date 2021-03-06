{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	{if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css", true)}{/if}
	<link rel="stylesheet" type="text/css" href="{$css|code}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css')|code}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css", true)|code}"/>
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/highlightjs/highlight.pack.js")|code}"></script>
	<script type="module">
        // 载入LaTeX支持
        import { LaTeXJSComponent } from "{$PAGE->getTplUrl("js/latex.js/dist/latex.m.js")}";
        customElements.define("latex-js", LaTeXJSComponent);
    </script>
    <script>
		hljs.initHighlightingOnLoad();

		var hu60_loaded = false;
        function hu60_onload() {
            var div = document.querySelector('#hu60_load_notice');
            if (div) div.style.display = 'none';
            hu60_loaded = true;
        }
        function hu60_loading() {
            if (!hu60_loaded) {
                var div = document.querySelector('#hu60_load_notice');
                if (div) div.style.display = 'block';
            }
        }
        $(document).ready(function() {
            hu60_onload();
			{if $onload !== null}{$onload};{/if}
        });
        setTimeout(hu60_loading, 3000);

        MathJax = {
            options: {
                renderActions: {
					find: [10, function (doc) {
						for (const node of document.querySelectorAll('hu60-math')) {
							const math = new doc.options.MathItem(node.textContent, doc.inputJax[0], false);
							const text = document.createTextNode('');
							node.parentNode.replaceChild(text, node);
							math.start = {
                                node: text, delim: '', n: 0
                            };
							math.end = {
                                node: text, delim: '', n: 0
                            };
							doc.math.push(math);
						}
					}, '']
                }
            }
        };
	</script>
    <script id="MathJax-script" async src="{$PAGE->getTplUrl("js/mathjax/es5/tex-chtml.js")}"></script>
	<style>{$grayRate=min(max((1586016000 - time()) / 1800, 0), 1)}
    html { 
        -webkit-filter: grayscale({$grayRate}); 
        -moz-filter: grayscale({$grayRate}); 
        -ms-filter: grayscale({$grayRate}); 
        -o-filter: grayscale({$grayRate}); 
        filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale={$grayRate});  
        _filter:none; 
    }
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	<title>{$title|code}</title>
</head>
<body>
<div style="text-align: left; width: 100%">
	<img src="/img/ShenQieAiDao.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; opacity: {$grayRate}; display: {if $grayRate > 0}inline{else}none{/if}" />
</div>
<hr/>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->getinfo('addin.webplug'))}
    <div id="hu60_load_notice" style="display: none; position:absolute">
        <p>网页插件加载中。如果长时间无法加载，可以考虑<a href="addin.webplug.{$BID}">修改或删除网页插件代码</a>。</p>
        <p>公告：<a href="https://hu60.cn/q.php/bbs.topic.92900.html?_origin=*">如果网站很卡，请修改网页插件内的外链js</a>（为保证能打开，此页未登录）。</p>
    </div>
    {$USER->getinfo('addin.webplug')}
{/if}
<a id="top" href="#bottom" accesskey="6"></a>
{if !$base}
	{if !$no_user && is_object($user)}
		<div class="tp">
		{if $user->uid}
			{if $user->islogin}
				{$MSG=msg::getInstance($USER)}
				<a href="user.index.{$bid}">{$user->name|code}</a>
				{$newMSG=$MSG->newMsg()}
				{$newATINFO=$MSG->newAtInfo()}
				{if $newMSG > 0}<a href="msg.index.inbox.no.{$bid}">{$newMSG}条新内信</a>{/if}
				{if $newATINFO > 0}<a href="msg.index.@.no.{$bid}">{$newATINFO}条新@消息</a>{/if}
				<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>
			{else}
				已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登录</a>
			{/if}
		{else}
			<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
			<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
		{/if}
		</div>
		<hr>
	{/if}
{/if}

