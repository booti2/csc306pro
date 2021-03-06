{if $content|trim}
	<div class="{$sidebox_wrapper|default:"sidebox-wrapper"} {if $hide_wrapper}hidden cm-hidden-wrapper{/if}{if $block.user_class} {$block.user_class}{/if}{if $content_alignment == 'RIGHT'} float-right{elseif $content_alignment == 'LEFT'} float-left{/if}">
		<h3 class="sidebox-title{if $header_class} {$header_class}{/if}">
			{hook name="wrapper:sidebox_general_title"}
			{if $smarty.capture.title|trim}
				{$smarty.capture.title}
			{else}
				<span>{$title}</span>
			{/if}
			{/hook}
		</h3>
		<div class="sidebox-body">{$content|unescape|default:"&nbsp;"}</div>
	</div>
{/if}