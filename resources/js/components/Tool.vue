<template>
	<div>
		<!-- <heading class="mb-6">Translation</heading> -->



		<div class="flex mb-6">

			<div class="relative w-1/2">
				<div class="relative">
					<div class="relative">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="search" role="presentation" class="fill-current absolute search-icon-center ml-3 text-80"><path fill-rule="nonzero" d="M14.32 12.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387a8 8 0 1 1 1.414-1.414zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path></svg>
						<input
							type="text"
							name="translation-search"
							class="pl-search w-full form-global-search"
							v-model="search"
							:placeholder="__('Press / to search')"
							/>
					</div>
				</div>
			</div>
			<div class="inline-block pl-3 w-1/4">
				<label
					for="labelVisibleCheckbox"
					class="inline-block text-80 pt-2 leading-tight"
				>{{ __("Show default value") }}</label>
				<input type="checkbox" id="labelVisibleCheckbox" class="checkbox" @click="changeLabelVisible" />
			</div>
			<div class="inline-block w-1/4">
				<label
					for="deleteCheckbox"
					class="inline-block text-80 pt-2 leading-tight"
				>{{ __("Show delete button") }}</label>
				<input type="checkbox" id="deleteCheckbox" class="checkbox" @click="showDeleteButton" />
			</div>
		</div>
        <loading-card :loading="loading"></loading-card>
		<card v-show="!loading" class="bg-0 flex flex-col items-center justify-center" style="min-height: 300px">
			<form method="POST" class="w-full" @submit.prevent="update">
				<div class="flex flex-row">
					<div
						v-for="(item, key, index) in group"
						:key="key"
						:class="[currentTab == index ? selectedTabClass : defaultTabClass]"
						@click="handleClick(index)"
					>{{ __(key.toString()) }}</div>
				</div>
				<div
					v-for="(singleGroup, k, index) in group"
					:key="k"
					:class="[ currentTab == index ? '' :'tab-body']"
				>
					<div class="flex py-6 px-8 items-center mb-3 border-b border-40" v-for="(item, id) in singleGroup" :key="id">
						<label
							v-if="showLabel"
							class="inline-block text-80 pt-2 leading-tight w-1/2 mb-2"
							:for="item.id"
						>{{ item['custom_properties']['key'] ? item['custom_properties']['key'] : item[key] | striphtml }}</label>
						<input
							v-if="item['custom_properties']['editor'] == false && item.value.length <= 60"
							class="w-full form-control form-input form-input-bordered"
							type="text"
							:name="item.id"
							:value="item[value]"
						/>
						<textarea
							v-if="item['custom_properties']['editor'] == false && item.value.length > 60"
							class="w-full form-control form-input form-input-bordered py-3 h-auto"
							:name="item.id"
							:value="item[value]"
							rows="3"
						></textarea>
						<vue-editor
							v-if="item['custom_properties']['editor']"
							class="w-full py-3 h-auto editor-field"
							:name="item.id"
							:value="item[value]"
							:editor-toolbar="customToolbar"
							ref="editorvue"
						></vue-editor>
						<div v-if="showDelete" @click="remove(item.id);" class="buttonDelete">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="red" class="heroicon-ui" d="M8 6V4c0-1.1.9-2 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8H3a1 1 0 1 1 0-2h5zM6 8v12h12V8H6zm8-2V4h-4v2h4zm-4 4a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1z"/></svg>
						</div>
					</div>
				</div>
				<div class="flex items-end flex-row-reverse py-6 px-8">
					<button type="submit" class="btn btn-default btn-primary ml-3">{{ __('Update')}}</button>
				</div>
			</form>
		</card>
	</div>
</template>

<script>
import { VueEditor } from "vue2-editor";
import Tabs from "vue-tabs-with-active-line";

