<template>
	<div>
		<!-- <heading class="mb-6">Translation</heading> -->

		<div class="flex py-6 px-8">
			<input
				type="text"
				class="bg-white pl-search w-1/2 form-global-search"
				name="translation-search"
				v-model="search"
				:placeholder="__('Press / to search')"
			/>
			<div class="inline-block pl-3 w-1/4">
				<label
					for="labelVisibleCheckbox"
					class="inline-block text-80 pt-2 leading-tight"
				>{{ __("Show default value") }}</label>
				<input type="checkbox" id="labelVisibleCheckbox" class="checkbox" @click="changeLabelVisible" />
			</div>
			<div class="inline-block pl-3 w-1/4">
				<label
					for="deleteCheckbox"
					class="inline-block text-80 pt-2 leading-tight"
				>{{ __("Show delete button") }}</label>
				<input type="checkbox" id="deleteCheckbox" class="checkbox" @click="showDeleteButton" />
			</div>
		</div>

		<card class="bg-0 flex flex-col items-center justify-center" style="min-height: 300px">
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
					<div class="flex py-6 px-8 items-center mb-3" v-for="(item, id) in singleGroup" :key="id">
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
						<div v-if="showDelete" @click="remove(id);" class="buttonDelete"></div>
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
			data: [],
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
		axios.get("/nova-vendor/translation/get").then(response => {
			this.data = response.data.data;
		});
	},
	methods: {
		update: function(form) {
			let editorFields = form.target.querySelectorAll(".editor-field");

			let formData = new FormData(form.target);
			for (var pair of editorFields) {
				formData.append(
					pair.attributes.name.value,
					pair.querySelector(".ql-editor").innerHTML
				);
			}

			// self.__('Sorry, your session has expired.')
			// let self = this;
			axios
				.post("/nova-vendor/translation/update", formData)
				.then(() => {
					this.$toasted.show(this.__("Saved successfully"), {
						type: "success"
					});
				})
				.catch(error => {
					this.$toasted.show(this.__("Error"), { type: "error" });
				});
		},
		handleClick(newTab) {
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
			let element = this.data.splice(item, 1);

			axios
				.post("/nova-vendor/translation/delete", {'element': element})
				.then(() => {
					this.$toasted.show(this.__("Deleted successfully"), {
						type: "success"
					});
				})
				.catch(error => {
					this.$toasted.show(this.__("Error"), { type: "error" });
				});

			return this.data;
		},
		changeLabelVisible() {
			this.showLabel = !this.showLabel;
		},
		showDeleteButton() {
			this.showDelete = !this.showDelete;
		}
	},
	computed: {
		content() {
			return this.data.filter(item => {
				return item.key.toLowerCase().includes(this.search.toLowerCase());
			});
		},
		group() {
			return this.groupBy(this.content);
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
	height: 40px;
	width: 40px;
	background-color: #e74444;
	mask-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAy%0D%0ANCIgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0Ij48cGF0aCBjbGFzcz0iaGVyb2ljb24tdWkiIGQ9Ik00%0D%0ALjkzIDE5LjA3QTEwIDEwIDAgMSAxIDE5LjA3IDQuOTMgMTAgMTAgMCAwIDEgNC45MyAxOS4wN3pt%0D%0AMS40MS0xLjQxQTggOCAwIDEgMCAxNy42NiA2LjM0IDggOCAwIDAgMCA2LjM0IDE3LjY2ek0xMy40%0D%0AMSAxMmwxLjQyIDEuNDFhMSAxIDAgMSAxLTEuNDIgMS40MkwxMiAxMy40bC0xLjQxIDEuNDJhMSAx%0D%0AIDAgMSAxLTEuNDItMS40MkwxMC42IDEybC0xLjQyLTEuNDFhMSAxIDAgMSAxIDEuNDItMS40Mkwx%0D%0AMiAxMC42bDEuNDEtMS40MmExIDEgMCAxIDEgMS40MiAxLjQyTDEzLjQgMTJ6Ii8+PC9zdmc+");
	mask-repeat: no-repeat;
	mask-size: 100% 100%;
}
</style>
