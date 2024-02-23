<?php
/**
 * Results Item js template
 */
?>

<div class="jet-ajax-search__results-item">
	<a class="jet-ajax-search__item-link" href="{{{data.link}}}" target="{{{data.link_target_attr}}}">
		{{{data.thumbnail}}}
		<div class="jet-ajax-search__item-content-wrapper">
			{{{data.before_title}}}
			<div class="jet-ajax-search__item-title">{{{data.title}}}</div>
			{{{data.after_title}}}
			{{{data.before_content}}}
			<div class="jet-ajax-search__item-content">{{{data.content}}}</div>
			{{{data.after_content}}}
			{{{data.rating}}}
			{{{data.price}}}
		</div>
	</a>
</div>
