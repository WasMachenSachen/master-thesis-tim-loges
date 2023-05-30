import template from "./tab-template.html.twig";

Shopware.Component.register("tab-page", {
	template,
	metaInfo() {
		return {
			title: "AI TAB",
		};
	},
});
