(()=>{"use strict";(0,window.wp.hooks.addFilter)("ep.Autosuggest.itemHTML","mai-elasticpress/autosuggestItemHTML",((t,s,e,a)=>{const o=s._source.post_title,u=s._source.permalink;return new Date(s._source.post_date).toLocaleString("en",{dateStyle:"medium"}),console.log(s,s._source),`<li class="autosuggest-item" role="option" aria-selected="false" id="autosuggest-option-${e}">\n\t\t<a href="${u}" class="autosuggest-link autosuggest-link-has-image" data-url="${u}" tabindex="-1">\n\t\t\t${s._source.thumbnail?`<img class="autosuggest-image" src="${s._source.thumbnail.src}" alt="${s._source.thumbnail.alt}">`:'<span class="autosuggest-image" style="aspect-ratio:var(--autosuggest-image-aspect-ratio,1/1);"></span>'}\n\t\t\t<span class="autosuggest-title">${o}</span>\n\t\t</a>\n\t</li>`}))})();