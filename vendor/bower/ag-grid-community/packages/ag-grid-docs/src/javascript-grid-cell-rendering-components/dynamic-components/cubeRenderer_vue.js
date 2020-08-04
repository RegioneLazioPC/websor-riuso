import Vue from "vue";

export default Vue.extend({
    template: `
        <span>
            {{this.value}}
        </span>
    `,
    data: function () {
        return {
            value: null
        };
    },
    beforeMount() {
    },
    mounted() {
        this.value = this.valueCubed();
    },
    methods: {
        valueCubed() {
            return this.params.value * this.params.value * this.params.value;
        }
    }
});