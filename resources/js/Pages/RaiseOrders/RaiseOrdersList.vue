<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Raise Orders List
            </h2>
        </template>
        <div class="py-3 sm:py-12">
          <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="flex justify-end flex-wrap items-center mb-10 space-x-2 sm:space-y-0 space-y-1">
               <date-picker v-model="range" is-range is-required> 
                  <template v-slot="{ inputValue, inputEvents }">
                    <div class="flex justify-end flex-wrap items-center space-x-2">
                      <jet-input
                        :value="inputValue.start"
                        v-on="inputEvents.start"
                        class="p-2 sm:text-sm border rounded focus:outline-none focus:border-blue-300"
                      />
                      <arrow-narrow-right-icon class="h-5 w-5 text-gray-500 sm:inline-block hidden" />
                      <jet-input
                        :value="inputValue.end"
                        v-on="inputEvents.end"
                        class="p-2 sm:text-sm border rounded focus:outline-none focus:border-blue-300"
                      />
                    </div>
                  </template>
               </date-picker>
              <tailwind-select :options="options" :selected="selected" @header-change="headerChange" class="w-52" />
              <jet-search-input placeholder="search theme name" @search="search"/>
              <jet-button @click="handlerRaiseOrdersModal(0)">
                  add Raise Orders
              </jet-button>
            </div>
            <!-- component -->
            <div v-show="raiseOrders" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="raiseOrder of raiseOrders" :key="raiseOrder.id" class="relative bg-white py-5 px-7 rounded-3xl w-64 my-3 shadow-xl mx-auto">
                    <div class="bg-white flex items-center absolute rounded-full p-0.5 shadow-xl left-4 -top-6 border-2 border-gray-400">
                        <img class="h-14 w-14 rounded-full object-cover" :src="handlerPhoto(raiseOrder.profile_photo_path, raiseOrder.name)" />
                    </div>
                    <div class="w-full flex justify-end space-x-3">
                      <button v-if="raiseOrder.user_id === $attrs.user.id" class="bg-transparent text-gray-500" @click="removeRaiseOrders(raiseOrder.id)">
                        <trash-icon class="w-5 h-5 text-gray-500" />
                      </button>
                      <button v-if="raiseOrder.user_id === $attrs.user.id" class="bg-transparent text-gray-500" @click="handlerRaiseOrdersModal(raiseOrder.id)">
                         <pencil-alt-icon class="h-5 w-5 text-gray-500"/>
                      </button>
                      <button v-if="raiseOrder.status === 1 || raiseOrder.status === 2" class="bg-transparent text-gray-500" @click="orders(raiseOrder.id)">
                         <chat-icon class="h-5 w-5 text-gray-500"/>
                      </button>
                    </div>
                    <div class="mt-8">
                        <p class="text-xl font-semibold my-2">{{raiseOrder.raise_order_theme}}</p>
                        <div class="flex space-x-2 text-gray-400 text-sm">
                            <outline-clock-icon class="h-5 w-5 text-gray-500"/>
                            <p>{{raiseOrder.start_time}}</p> 
                        </div>
                        <div class="flex space-x-2 text-gray-400 text-sm my-3">
                            <solid-clock-icon class="h-5 w-5 text-gray-500"/>
                            <p>{{raiseOrder.end_time}}</p> 
                        </div>
                        <div class="border-t-2"></div>
                        <div class="flex justify-between">
                            <div class="my-2">
                              <p class="font-semibold text-base mb-2">Status</p>
                              <div class="flex flex-wrap">
                                <span v-if="raiseOrder.status === 1" class="inline-block rounded-full text-white bg-gradient-to-r from-blue-400 to-indigo-400 px-2 py-1 text-xs font-bold mr-3">open</span>
                                <span v-if="raiseOrder.status === 2" class="inline-block rounded-full text-white bg-yellow-500 px-2 py-1 text-xs font-bold mr-3">ancient</span>
                                <span v-if="raiseOrder.status === 3" class="inline-block rounded-full text-white bg-gray-500 px-2 py-1 text-xs font-bold mr-3">not yet</span>
                                <span v-if="raiseOrder.is_found" class="inline-block rounded-full text-white bg-indigo-500 px-2 py-1 text-xs font-bold mr-3">completed</span>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <add-raise-orders-modal :isEdit="isEdit" :open-model="openModel" @close-modal="closeModal" />
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import TailwindSelect from '@/Component/Tailwind/TailwindSelect'
    import AddRaiseOrdersModal from '@/Pages/RaiseOrders/AddRaiseOrdersModal'
    import JetSearchInput from '@/Jetstream/SearchInput'
    import { ref, onMounted, watch } from "vue";
    import { ClockIcon as SolidClockIcon} from '@heroicons/vue/solid'
    import { ClockIcon as OutlineClockIcon} from '@heroicons/vue/outline'
    import { ArrowNarrowRightIcon } from '@heroicons/vue/solid'
    import { PencilAltIcon } from '@heroicons/vue/solid'
    import { ChatIcon } from '@heroicons/vue/solid'
    import { DatePicker } from 'v-calendar';
    import JetInput from '@/Jetstream/Input'
    import moment from 'moment'
    import { TrashIcon } from '@heroicons/vue/solid'

    export default {
        components: { 
          JetButton, 
          AppLayout, 
          AddRaiseOrdersModal, 
          JetSearchInput, 
          OutlineClockIcon, 
          SolidClockIcon,
          ArrowNarrowRightIcon,
          TailwindSelect,
          DatePicker,
          JetInput,
          PencilAltIcon,
          ChatIcon,
          TrashIcon
        },
        data() {
          return {
            openModel: false,
            isEdit: 0,
            options: [
                {
                  name: 'open',
                  value: 1
                },
                {
                  name: 'ancient',
                  value: 2
                },
                {
                  name: 'not yet',
                  value: 3
                },
              ],
          }
        },
        setup(){
          const loading = ref(false)
          const raiseOrders = ref([])
          const selected = ref(1);
          const searchValue = ref("")
          const range = ref({
            start: moment().startOf("month").format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD'),
          })

          const getRaiseOrders = async () => {
            if(loading.value) return
            await axios.post(route('raise-orders-api.index'),{
              status: selected.value,
              search: searchValue.value,
              searchDateRange: range.value
            }).then(response => {
                raiseOrders.value = response.data
                loading.value = true
            })
          }
          const reload = () => {
            range.value.start = moment(range.value.start).format('YYYY-MM-DD')
            range.value.end = moment(range.value.end).format('YYYY-MM-DD')
            loading.value = false
          }
          onMounted(getRaiseOrders)
          watch(range, reload)
          watch(loading, getRaiseOrders)
          return { raiseOrders, getRaiseOrders, loading, selected, searchValue, range }
        },

        methods: {
          headerChange(selected) {
            this.selected = selected.value
            this.loading = false
          },
          search(data) {
            this.searchValue = data
            this.loading = false
          },
          handlerRaiseOrdersModal(id) {
            this.openModel = true
            this.isEdit = id
          },
          closeModal(val) {
            this.openModel = val
            this.loading = val
          },
          handlerPhoto(path, name) {
            if(path) return `storage/${path}`;
            else return `https://ui-avatars.com/api/?name=${name}&color=7F9CF5&background=EBF4FF`;
          },
          orders(id) {
            this.$inertia.get(`orders/${id}`)
          },
          removeRaiseOrders(id) {
            if(confirm('Do you want to delete this raise order?')) {
              axios.delete(`/raise-orders-api/${id}`)
              .then(response => { 
                if(response.data.messages === "") this.loading = false
                else alert(response.data.messages)
              })
            }
          }
        }
    }
</script>