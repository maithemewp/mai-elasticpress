import { addFilter } from '@wordpress/hooks'

addFilter( 'ep.InstantResults.Result', 'mai-elasticpress/autosuggest', () => autoSuggestImage );
const autoSuggestImage = ({ date, thumbnail, title, url }) => {
	console.log( thumbnail );

	return (
		<div className="maiep-autosuggest-item">
			<a href={url}>{thumbnail}<span className="maiep-autosuggest-title">{title}</span></a>
		</div>
	)
};
