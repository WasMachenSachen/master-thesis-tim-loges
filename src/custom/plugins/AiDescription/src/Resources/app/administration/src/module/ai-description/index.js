import "./page/index-page";

import deDE from "./snippet/de-DE";
import enGB from "./snippet/en-GB";

Shopware.Module.register("ai-description", {
	type: "plugin",
	name: "ai-description",
	title: "ai-description.general.title",
	description: "ai-description.general.description",
	color: "#ff3d58",
	icon: "default-object-product",
	snippets: {
		"de-DE": deDE,
		"en-GB": enGB,
	},
	navigation: [
		{
			id: "ai-description",
			path: "ai.description.index",
			label: "navigation label",
			color: "#ff3d58",
			icon: "default-shopping-paper-bag-product",
			position: 100,
			parent: "sw-catalogue",
		},
	],
	routes: {
		index: {
			component: "index-page",
			path: "start",
		},
	},
});
