<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              <div class="flex justify-between">
                <div>Start Order - {{raiseOrder.raise_order_theme}}</div>
                  <div class="flex items-center space-x-3">
                    <clock-icon class="h-5 w-5 text-gray-500 animate-bounce "/> 
                    <div>{{RemainingTime()}}</div>
                </div>
              </div>
            </h2> 
        </template>
        <div class="p-3 sm:py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end items-center space-x-2 mb-3">
              <button v-if="countDown > 0 " @click="handlerOrdersModal(0)" title="add order" class="p-2 focus:outline-none focus:ring focus:border-gray-100 rounded-full">
               <plus-circle-icon class="h-5 w-5 text-gray-500"/>
              </button>
              <button @click="this.handlerRaiseOrdersDetailDisplay = ! this.handlerRaiseOrdersDetailDisplay" title="view raise order detail" class="p-2 focus:outline-none focus:ring focus:border-gray-100 rounded-full">
               <information-circle-icon class="h-5 w-5 text-gray-500"/>
              </button>
              <button class="p-2 focus:outline-none focus:ring focus:border-gray-100 rounded-full" title="go to restaurant photos">
                <inertia-link :href="restaurantPhotosUrl">
                  <photograph-icon class="h-5 w-5 text-gray-500" />
                </inertia-link>
              </button>
              <div v-show="raiseOrder.is_found === 0 && countDown < 0 && raiseOrder.user_id === $attrs.user.id" title="completed order">
                <button @click="raiseOrderCompleted(id)" class="p-2 focus:outline-none focus:ring focus:border-gray-100 rounded-full">
                  <clipboard-check-icon class="h-5 w-5 text-gray-500"/>
                </button>
              </div>
              <div v-show="raiseOrder.user_id === $attrs.user.id" title="view sum orders">
                <button @click="handlerSumOrdersModal" class="p-2 focus:outline-none focus:ring focus:border-gray-100 rounded-full">
                  <eye-icon class="h-5 w-5 text-gray-500"/>
                </button>
              </div>
              <jet-search-input placeholder="search uesr name" @search="search"/>
            </div>
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95">
              <div class="transition mt-1" v-show="handlerRaiseOrdersDetailDisplay">
                <description-lists class="mb-4 border border-gray-300">
                  <template #title>
                    {{ raiseOrder.raise_order_theme }}
                  </template>
                  <template #content>
                    <div v-for="(item, index) of RaiseOrderSort" :key="index" class="font-semibold px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        <span>{{ index }}</span>
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <inertia-link v-if="index === 'Restaurant'" :href="restaurantDetailUrl">
                          <span class="text-blue-600 font-bold" >{{ item }}</span>
                        </inertia-link>
                        <span v-else>{{ item }}</span>
                      </dd>
                    </div>
                  </template>
                </description-lists>
              </div>
            </transition>
            <!-- Start of Order Cards component -->
            <div v-show="orders" class="grid sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
              <div v-for="(order, index) of orders" :key="index" class="w-full bg-white border-2 border-gray-300 p-4 rounded-md tracking-wide shadow-lg">
                <div id="header" class="flex items-center mb-4"> 
                  <img :alt="order[0].name" class="w-16 rounded-full border-2 border-gray-300" :src="order[0].profile_photo_path" />
                  <div id="header-text" class="leading-5 ml-6 space-y-1">
                    <p class="text-xl font-semibold">{{order[0].name}}</p>
                    <p class="font-semibold text-blue-600 text-sm">$ {{handlerTotal(order)}}</p>
                  </div>
                </div>
                <div class="border-t-2"></div>
                <div>
                  <div v-for="detail of order" :key="detail.id" :class="[ countDown > 0 && $attrs.user.id === detail.user_id ? 'grid-cols-4' : 'grid-cols-3' ]" class="grid gap-2 mt-3 text-center">
                    <div>{{ detail.order_item }}</div>
                    <div>x {{ detail.order_quantity }}</div>
                    <div>$ {{ detail.order_cost }}</div>
                    <div class="relative" v-if="countDown > 0 && $attrs.user.id === detail.user_id">
                      <Menu as="div" class="relative inline-block text-left">
                        <div>
                          <menu-button class="p-1 focus:outline-none focus:ring" >
                            <dots-vertical-icon class="h-4 w-4 text-gray-500"/>
                          </menu-button>
                        </div>
                        <transition
                          enter-active-class="transition duration-100 ease-out"
                          enter-from-class="transform scale-95 opacity-0"
                          enter-to-class="transform scale-100 opacity-100"
                          leave-active-class="transition duration-75 ease-in"
                          leave-from-class="transform scale-100 opacity-100"
                          leave-to-class="transform scale-95 opacity-0"
                        >
                          <menu-items
                            class="z-10 absolute top-6 right-0 ring-1 ring-black ring-opacity-5 w-28 mt-2 bg-white shadow-xl focus:outline-none" >
                            <div class="px-1 py-1">
                              <menu-item v-slot="{ active }">
                                <button @click="handlerOrdersModal(detail.id)" :class="[ active ? 'bg-indigo-500 text-white' : 'text-gray-900', 'group flex rounded-md items-center w-full px-2 py-2 text-sm' ]" >
                                  <pencil-icon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                  Edit
                                </button>
                              </menu-item>
                              <menu-item v-slot="{ active }">
                                <button @click="removeOrder(detail.id)" :class="[ active ? 'bg-indigo-500 text-white' : 'text-gray-900', 'group flex rounded-md items-center w-full px-2 py-2 text-sm' ]" > 
                                <trash-icon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                  Remove
                                </button>
                              </menu-item>
                            </div>
                          </menu-items>
                        </transition>
                      </Menu>
                    </div>
                    <div v-if="detail.memo" class="col-span-full text-xs text-gray-500 text-left italic ml-3">“ {{ detail.memo }} ”</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of component -->
          </div>
        </div>
        <add-orders-modal :isEdit="isEdit" :raiseOrderId = "id" :open-model="openAddOrdersModel" @close-modal="closeAddOrderModal" />
        <sum-orders-modal :raiseOrderId = "id" :open-model="openSumOrdersModel" @close-modal="closeSumOrderModal" />
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import RestaurantNav from '@/Pages/RestaurantList/RestaurantNav'
    import { reactive, ref, onMounted, watch } from "vue";
    import JetButton from '@/Jetstream/Button'
    import { InformationCircleIcon } from '@heroicons/vue/solid'
    import { PhotographIcon } from '@heroicons/vue/solid'
    import DescriptionLists from '@/Component/Tailwind/DescriptionLists'
    import moment from 'moment'
    import { ClockIcon } from '@heroicons/vue/outline'
    import { DotsVerticalIcon } from '@heroicons/vue/solid'
    import { PlusCircleIcon } from '@heroicons/vue/solid'
    import { PencilIcon } from '@heroicons/vue/solid'
    import { TrashIcon } from '@heroicons/vue/solid'
    import { ClipboardCheckIcon } from '@heroicons/vue/solid'
    import { EyeIcon } from '@heroicons/vue/solid'
    import AddOrdersModal from '@/Pages/Orders/AddOrdersModal'
    import SumOrdersModal from '@/Pages/Orders/SumOrdersModal'
    import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
    import JetSearchInput from '@/Jetstream/SearchInput'
    import _ from 'lodash'

    export default {
        components: { 
          AppLayout,
          DescriptionLists,
          JetButton,
          RestaurantNav,
          InformationCircleIcon,
          DescriptionLists,
          ClockIcon,
          PhotographIcon,
          DotsVerticalIcon,
          PlusCircleIcon,
          AddOrdersModal ,
          Menu,
          MenuButton,
          MenuItems,
          MenuItem,
          PencilIcon,
          TrashIcon,
          EyeIcon,
          JetSearchInput,
          ClipboardCheckIcon,
          SumOrdersModal
        },
        props: {
          id: Number,
          raiseOrder: Object,
        },
        data() {
          return {
            openAddOrdersModel: false,
            openSumOrdersModel: false,
            isEdit: 0
          }
        },
        setup(props){
          const loading = ref(false)
          const orders = ref(false)
          const searchValue = ref("")

          const getOrders = async () => {
            if(loading.value) return
            await axios.post(route('orders-api.show'),{
              search: searchValue.value,
              raiseOrderId: props.raiseOrder.id
            }).then(response => {
                orders.value = response.data
                loading.value = true
            })
          }
          const RaiseOrderSort = reactive({
            "Restaurant": props.raiseOrder.restaurant_name,
            "Theme": props.raiseOrder.raise_order_theme,
            "Start Time": props.raiseOrder.start_time,
            "End Time": props.raiseOrder.end_time,
            "Memo": props.raiseOrder.memo,
          })
          const countDown = ref(moment(props.raiseOrder.end_time).diff(moment(), "seconds"));
          const checkTimer = () => {
            if(countDown.value > 0) {
              setTimeout(()=> {
                countDown.value = moment(props.raiseOrder.end_time).diff(moment(), "seconds")
              }, 1000);
            } 
          }
          const handlerRaiseOrdersDetailDisplay = ref(false);
          const restaurantDetailUrl = ref(`/restaurant-list/${props.raiseOrder.restaurant_id}`)
          const restaurantPhotosUrl = ref(`/restaurant-list/photos/${props.raiseOrder.restaurant_id}`)
          const RemainingTime = () => {
            let time = "00 : 00 : 00"
            if(countDown.value > 0) {
              let hours = Math.floor(countDown.value / 3600)+''
              let minute = Math.floor((countDown.value - (hours * 3600)) / 60)+''
              let seconds = parseInt( countDown.value - (hours * 3600) - (minute * 60))+''
              if(hours.length < 2) hours = `0${hours}`
              if(minute.length < 2) minute = `0${minute}`
              if(seconds.length < 2) seconds = `0${seconds}`
              time = `${hours} : ${minute} : ${seconds}`
            }
            return time
          }
          onMounted(getOrders)
          onMounted(checkTimer)
          watch(countDown, checkTimer)
          watch(loading, getOrders)
          return { RaiseOrderSort, handlerRaiseOrdersDetailDisplay, countDown, RemainingTime, restaurantDetailUrl, restaurantPhotosUrl, orders, loading, searchValue };
        },
        methods: {
          closeAddOrderModal(val) {
            this.openAddOrdersModel = val
            this.loading = false
          },
          closeSumOrderModal(val) {
            this.openSumOrdersModel = val
          },
          handlerOrdersModal(id) {
            this.openAddOrdersModel = true
            this.isEdit = id
          },
          removeOrder(id) {
            if(confirm('Do you want to delete this order?')) {
                axios.delete(`/orders-api/${id}`)
                .then(response => { 
                  if(response.data.messages === "") this.loading = false
                  else alert(response.data.messages)
                })
             }
          },
          handlerTotal(order) {
            let total = 0;
            for(let row of order) {
              total += _.ceil(row.order_cost, 2);
            }
            return total
          },
          raiseOrderCompleted(id) {
            if(confirm('Do you want to raise order complete?')) {
                axios.put(`/raise-orders-api/completed/${id}`)
                .then(response => { 
                  if(response.data.messages === "") setTimeout(() => this.$inertia.reload(), 100 )
                  else alert(response.data.messages)
                })
             }
          },
          search(data) {
            this.searchValue = data
            this.loading = false
          },
          handlerSumOrdersModal() {
            this.openSumOrdersModel = true
          },
        },
    }
</script>