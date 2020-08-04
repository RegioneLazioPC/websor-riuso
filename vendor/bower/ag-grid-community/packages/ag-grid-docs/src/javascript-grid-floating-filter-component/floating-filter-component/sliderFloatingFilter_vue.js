import Vue from "vue";

export default Vue.extend({
    template: `
            <input type="range"
                   :value="currentValue"
                   min="0"
                   :max="maxValue"
                   step="1"
                   @change="valueChanged($event)"/>
    `,
    data: function () {
        return {
            maxValue: 0,
            currentValue: 0
        };
    },
    beforeMount() {
        this.maxValue = this.params.maxValue;
    },
    mounted() {
    },
    methods: {
        valueChanged(event) {
            this.currentValue = event.target.value;
            this.params.onFloatingFilterChanged({model: this.buildModel()});

        },

        onParentModelChanged(parentModel) {
            // note that the filter could be anything here, but our purposes we're assuming a greater than filter only,
            // so just read off the value and use that
            this.currentValue = !parentModel ? 0 : parentModel.filter
        },

        buildModel() {
            if (this.currentValue === 0) {
                return null;
            }
            return {
                filterType: 'number',
                type: 'greaterThan',
                filter: this.currentValue,
                filterTo: null
            };
        }
    }
});