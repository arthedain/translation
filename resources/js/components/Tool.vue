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
				<div class="flex py-6 px-8 items-center mb-3" v-for="(item, id) in content" :key="id">
					<label
						class="inline-block text-80 pt-2 leading-tight w-1/2 mb-2"
						:for="item.id"
					>{{ item[key] }}</label>
					<input
						v-if="item.value.length <= 60"
						class="w-full form-control form-input form-input-bordered"
						type="text"
						:name="item.id"
						:value="item[value]"
					/>
					<textarea
						v-if="item.value.length > 60"
						class="w-full form-control form-input form-input-bordered py-3 h-auto"
						:name="item.id"
						:value="item[value]"
						rows="3"
					></textarea>
					<!-- <vue-editor
						v-if="item.value.length > 60"
						class="w-full py-3 h-auto editor-field"
						:name="item.id"
						:value="item[value]"
						ref="editorvue"
					></vue-editor> -->
				</div>
				<div class="flex items-end flex-row-reverse py-6 px-8">
					<button type="submit" class="btn btn-default btn-primary ml-3">{{ __('Update')}}</button>
				</div>
			</form>
		</card>
	</div>
</template>

<script>
import { VueEditor, Quill } from "vue2-editor";
import Editor from '@tinymce/tinymce-vue';

export default {
	data() {
		return {
			search: "",
			data: [],
			key: "",
			value_field: "",
			value: ""
		};
	},
	components: {
	VueEditor,
	Editor
  },
	mounted() {
		axios.get("/nova-vendor/translation/get").then(response => {
			this.data = response.data.data;
			this.key = response.data.key;
			this.value = response.data.value;

			// console.log(response);
		});
		
	},
	methods: {
		update: function(form) {
			let editorFields = form.target.querySelectorAll('.editor-field');
		
			let formData = new FormData(form.target);
			for(var pair of editorFields) {
				formData.append(pair.attributes.name.value, pair.querySelector('.ql-editor').innerHTML)
			}
			
			// self.__('Sorry, your session has expired.')
			let self = this;
			axios
				.post("/nova-vendor/translation/update", formData)
				.then(function() {
					self.$toasted.show(self.__("Переводы успешно обновлены"), {
						type: "success"
					});
				})
				.catch(function(error) {
					self.$toasted.show(self.__("Ошибка"), { type: "error" });
				});
		}
	},
	computed: {
		content() {
			return this.data.filter(item => {
				return item.key.toLowerCase().includes(this.search.toLowerCase());
			});
		}
	}
};
</script>

<style>
/* Scoped Styles */
.editor-field{
	padding-left: .75rem;
}
</style>
