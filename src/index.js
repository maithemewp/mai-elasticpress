import { addFilter } from '@wordpress/hooks'

const autosuggestItemHTML = ( itemHTML, option, index, searchText ) => {
	const text     = option._source.post_title;
	const url      = option._source.permalink;
	const postDate = new Date(option._source.post_date).toLocaleString('en', { dateStyle: 'medium' })

	console.log( option, option._source );

	return `<li class="autosuggest-item" role="option" aria-selected="false" id="autosuggest-option-${index}">
		<a href="${url}" class="autosuggest-link" data-url="${url}" tabindex="-1">
			${text} (${postDate})
		</a>
	</li>`;
};

addFilter( 'ep.Autosuggest.itemHTML', 'mai-elasticpress/autosuggestItemHTML', autosuggestItemHTML );
