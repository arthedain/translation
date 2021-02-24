<template>
    <div>
        <div class="flex mb-6">

            <div class="relative w-1/2">
                <div class="relative">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                             aria-labelledby="search" role="presentation"
                             class="fill-current absolute search-icon-center ml-3 text-80">
                            <path fill-rule="nonzero"
                                  d="M14.32 12.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387a8 8 0 1 1 1.414-1.414zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
                        </svg>
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
                <input type="checkbox" id="labelVisibleCheckbox" class="checkbox" @click="changeLabelVisible"/>
            </div>
            <div class="inline-block w-1/4">
                <label
                    for="deleteCheckbox"
                    class="inline-block text-80 pt-2 leading-tight"
                >{{ __("Show delete button") }}</label>
                <input type="checkbox" id="deleteCheckbox" class="checkbox" @click="showDeleteButton"/>
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
                    >{{ __(key.toString()) }}
                    </div>
                </div>
                <div
                    v-for="(singleGroup, k, index) in group"
                    :key="k"
                    :class="[ currentTab == index ? '' :'tab-body']"
                >
                    <div class="flex py-6 px-8 items-center mb-3 border-b border-40" v-for="(item, id) in singleGroup"
                         :key="id">
                        <label
                            v-if="showLabel"
                            class="inline-block text-80 pt-2 leading-tight w-1/2 mb-2"
                            :for="item.id"
                        >{{ item['custom_properties']['key'] ? item['custom_properties']['key'] : item[key] | striphtml
                            }}</label>
                        <input
                            v-if="item['custom_properties']['editor'] == false && item.value.length <= 60"
                            class="w-full form-control form-input form-input-bordered"
                            type="text"
                            :name="item.id"
                            v-model="item.value"
                        />
                        <textarea
                            v-if="item['custom_properties']['editor'] == false && item.value.length > 60"
                            class="w-full form-control form-input form-input-bordered py-3 h-auto"
                            :name="item.id"
                            v-model="item.value"
                            rows="3"
                        ></textarea>
                        <div class="w-full" v-if="item['custom_properties']['editor']">
                            <editor
                                :name="item.id"
                                v-model="item.value"
                                class="w-full py-3 h-auto"
                                :init="{content_css: '/vendor/tinymce/skins/ui/oxide/content.min.css',
                                skin_url:'/vendor/tinymce/skins/ui/oxide',
                                plugins: [
                                   'advlist autolink lists link image charmap print preview anchor',
                                   'searchreplace visualblocks code fullscreen',
                                   'insertdatetime media table paste code wordcount'
                                 ],
                                 toolbar:
                                   'undo redo | formatselect | bold italic backcolor | \
                                   alignleft aligncenter alignright alignjustify | \
                                   bullist numlist outdent indent | removeformat'
                                }"
                            />
                        </div>
                        <div v-if="showDelete" @click="remove(item.id);" class="buttonDelete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="red" class="heroicon-ui"
                                      d="M8 6V4c0-1.1.9-2 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8H3a1 1 0 1 1 0-2h5zM6 8v12h12V8H6zm8-2V4h-4v2h4zm-4 4a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1z"/>
                            </svg>
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
    import Tabs from "vue-tabs-with-active-line";
    import Editor from '@tinymce/tinymce-vue';
    import form from "../utils/form";

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
                id: false
            };
        },
        components: {
            Editor,
            Tabs
        },
        mounted() {
            this.activeTab();
            this.fetch();
        },
        methods: {
            async fetch() {
                await this.$store.dispatch('fetchTranslations');
            },
            async update() {

                let data = [];
                _.each(this.group, (item, key) => {
                    _.each(item, (i, k) => {
                        data.push(i);
                    });
                })

                await axios
                    .post("/nova-vendor/translation/update", form.prepareValue(data))
                    .then(() => {
                        this.$toasted.show(this.__("Saved successfully"), {
                            type: "success"
                        });
                    })
                    .catch(error => {
                        this.$toasted.show(this.__("Error"), {type: "error"});
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
                        this.$toasted.show(this.__("Error"), {type: "error"});
                    });
                this.fetch();
            },
            changeLabelVisible() {
                this.showLabel = !this.showLabel;
            },
            showDeleteButton() {
                this.showDelete = !this.showDelete;
            },
            activeTab() {
                const {query} = this.$router.history.current;
                this.currentTab = query.tab ? query.tab : 0;
            },
        },
        computed: {
            data() {
                return this.$store.getters.transition;
            },
            content() {
                const arr = this.$store.getters.translation;
                return arr.filter(item => {
                    return item.key.toLowerCase().includes(this.search.toLowerCase());
                });
            },
            group() {
                return this.groupBy(this.content);
            },
            loading() {
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
    }

    form {
        min-height: 300px;
    }
</style>
