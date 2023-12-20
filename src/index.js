import { createHooks } from '@wordpress/hooks';

wp.hooks.addFilter( 'ep.InstantResults.Result', 'mai-elasticpress/autosuggest', () => autoSuggestImage );
const autoSuggestImage = ({ date, image, title, url }) => {
	return (
		<div className="maiep-autosuggest-item">
			<a href={url}>{image}<span className="maiep-autosuggest-title">{title}</span></a>
		</div>
	)
};
