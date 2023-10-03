// import "./page/index-page";

import deDE from "./snippet/de-DE";
import enGB from "./snippet/en-GB";

Shopware.Module.register("ai-description", {
	type: "plugin",
	name: "ai-description",
	title: "ai-description.general.title",
	description: "ai-description.general.description",
	snippets: {
		"de-DE": deDE,
		"en-GB": enGB,
	},
});
