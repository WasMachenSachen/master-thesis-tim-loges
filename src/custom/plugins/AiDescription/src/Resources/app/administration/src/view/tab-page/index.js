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
			isLoadingDescription: false,
			// isLoadingDescriptionPlaceholder: "Beschreibung wird erstellt...",
			buttonConfig: [
				{
					type: "paragraph",
					title: this.$tc("sw-text-editor-toolbar.title.format"),
					icon: "regular-style-xs",
					children: [
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.paragraph"),
							value: "p",
							tag: "p",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h1"),
							value: "h1",
							tag: "h1",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h2"),
							value: "h2",
							tag: "h2",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h3"),
							value: "h3",
							tag: "h3",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h4"),
							value: "h4",
							tag: "h4",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h5"),
							value: "h5",
							tag: "h5",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.h6"),
							value: "h6",
							tag: "h6",
						},
						{
							type: "formatBlock",
							name: this.$tc("sw-text-editor-toolbar.title.blockquote"),
							value: "blockquote",
							tag: "blockquote",
						},
					],
				},
				{
					type: "foreColor",
					title: this.$tc("sw-text-editor-toolbar.title.text-color"),
					value: "",
					tag: "font",
				},
				{
					type: "bold",
					title: this.$tc("sw-text-editor-toolbar.title.bold"),
					icon: "regular-bold-xs",
					tag: "b",
				},
				{
					type: "italic",
					title: this.$tc("sw-text-editor-toolbar.title.italic"),
					icon: "regular-italic-xs",
					tag: "i",
				},
				{
					type: "underline",
					title: this.$tc("sw-text-editor-toolbar.title.underline"),
					icon: "regular-underline-xs",
					tag: "u",
				},
				{
					type: "strikethrough",
					title: this.$tc("sw-text-editor-toolbar.title.strikethrough"),
					icon: "regular-strikethrough-xs",
					tag: "strike",
				},
				{
					type: "superscript",
					title: this.$tc("sw-text-editor-toolbar.title.superscript"),
					icon: "regular-superscript-xs",
					tag: "sup",
				},
				{
					type: "subscript",
					title: this.$tc("sw-text-editor-toolbar.title.subscript"),
					icon: "regular-subscript-xs",
					tag: "sub",
				},
				{
					type: "justify",
					title: this.$tc("sw-text-editor-toolbar.title.textAlign"),
					icon: "regular-align-left-xs",
					children: [
						{
							type: "justifyLeft",
							title: this.$tc("sw-text-editor-toolbar.title.alignLeft"),
							icon: "regular-align-left",
						},
						{
							type: "justifyCenter",
							title: this.$tc("sw-text-editor-toolbar.title.alignCenter"),
							icon: "regular-align-center",
						},
						{
							type: "justifyRight",
							title: this.$tc("sw-text-editor-toolbar.title.alignRight"),
							icon: "regular-align-right",
						},
						{
							type: "justifyFull",
							title: this.$tc("sw-text-editor-toolbar.title.justify"),
							icon: "regular-align-justify",
						},
					],
				},
				{
					type: "insertUnorderedList",
					title: this.$tc("sw-text-editor-toolbar.title.insert-unordered-list"),
					icon: "regular-list-unordered-xs",
					tag: "ul",
				},
				{
					type: "insertOrderedList",
					title: this.$tc("sw-text-editor-toolbar.title.insert-ordered-list"),
					icon: "regular-list-numbered-xs",
					tag: "ol",
				},
				{
					type: "link",
					title: this.$tc("sw-text-editor-toolbar.title.link"),
					icon: "regular-link-xs",
					expanded: false,
					newTab: false,
					displayAsButton: false,
					value: "",
					tag: "a",
				},
				{
					// type: "hiliteColor",
					title: "Umformulieren",
					/* platzhalter icon, da sparkles in dieser version anscheinend nicht dabei ist */
					icon: "solid-circle-download",
					// tag: "span",
					value: "red",
					handler: (button, parent = null) => {
						this.markParagraphs(button, parent);
					},
				},
				// {
				// 	type: "undo",
				// 	title: this.$tc("sw-text-editor-toolbar.title.undo"),
				// 	icon: "regular-undo-xs",
				// 	position: "middle",
				// },
				// {
				// 	type: "redo",
				// 	title: this.$tc("sw-text-editor-toolbar.title.redo"),
				// 	icon: "regular-redo-xs",
				// 	position: "middle",
				// },
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
		markParagraphs(button, parent) {
			console.log("markParagraphs");
			console.log(button);
			console.log(parent);
			console.log(this.getSelectedText());
			this.wrapSelectedTextWithSpan();
		},
		getSelectedText() {
			let selectedText = "";
			if (window.getSelection) {
				// Check if the method is supported by the browser
				selectedText = window.getSelection().toString();
			} else if (document.selection && document.selection.type != "Control") {
				// For older IE versions
				selectedText = document.selection.createRange().text;
			}
			return selectedText;
		},
		wrapSelectedTextWithSpan() {
			let selectedText = this.getSelectedText();
			if (selectedText) {
				const span = document.createElement("span");
				span.setAttribute("data-change", "true");
				span.textContent = selectedText;
				const range = this.getSelectedRange();
				if (range) {
					range.deleteContents();
					range.insertNode(span);
				}
			}
		},
		getSelectedRange() {
			if (window.getSelection) {
				const selection = window.getSelection();
				if (selection.rangeCount > 0) {
					return selection.getRangeAt(0);
				}
			}
			return null;
		},
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
			this.isLoadingDescription = true;
			this.currentDescription = "Beschreibung wird erstellt...";
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
			this.isLoadingDescription = false;
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
		/**
		 *
		 * @async
		 * @function getExcludedProperties
		 * @returns {Promise<Array>} An array of excluded properties if the fetch operation is successful, otherwise an empty array.
		 */
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
