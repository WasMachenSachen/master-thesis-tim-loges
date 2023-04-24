import template from "./index-template.html.twig";

Shopware.Component.register("index-page", {
	template,
	metaInfo() {
		return {
			title: this.$createTitle(),
		};
	},
});
