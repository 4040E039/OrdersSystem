<template>
  <div :class="themeStyle" class="text-white w-full px-6 py-4 border-0 rounded absolute bottom-0 right-0">
    <span class="inline-block align-middle mr-8">
      <b class="capitalize">
        <slot name="alertContent"></slot>
      </b>
    </span>
    <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" v-on:click="closeAlert()">
      <span>×</span>
    </button>
  </div>
</template>

<script>
export default {
  name: "alert",
  props: {
    theme: {
        type: String,
        default: 'default',
    },
    alertOpen: {
      type: Boolean,
      default: false,
    },
  },
  emits:['closeAlert'],
  computed: {
    themeStyle() {
      switch (this.theme) {
        case 'default':
          return 'bg-gray-500'
        case 'success':
          return 'bg-green-500'
        case 'danger':
          return 'bg-red-500'
        default:
          return 'bg-gray-500'
      }
    }
  },
  methods: {
    closeAlert: function(){
      this.$emit('closeAlert', false)
    }
  }
}
</script>