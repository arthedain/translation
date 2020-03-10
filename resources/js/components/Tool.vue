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
			<!-- <svg
                class="spin fill-80 mb-6"
                width="69"
                height="72"
                viewBox="0 0 23 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20.12 20.455A12.184 12.184 0 0 1 11.5 24a12.18 12.18 0 0 1-9.333-4.319c4.772 3.933 11.88 3.687 16.36-.738a7.571 7.571 0 0 0 0-10.8c-3.018-2.982-7.912-2.982-10.931 0a3.245 3.245 0 0 0 0 4.628 3.342 3.342 0 0 0 4.685 0 1.114 1.114 0 0 1 1.561 0 1.082 1.082 0 0 1 0 1.543 5.57 5.57 0 0 1-7.808 0 5.408 5.408 0 0 1 0-7.714c3.881-3.834 10.174-3.834 14.055 0a9.734 9.734 0 0 1 .03 13.855zM4.472 5.057a7.571 7.571 0 0 0 0 10.8c3.018 2.982 7.912 2.982 10.931 0a3.245 3.245 0 0 0 0-4.628 3.342 3.342 0 0 0-4.685 0 1.114 1.114 0 0 1-1.561 0 1.082 1.082 0 0 1 0-1.543 5.57 5.57 0 0 1 7.808 0 5.408 5.408 0 0 1 0 7.714c-3.881 3.834-10.174 3.834-14.055 0a9.734 9.734 0 0 1-.015-13.87C5.096 1.35 8.138 0 11.5 0c3.75 0 7.105 1.68 9.333 4.319C16.06.386 8.953.632 4.473 5.057z"
                    fill-rule="evenodd"
                />
            </svg>

            <h1 class="text-white text-4xl text-90 font-light mb-6">
                We're in a black hole.
            </h1>

            <p class="text-white-50% text-lg">
                You can edit this tool's component at:
                <code class="ml-1 border border-80 text-sm font-mono text-white bg-black rounded px-2 py-1">
                    /nova-components/Translation/resources/js/components/Tool.vue
                </code>
			</p>-->

			<form method="POST" class="w-full" @submit.prevent="update">
				<!-- <vue-tabs>
					<v-tab title="First tab"> -->
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
							<!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
					<path
						class="heroicon-ui"
						d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"
					/>
							</svg>-->
						</div>
						<div class="flex items-end flex-row-reverse py-6 px-8">
							<button type="submit" class="btn btn-default btn-primary ml-3">{{ __('Update')}}</button>
						</div>
					<!-- </v-tab>
				</vue-tabs> -->
			</form>
		</card>
	</div>
</template>

<script>
// import { VueTabs, VTab } from "vue-nav-tabs";
// //you can also import this in your style tag
// import "vue-nav-tabs/themes/vue-tabs.css";
export default {
	// components: {
	// 	VueTabs,
	// 	VTab
	// },
	data() {
		return {
			search: "",
			data: [],
			key: '',
			value_field: '',
		};
	},
	mounted() {
		axios.get("/nova-vendor/translation/get").then(response => {
			this.data = response.data.data;
			this.key = response.data.key;
			this.value = response.data.value;
			console.log(this.data);
			
			// console.log(response);
		});
	},
	methods: {
		update: function(form) {
			let formData = new FormData(form.target);

			// self.__('Sorry, your session has expired.')
			let self = this;
			axios
				.post("/nova-vendor/translation/update", formData)
				.then(function() {
					self.$toasted.show(self.__("Переводы успешно обновлены"), { type: "success" });
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
</style>
