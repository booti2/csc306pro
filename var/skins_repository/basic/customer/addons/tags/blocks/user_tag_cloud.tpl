{** block-description:my_tag_cloud **}

{if $items}
{foreach from=$items item="tag"}
	{assign var="tag_name" value=$tag.tag|escape:url}
	<a href="{"tags.view?tag=`$tag_name`&amp;see=my"|fn_url}" class="tag-level-{$tag.level}">{$tag.tag}</a>&nbsp;({$tag.popularity})
{/foreach}

<p class="right">
	<a class="extra-link" href="{"tags.summary"|fn_url}">{$lang.my_tags_summary}</a>
</p>
{/if}