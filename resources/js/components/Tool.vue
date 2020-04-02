<template>
	<div>
		<!-- <heading class="mb-6">Translation</heading> -->

		<div class="py-6 px-8">
			<input
				type="text"
				class="bg-white pl-search w-1/2 form-global-search"
				name="translation-search"
				v-model="search"
				:placeholder="__('Press / to search')"
			/>
		</div>

		<card class="bg-0 flex flex-col items-center justify-center" style="min-height: 300px">
			<form method="POST" class="w-full" @submit.prevent="update">
				<div class="flex flex-row">
					<div v-for="(item, key, index) in group" :key="key" 
						:class="[currentTab == index ? selectedTabClass : defaultTabClass]" 
						@click="handleClick(index)">{{ __(key.toString()) }}</div>
				</div>
				<div v-for="(singleGroup, k, index) in group" :key="k" :class="[ currentTab == index ? '' :'tab-body']">
					<div class="flex py-6 px-8 items-center mb-3" v-for="(item, id) in singleGroup" :key="id">
						<label
							class="inline-block text-80 pt-2 leading-tight w-1/2 mb-2"
							:for="item.id"
						>{{ item[key] }}</label>
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
							
							ref="editorvue"
						></vue-editor>
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
			defaultTabClass: 'py-5 px-8 border-b-2 focus:outline-none tab cursor-pointer text-grey font-semibold border-40',
			selectedTabClass: 'py-5 px-8 border-b-2 focus:outline-none tab cursor-pointer text-grey-black font-bold border-primary',
			customToolbar: [["bold", "italic", "underline"], [{ list: "ordered" }, { list: "bullet" }], ["image", "code-block"]]
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
				.then(()=> {
					this.$toasted.show(this.__("Переводы успешно обновлены"), {
						type: "success"
					});
				})
				.catch((error)=> {
					this.$toasted.show(this.__("Ошибка"), { type: "error" });
				});
		},
		handleClick(newTab) {
			this.currentTab = newTab;
		},
		groupBy(array) {
			let result = {};
			array.forEach(item => {
				if (!result[item['custom_properties']['tab']]){
				result[item['custom_properties']['tab']] = []
				}
				result[item['custom_properties']['tab']].push(item)
			})
			return result;
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
.tab{
	padding-top: 1.25rem;
    padding-bottom: 1.25rem;
}
.tab-body{
	display: none;
}
</style>
