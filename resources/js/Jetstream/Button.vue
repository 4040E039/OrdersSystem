<template>
    <button :type="type" :class="themeStyle" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition">
        <slot></slot>
    </button>
</template>

<script>
    import { ref } from "vue";
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
            theme: {
                type: String,
                default: 'default',
            },
        },
        setup(props){
          let themeStyle = ref("");
          switch (props.theme) {
            case 'default':
              themeStyle.value = "bg-gray-800 active:bg-gray-900 hover:bg-gray-700 focus:border-gray-900 focus:ring-gray-300"
              break;
            case 'danger':
              themeStyle.value = "bg-red-700 active:bg-red-900 hover:bg-red-600 focus:border-red-900 focus:ring-red-300"
              break;
            case 'success':
              themeStyle.value = "bg-emerald-700 active:bg-emerald-900 hover:bg-emerald-600 focus:border-emerald-900 focus:ring-emerald-300"
              break;
          }
          return { themeStyle }
        }
        
    }
</script>
