<template>
    <div>
        <div class="flex border-b border-40 remove-bottom-border">
            <div class="w-1/4 py-4">
                <h4 class="font-normal text-80">{{ field.name }}</h4>
            </div>
            <div class="w-3/4 py-4 break-words">
                <p>{{ field.tab }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    mounted(){
        this.fetchTranslations();
    },

    methods: {
        async fetchTranslations(){
            await this.$store.dispatch('fetchTabTranslations', this.field.tab);
        },
    },

    computed: {
        translation() {
            return this.$store.getters.tabTranslation;
        },
        loading() {
            return this.$store.getters.loading;
        }
    }
}
</script>
