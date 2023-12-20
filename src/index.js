import { addFilter } from '@wordpress/hooks'

addFilter( 'ep.InstantResults.Result', 'mai-elasticpress/autosuggest', () => autoSuggestImage );
const autoSuggestImage = ({ date, image, title, url }) => {

	console.log( image );

	return (
		<div className="maiep-autosuggest-item">
			<a href={url}>{image}<span className="maiep-autosuggest-title">{title}</span></a>
		</div>
	)
};
