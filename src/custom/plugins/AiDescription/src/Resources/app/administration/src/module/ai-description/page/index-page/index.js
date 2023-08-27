import template from "./index-template.html.twig";

Shopware.Component.register("index-page", {
	template,
	inject: ["loginService"],
	metaInfo() {
		return {
			title: this.$createTitle(),
		};
	},
	created() {
		console.log("Hello");
	},
	methods: {
		async callServiceFunction() {
			const response = await fetch("/api/aidescription/test", {
				headers: {
					Authorization: `Bearer ${this.loginService.getToken()}`,
				},
			});
			const data = await response.json();
			console.log(data);
		},
	},
});
