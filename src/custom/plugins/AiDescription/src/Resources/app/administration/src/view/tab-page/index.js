import template from "./tab-template.html.twig";
import "./tab-style.scss";
const { Criteria } = Shopware.Data;

Shopware.Component.register("tab-page", {
	template,

	inject: ["repositoryFactory", "loginService"],

	metaInfo() {
		return {
			title: "AI TAB",
		};
	},
	props: {
		id: {
			type: String,
			required: true,
			// default: null,
		},
	},
	data() {
		return {
			entity: null,
			currentSelection: "a",
			currentDescription: "",
			properties: [],
			excludedProperties: [],
			groupIds: [],
			options: [
				{
					id: 1,
					label: "Verspielt",
					value: "a",
				},
				{
					id: 2,
					label: "Freundlich",
					value: "b",
				},
				{
					id: 3,
					label: "Professionell",
					value: "c",
				},
			],
		};
	},
	// watch: {
	// 	properties: {
	// 		handler() {
	// 		},
	// 		deep: true,
	// 	},
	// },

	computed: {
		productRepository() {
			return this.repositoryFactory.create("product");
		},
		propertyGroupRepository() {
			return this.repositoryFactory.create("property_group");
		},
		propertyGroupCriteria() {
			const criteria = new Criteria();

			criteria.addSorting(Criteria.sort("name", "ASC", false));
			criteria.addFilter(Criteria.equalsAny("id", this.groupIds));

			const optionIds = this.entity.properties.getIds();

			criteria.getAssociation("options").addFilter(Criteria.equalsAny("id", optionIds));
			criteria.addFilter(Criteria.equalsAny("options.id", optionIds));

			return criteria;
		},
	},
	methods: {
		callServiceFunction() {
			// this.AiDescription.apiCall();
		},
		onChangeTona(value) {
			console.log(value);
		},
		isSelected(value) {
			console.log(value);
		},
		async generateDescription() {
			const config = {
				tonality: this.options.find((option) => option.value === this.currentSelection).label ?? "Professionell",
				properties: this.properties,
			};
			// Post to /api/aidescription/generateDescription with the config as body
			const response = await fetch("/api/aidescription/generateDescription", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${this.loginService.getToken()}`,
				},
				body: JSON.stringify(config),
			});
			const data = await response.json();
			console.log(data);
			const content = JSON.parse(data.generateDescription);
			console.log(content.choices[0].text);
			this.currentDescription = content.choices[0].text;
		},

		duplicateDescription() {},
		previousDescription() {},
		nextDescription() {},
		publishDescription() {},

		getGroupIds() {
			if (!this.entity?.id) {
				return;
			}

			this.groupIds = this.entity.properties.reduce((accumulator, { groupId }) => {
				if (accumulator.indexOf(groupId) < 0) {
					accumulator.push(groupId);
				}

				return accumulator;
			}, []);
		},
		getProperties() {
			if (!this.entity?.id || this.groupIds.length <= 0) {
				// this.properties = [];
				return Promise.resolve();
			}

			// this.isPropertiesLoading = true;
			return this.propertyGroupRepository
				.search(this.propertyGroupCriteria, Shopware.Context.api)
				.then((prop) => {
					return prop;
				})
				.catch(() => {
					return [];
				})
				.finally(() => {
					// this.isPropertiesLoading = false;
				});
		},
		async getExcludedProperties() {
			try {
				const response = await fetch("/api/aidescription/exludedProperties", {
					headers: {
						Authorization: `Bearer ${this.loginService.getToken()}`,
					},
				});
				const data = await response.json();
				return data.excludedProperties ?? [];
			} catch (error) {
				console.error(error);
				return [];
			}
		},
	},
	async created() {
		const criteria = new Criteria().addAssociation("properties");
		try {
			this.excludedProperties = await this.getExcludedProperties();
			this.entity = await this.productRepository.get(this.id, Shopware.Context.api, criteria);
			this.getGroupIds();
			this.properties = await this.getProperties();
			this.properties.forEach((property) => {
				property.checked = !this.excludedProperties.includes(property.id);
			});
		} catch (error) {
			console.error(error);
		}
	},
});
