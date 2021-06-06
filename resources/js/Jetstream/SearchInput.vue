<template>
  <div class="relative rounded-md shadow-sm">
    <div class="absolute inset-y-0 left-0 px-3 flex items-center pointer-events-none">
      <span class="text-gray-500 sm:text-sm">
         <search-icon class="h-5 w-5 text-gray-500"/>
      </span>
    </div>
    <jet-input id="search" type="text" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-9 pr-12 sm:text-sm border-gray-300 rounded-md" :placeholder="placeholder" v-model="search" autocomplete="search" />
  </div>
</template>

<script>
    import JetInput from '@/Jetstream/Input'
    import { ref, watch } from "vue";
    import { SearchIcon } from '@heroicons/vue/solid'

    export default {
        components: { JetInput, SearchIcon },
        props: ['input', 'placeholder'],
        emits: ['search'],
        setup(props, {emit}){
          let search = ref(props.input)
          let searchData = () => {
            emit('search', search.value)
          }
          watch(search, searchData)
          return {search}
        }
    }
</script>

