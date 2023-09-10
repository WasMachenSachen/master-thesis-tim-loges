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
			currentDescription:
				"Sunt dolore reprehenderit est enim fugiat officia officia anim adipisicing laboris qui in anim esse. Duis proident non esse reprehenderit ea. Cupidatat officia ut voluptate quis voluptate dolor reprehenderit anim ea sit do ex nisi elit aliquip. Duis irure deserunt id esse ipsum aliqua esse exercitation aliquip aliquip id occaecat do magna. Voluptate aliquip velit consequat in dolore cillum ullamco ad. Ad mollit amet aute consectetur veniam in commodo eu pariatur tempor fugiat est ipsum dolor labore. Sint minim enim ullamco consequat dolore occaecat tempor. Ex reprehenderit ut aliqua sit velit culpa cupidatat nostrud cupidatat aliqua proident.",
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
					tag: "span",
					value: "red",
					handler: (button, parent = null) => {
						this.markParagraphs(button, parent);
					},
				},
				{
					// undo doesnt work after switch edit mode - remove button
					type: "undo",
					title: this.$tc("sw-text-editor-toolbar.title.undo"),
					icon: "regular-undo-xs",
					position: "middle",
				},
				{
					type: "redo",
					title: this.$tc("sw-text-editor-toolbar.title.redo"),
					icon: "regular-redo-xs",
					position: "middle",
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
		markParagraphs(button, parent) {
			console.log("markParagraphs");
			this.toggleSpan();
		},
		toggleSpan() {
			const selection = window.getSelection();
			const editorElement = document.querySelector("#aidescription-generated-editor");
			// if nothing is selected or the selection is outside the custom editor do not transform the selection
			if (!editorElement && selection.rangeCount === 0) return;

			const range = selection.getRangeAt(0);
			const spanElements = editorElement.querySelectorAll("span");

			let isPartOfSpan = false;
			let selectedText = "";

			// check if the selection contains any text and build the selectedText
			if (range.toString().trim() !== "") {
				selectedText = range.toString();
			}

			// we have to "walk" through all span elements and check if inside the selection is part of a span. we cant use the selection.containsNode() because it doesnt work for partial selections
			for (const spanElement of spanElements) {
				const spanRange = document.createRange();
				spanRange.selectNodeContents(spanElement);

				if (range.intersectsNode(spanElement)) {
					isPartOfSpan = true;
					break;
				}
			}
			/*
			 * we have the following cases:
			 * 1. the selection contains only text -> wrap it inside a span
			 * 2. the selection contains some text and a partial of a span -> ignore the selection and remove the span while preserving its content at the same place
			 * 3. the selection contains a partial of a span and then some text -> same as before
			 * 4. the selection contains some text and a hole span and then again some text -> same as before
			 *
			 * a partial would be something like "This is a <span>test</span> text" and the selection is "test text"
			 * we want to check for partials, so a user can select something that is already "marked" for regeneration and undo it
			 */
			if (!isPartOfSpan && selectedText !== "") {
				// case 1: the selection only contains text and is not a partial of another span.
				const newSpan = document.createElement("span");
				newSpan.setAttribute("data-change", "true");
				range.surroundContents(newSpan);
			} else {
				// case 2, 3 and 4
				for (const spanElement of spanElements) {
					const spanRange = document.createRange();
					spanRange.selectNodeContents(spanElement);

					if (range.intersectsNode(spanElement)) {
						const fragment = spanRange.extractContents();
						const parent = spanElement.parentNode;

						parent.insertBefore(fragment, spanElement);
						parent.removeChild(spanElement);
					}
				}
			}
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
			// console.log(data);
			// debugger;
			// const content = JSON.parse(data);
			// console.log(content.choices[0].text);
			this.currentDescription = data.response;
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
