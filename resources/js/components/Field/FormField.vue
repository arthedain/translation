<template>
    <div>
        <loading-card :loading="loading"></loading-card>
        <form v-if="!loading">
            <div v-for="(item, key) in translation" :key="key" class="p-4 border-b border-40">
                <input
                    v-if="item['custom_properties']['editor'] === false && item.value.length <= 60"
                    :id="item.id"
                    type="text"
                    class="w-full form-control form-input form-input-bordered "
                    :placeholder="item.value"
                    v-model="item.value"
                />
                <textarea
                    v-if="item['custom_properties']['editor'] === false && item.value.length > 60"
                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                    :name="item.id"
                    v-model="item.value"
                    rows="3"
                ></textarea>
                <editor
                    v-if="item['custom_properties']['editor']"
                    :name="item.id"
                    v-model="item.value"
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
        </form>
    </div>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'
    import Editor from '@tinymce/tinymce-vue';
    import form from "../../utils/form";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        components: {
            Editor
        },

        data() {
            return {
            }
        },

        mounted() {
            this.fetchTranslation();
        },

        methods: {
            async fetchTranslation() {
                await this.$store.dispatch('fetchTabTranslations', this.field.tab);
            },

            async updateTranslation() {
                await this.$store.dispatch('updateTranslations', form.prepareValue(this.translation));
            },
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                this.updateTranslation();
                // formData.append(this.field.attribute, null)
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },


        },
        computed: {
            translation() {
                return this.$store.getters.tabTranslation;
            },
            loading() {
                return this.$store.getters.loading;
            }
        },
    }
</script>
