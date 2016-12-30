export default {
    data() {
        return {
            isSelected: false,
        }
    },
    methods: {
        toggleSelection: function () {
            this.isSelected = !this.isSelected;
        },
    }
}