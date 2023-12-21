import { addFilter } from '@wordpress/hooks'

const autosuggestItemHTML = ( itemHTML, option, index, searchText ) => {
	const text     = option._source.post_title;
	const url      = option._source.permalink;
	const postDate = new Date(option._source.post_date).toLocaleString('en', { dateStyle: 'medium' })

	console.log( option, option._source );

	return `<li class="autosuggest-item" role="option" aria-selected="false" id="autosuggest-option-${index}">
		<a href="${url}" class="autosuggest-link autosuggest-link-has-image" data-url="${url}" tabindex="-1">
			${option._source.thumbnail ? `<img class="autosuggest-image" src="${option._source.thumbnail.src}" alt="${option._source.thumbnail.alt}">` : '<span class="autosuggest-image"></span>'}
			<span class="autosuggest-title">${text}</span>
		</a>
	</li>`;
};

addFilter( 'ep.Autosuggest.itemHTML', 'mai-elasticpress/autosuggestItemHTML', autosuggestItemHTML );