export default {
	data() {
		return {
			search: "",
			key: "key",
			value_field: "",
			value: "value",
			currentTab: 0,
			showLabel: false,
			showDelete: false,
			defaultTabClass:
				"py-5 px-8 border-b-2 focus:outline-none tab cursor-pointer text-grey font-semibold border-40",
			selectedTabClass:
				"py-5 px-8 border-b-2 focus:outline-none tab cursor-pointer text-grey-black font-bold border-primary",
			customToolbar: [
				[
					{
						header: [false, 1, 2, 3, 4, 5, 6]
					}
				],
				["bold", "italic", "underline", "strike"], // toggled buttons
				[
					{
						align: ""
					},
					{
						align: "center"
					},
					{
						align: "right"
					},
					{
						align: "justify"
					}
				],
				["blockquote", "code-block"],
				[
					{
						list: "ordered"
					},
					{
						list: "bullet"
					},
					{
						list: "check"
					}
				],
				[
					{
						indent: "-1"
					},
					{
						indent: "+1"
					}
				], // outdent/indent
				[
					{
						color: []
					},
					{
						background: []
					}
				], // dropdown with defaults from theme
				["link"],
				["clean"] // remove formatting button
			],
			id: false
		};
	},
	components: {
		VueEditor,
		Tabs
	},
	mounted() {
        this.activeTab();
        this.fetch();
	},
	methods: {
	    async fetch(){
            await this.$store.dispatch('fetchTranslations');
        },
		async update(form) {
			let editorFields = form.target.querySelectorAll(".editor-field");

			let formData = new FormData(form.target);
			for (var pair of editorFields) {
				formData.append(
					pair.attributes.name.value,
					pair.querySelector(".ql-editor").innerHTML
				);
			}

			await axios
				.post("/nova-vendor/translation/update", formData)
				.then(() => {
					this.$toasted.show(this.__("Saved successfully"), {
						type: "success"
					});
				})
				.catch(error => {
					this.$toasted.show(this.__("Error"), { type: "error" });
				});

            await this.fetch();
		},
		handleClick(newTab) {
	        this.$router.push({query: {tab: newTab}});
			this.currentTab = newTab;
		},
		groupBy(array) {
			let result = {};
			array.forEach(item => {
				if (!result[item["custom_properties"]["tab"]]) {
					result[item["custom_properties"]["tab"]] = [];
				}
				result[item["custom_properties"]["tab"]].push(item);
			});
			return result;
		},
		remove(item) {
			axios
				.post("/nova-vendor/translation/delete", {'element': item})
				.then(() => {
					this.$toasted.show(this.__("Deleted successfully"), {
						type: "success"
					});
				})
				.catch(error => {
					this.$toasted.show(this.__("Error"), { type: "error" });
				});
			this.fetch();
		},
		changeLabelVisible() {
			this.showLabel = !this.showLabel;
		},
		showDeleteButton() {
			this.showDelete = !this.showDelete;
		},
        activeTab(){
            const {query} = this.$router.history.current;
            this.currentTab = query.tab ? query.tab : 0;
        },
	},
	computed: {
	    data(){
	        return this.$store.getters.transition;
        },
        content(){
	        const arr = this.$store.getters.translation;
	        return arr.filter(item => {
                return item.key.toLowerCase().includes(this.search.toLowerCase());
            });
        },
		group() {
			return this.groupBy(this.content);
		},
        loading(){
	        return this.$store.getters.loading;
        }
	}
};
</script>

<style>
/* Scoped Styles */
.editor-field {
	padding-left: 0.75rem;
}
.tab {
	padding-top: 1.25rem;
	padding-bottom: 1.25rem;
}
.tab-body {
	display: none;
}
.buttonDelete {
	margin-left: 5px;
	cursor: pointer;
	/* height: 40px; */
	/* width: 40px; */
	/* background-color: #e74444; */
	/* mask-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAy%0D%0ANCIgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0Ij48cGF0aCBjbGFzcz0iaGVyb2ljb24tdWkiIGQ9Ik00%0D%0ALjkzIDE5LjA3QTEwIDEwIDAgMSAxIDE5LjA3IDQuOTMgMTAgMTAgMCAwIDEgNC45MyAxOS4wN3pt%0D%0AMS40MS0xLjQxQTggOCAwIDEgMCAxNy42NiA2LjM0IDggOCAwIDAgMCA2LjM0IDE3LjY2ek0xMy40%0D%0AMSAxMmwxLjQyIDEuNDFhMSAxIDAgMSAxLTEuNDIgMS40MkwxMiAxMy40bC0xLjQxIDEuNDJhMSAx%0D%0AIDAgMSAxLTEuNDItMS40MkwxMC42IDEybC0xLjQyLTEuNDFhMSAxIDAgMSAxIDEuNDItMS40Mkwx%0D%0AMiAxMC42bDEuNDEtMS40MmExIDEgMCAxIDEgMS40MiAxLjQyTDEzLjQgMTJ6Ii8+PC9zdmc+"); */
	/* mask-repeat: no-repeat; */
	/* mask-size: 100% 100%; */
}
form{
	min-height: 300px;
}
</style>
