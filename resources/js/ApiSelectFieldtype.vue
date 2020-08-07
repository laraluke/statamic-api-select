<template>
    <v-select
        ref="input"
        :name="name"
        :clearable="config.clearable"
        :disabled="config.disabled || isReadOnly"
        :options="options"
        :placeholder="config.placeholder"
        :searchable="config.searchable"
        :multiple="config.multiple"
        :close-on-select="true"
        :value="selectedOptions"
        @input="vueSelectUpdated"
        @search:focus="$emit('focus')"
        @search:blur="$emit('blur')">
            <template #selected-option-container v-if="config.multiple"><i class="hidden"></i></template>
            <template #search="{ events, attributes }" v-if="config.multiple">
                <input
                    :placeholder="config.placeholder"
                    class="vs__search"
                    type="search"
                    v-on="events"
                    v-bind="attributes"
                >
            </template>
             <template #no-options>
                <div class="text-sm text-grey-70 text-left py-1 px-2" v-text="__('No options to choose from.')" />
            </template>
            <template #footer="{ deselect }" v-if="config.multiple">
                <div class="vs__selected-options-outside flex flex-wrap">
                    <span v-for="option in selectedOptions" :key="option.value" class="vs__selected mt-1">
                        {{ option.label }}
                        <button @click="deselect(option)" type="button" :aria-label="__('Deselect option')" class="vs__deselect">
                            <span>×</span>
                        </button>
                    </span>
                </div>
            </template>
    </v-select>
</template>

<script>
export default {

    mixins: [Fieldtype],

    data: function() {
        return {
            loading: true,
            options: []
        }
    },

    computed: {
        selectedOptions() {
            let selections = this.value || [];

            if (typeof selections === 'string' || typeof selections === 'number') {
                selections = [selections];
            }

            return selections.map(value => {
                return _.findWhere(this.options, {value}) || { value, label: value };
            });
        },
    },

    mounted() {
        this.$axios.get(this.config.endpoint).then(response => {

            var items = response.data;

            var options = [];

            _.each(items, function(item) {
                options.push({
                    label: item[this.item_label],
                    value: item[this.item_key]
                });
            }, this.config);

            this.options = options;
        });
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },

        vueSelectUpdated(value) {
            if (this.config.multiple) {
                this.update(value.map(v => v.value));
            } else {
                if (value) {
                    this.update(value.value)
                } else {
                    this.update(null);
                }
            }
        },
    }
};
</script>
