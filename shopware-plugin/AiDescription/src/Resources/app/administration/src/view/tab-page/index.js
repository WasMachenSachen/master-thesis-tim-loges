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
			history: null,
			currentHistoryIndex: null,
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
					title: "Umformulierung markieren",
					/* platzhalter icon, da sparkles in dieser sw version nicht dabei ist */
					icon: "solid-circle-download",
					handler: (button, parent = null) => {
						this.toggleSpan(button, parent);
					},
				},
			],
			explanation:
				"Nutze 'Generieren' um einen Text anhand der Konfiguration zu erstellen. In der Menü-Leiste des Editors können ausgewählte Passagen mit 'Umformulierung markieren' ausgewählt werden und dann über die Schaltfläche 'Umformulieren' neu Formuliert werden. Mit der Schaltfläche 'Veröffentlichen' wird der neue Text in die Produktbeschreibung übernommen. Die Änderungen müssen über die Schaltfläche 'Save' gespeichert werden!",
			explanationConfig:
				"Nutze die Tonalität um die Formulierungen der Texte zu beinflussen. Die Eigenschaften können an- und abgewählt werden. Nur die ausgewählten Eigenschaften werden explizit im Text genannt. Alle anderen werden nur genutzt, um den Wein besser zu verstehen.",
		};
	},
	watch: {
		currentHistoryIndex: {
			handler(current, prev) {
				this.currentDescription = this.history[current - 1].content;
			},
			deep: true,
		},
	},

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
		toggleSpan() {
			const selection = window.getSelection();
			const editorElement = document.querySelector("#aidescription-generated-editor");
			// if nothing is selected or the selection is outside the custom editor do not transform the selection
			if (!editorElement && selection.rangeCount === 0) return;

			const userSelectedRange = selection.getRangeAt(0);
			//this also includes all buttons, but this shouldnt be a problem
			const spanElements = editorElement.querySelectorAll("span");

			let includesAnotherSpan = false;
			let selectedText = "";

			// check if the selection contains any text and fill selectedText
			if (userSelectedRange.toString().trim() !== "") {
				selectedText = userSelectedRange.toString();
			}

			// we have to "walk" through all span elements and check if inside the selection is part of a span. we cant use the selection.containsNode() because it doesnt work for partial selections
			for (const spanElement of spanElements) {
				if (userSelectedRange.intersectsNode(spanElement)) {
					includesAnotherSpan = true;
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
			 * a partial would be something like "this is a <span>test</span> text" and the selection is "test text"
			 * we want to check for partials, so a user can select something that is already "marked" for regeneration and undo it
			 *
			 * if we try to wrap the text and some part of a span we get a lot of edge cases, so it is way easier to just remove the span and preserve its content.
			 * also it is not clear how we should proceed if the partial from a span that is not selected is bigger then the selection: should it all be wrapped? should it be removed? should it be split?
			 * right now the worst case it that the user has select a part again if he originally wanted to extend a marked passage, which is not a big inconvenience.
			 */
			if (!includesAnotherSpan && selectedText !== "") {
				// case 1: the selection only contains text and is not a partial of another span.
				const newSpan = document.createElement("span");
				newSpan.setAttribute("data-change", "true");
				// edge case: the selection contains another DOM-Element
				// cant use userSelectedRange.surroundContents(newSpan) cause it will break if the selection contains part of another dom element. see: https://developer.mozilla.org/en-US/docs/Web/API/userSelectedRange/surroundContents and https://stackoverflow.com/questions/1730967/how-to-wrap-with-html-tags-a-cross-boundary-dom-selection-userSelectedRange
				// extractContents to the rescue: "Partially selected nodes are cloned to include the parent tags necessary to make the document fragment valid."
				newSpan.appendChild(userSelectedRange.extractContents());
				userSelectedRange.insertNode(newSpan);
			} else {
				// turns out we can combine case 2, 3 and 4: just remove the span and preserve its content
				for (const spanElement of spanElements) {
					const spanRange = document.createRange();
					spanRange.selectNodeContents(spanElement);

					if (userSelectedRange.intersectsNode(spanElement)) {
						const fragment = spanRange.extractContents();
						const parent = spanElement.parentNode;

						parent.insertBefore(fragment, spanElement);
						// remove the now empty span
						parent.removeChild(spanElement);
						// dont break here, because there could be multiple spans that need to be removed!
					}
				}
			}
		},

		async regenerateDescription() {
			// check if currentDescription does not contain any span elements -> eg nothing is selected, a regeneration would not do anything
			if (!this.currentDescription.includes('<span data-change="true">')) {
				this.$store.dispatch("notification/createNotification", {
					variant: "error",
					message: "Keine Passage für eine Umformulierung ausgewählt!",
				});
				return;
			}

			this.isLoadingDescription = true;
			const config = {
				tonality: this.options.find((option) => option.value === this.currentSelection).label ?? "Professionell",
				description: this.currentDescription,
				product_id: this.entity.id,
			};

			this.currentDescription = "Beschreibung wird überarbeitet... Seite nicht verlassen!";

			const response = await fetch("/api/aidescription/regenerateDescription", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${this.loginService.getToken()}`,
				},
				body: JSON.stringify(config),
			});
			const data = await response.json();
			console.log(data);

			if (data.error) {
				this.$store.dispatch("notification/createNotification", {
					variant: "error",
					message: data.error,
				});
			}

			if (data?.history?.elements) {
				this.history = this.sortHistory(data.history.elements);
				this.currentDescription = this.history[this.history.length - 1].content;
				this.currentHistoryIndex = this.history.length;
			} else {
				this.currentDescription = data.response;
			}
			this.isLoadingDescription = false;
		},
		async generateDescription() {
			this.isLoadingDescription = true;
			this.currentDescription = "Beschreibung wird erstellt... Seite nicht verlassen!";
			const config = {
				tonality: this.options.find((option) => option.value === this.currentSelection).label ?? "Professionell",
				properties: this.properties,
				product_id: this.entity.id,
			};

			const response = await fetch("/api/aidescription/generateDescription", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${this.loginService.getToken()}`,
				},
				body: JSON.stringify(config),
			});
			const data = await response.json();

			if (data.error) {
				this.$store.dispatch("notification/createNotification", {
					variant: "error",
					message: data.error,
				});
			}

			if (data?.history?.elements) {
				this.history = this.sortHistory(data.history.elements);
				this.currentDescription = this.history[this.history.length - 1].content;
				this.currentHistoryIndex = this.history.length;
			} else {
				this.currentDescription = data.response;
			}
			this.isLoadingDescription = false;
		},

		publishDescription() {
			this.entity.description = this.currentDescription;
			Shopware.State.commit("swProductDetail/setProduct", this.entity);
		},

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
		async getHistory() {
			const response = await fetch("/api/aidescription/history", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${this.loginService.getToken()}`,
				},
				body: JSON.stringify({ product_id: this.entity.id }),
			});
			const data = await response.json();

			if (data.history.elements) {
				this.history = this.sortHistory(data.history.elements);
				if (this.history.length > 0) {
					this.currentDescription = this.history[this.history.length - 1].content;
					this.currentHistoryIndex = this.history.length;
				}
			}
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
		sortHistory(history) {
			// convert the history elements object and sort them by createdAt
			const sortedHistory = Object.values(history);

			sortedHistory.sort((a, b) => {
				const dateA = new Date(a.createdAt);
				const dateB = new Date(b.createdAt);
				return dateA - dateB;
			});
			return sortedHistory;
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
			this.getHistory();
		} catch (error) {
			console.error(error);
		}
	},
});
