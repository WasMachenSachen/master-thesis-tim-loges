import "./module/ai-description";
import "./page/sw-product-detail";
import "./view/tab-page";

/* dummy module for overiding tab route */
Shopware.Module.register("sw-new-tab-ai", {
	routeMiddleware(next, currentRoute) {
		const customRouteName = "sw.product.detail.ai";

		if (
			currentRoute.name === "sw.product.detail" &&
			currentRoute.children.every((currentRoute) => currentRoute.name !== customRouteName)
		) {
			currentRoute.children.push({
				name: customRouteName,
				path: "/sw/product/detail/:id/ai",
				component: "tab-page",
				meta: {
					parentPath: "sw.product.index",
				},
			});
		}
		next(currentRoute);
	},
});
