<template>
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-300 sm:rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead :class="[setBackgroundColor, setFontColor, setFontSize]">
              <tr>
                <th v-for="title of tableHeader" :key="title" scope="col" class="px-6 py-3 text-left font-medium tracking-wider">
                  {{title}}
                </th>
                <th v-if="hasAction && hasAction.length > 0" scope="col" class="px-6 py-3 text-left font-medium tracking-wider">
                  Action
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="data of displayedData()" :key="data.id">
                  <td v-for="(item, index) of data" v-show="index !== 'id'" :key="index" class="px-6 py-4 whitespace-nowrap text-left">
                    <div class="text-gray-900">{{ item }}</div>
                  </td>  
                  <td v-if="hasAction && hasAction.length > 0" class="px-6 py-4 whitespace-nowrap text-left">
                    <jet-button type="button" :theme="handlerTheme(action)" v-for="(action, index) of hasAction" :key="index" class="ml-4" @click="handler(action, data.id)">
                        {{action}}
                    </jet-button>
                  </td>
                </tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end mt-5">
          <vue-tailwind-pagination v-show="total > 0" :current="currentPage" :total="total" :per-page="perPage"  @page-changed="currentPage = $event" />
        </div>
      </div>
    </div>
  </div>
  <div class="pr-6 px-10 transform rotate-45 hidden"><span class="transform -rotate-45"></span></div>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
    import VueTailwindPagination from '@ocrv/vue-tailwind-pagination'
    import { ref, onBeforeUpdate } from "vue";

    export default {
      components: { JetButton, ChevronLeftIcon, ChevronRightIcon, VueTailwindPagination},
      props: ['table-header', 'table-list', 'has-action'],
      emits: ['detail', 'edit', 'remove'],
      data() {
          return {
            thead: {
              backgroundColor: {
                'color': 'gray',
                'depth': '200'
              },
              fontColor: {
                'color': 'gray',
                'depth': '600'
              },
              fontSize: {
                'size': 'md',
              },
            },
          } 
      },
      setup(props){
        let currentPage = ref(1);
        let perPage = ref(5);
        let total = ref(0);
        let listdata = ref([]);

        const getData = () => {
          listdata.value = props.tableList
          total.value = props.tableList.length
        }

        const displayedData = () => {
          let page = currentPage.value;
          let perPage2 = perPage.value;
          let from = (page * perPage2) - perPage2;
          let to = (page * perPage2);
          return listdata.value.slice(from, to);
        }

        onBeforeUpdate(getData);
        return { currentPage, perPage, total, displayedData };
      },

      computed: {
        setBackgroundColor() {
          return `bg-${this.thead.backgroundColor.color}-${this.thead.backgroundColor.depth}`
        },
        setFontColor() {
          return `text-${this.thead.fontColor.color}-${this.thead.fontColor.depth}`
        },
        setFontSize() {
          return `text-${this.thead.fontSize.size}`
        }
      },
      methods: {
        handler(action, id) {
          this.$emit(action, id)
        },
        handlerTheme(action) {
          return (action === 'remove') ? 'danger' : 'default'
        },
      },
    }
</script>
