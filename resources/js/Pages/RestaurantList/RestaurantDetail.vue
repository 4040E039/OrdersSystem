<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <restaurant-nav :id="id"/>
            </h2>
        </template>
        <div class="py-3 sm:py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 font-semibold">
            <description-lists class="border rounded-lg border-gray-300 mx-4 ">
              <template #title>
                {{ restaurantDetail.Name }}
                <jet-button type="button" class="float-right" @click="edit(id)">
                  Edit
                </jet-button>
              </template>
              <template #content>
                <div v-for="(item, index) of restaurantDetail" :key="index" class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    {{ index }}
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ item }}
                  </dd>
                </div>
              </template>
            </description-lists>
            <!-- post card -->
            <div class="shadow-lg rounded-lg mx-4 my-12 border border-gray-300">
              <div class="flex items-center justify-between px-4 py-3 w-full bg-gray-200 w-full">
                <div class="text-gray-700 text-lg">
                  Comments
                </div>
                <div v-show="! isRelease" class="text-gray-700 text-lg mt-1">
                  <jet-button v-if="! isRelease" type="button" @click="handlerCommentModal(0)">
                    Add
                  </jet-button>
                </div>
              </div>
              <div class="divide-y divide-gray-300" v-show="restaurantComments">
                <div v-for="comment of restaurantComments" :key="comment.id" class="flex items-start px-4 py-6 w-full bg-white">
                    <img class="w-12 h-12 rounded-full mr-4 shadow border-2 border-gray-400" :src="comment.profile_photo_path" :alt="comment.name">
                    <div class="w-full">
                      <div class="flex items-center justify-between">
                          <h2 class="text-lg font-semibold text-gray-900 -mt-1">{{comment.name}}</h2>
                          <small class="text-sm text-gray-700 mr-2">{{handlerReleaseTime(comment.updated_at)}}</small>
                      </div>
                      <p class="space-x-2 flex mt-1">
                        <solid-star-icon v-for="index of comment.score" :key="index" class="w-4 h-4 text-yellow-400"/>
                        <outline-star-icon v-for="index of (5 - comment.score)" :key="index" class="w-4 h-4 text-yellow-400"/>
                      </p>
                      <div class="flex items-center justify-between mt-1">
                        <p class="mt-3 text-gray-600 text-sm">
                          {{comment.message}}
                        </p>
                        <div v-if="$attrs.user.id === comment.user_id">
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
                                    <button @click="handlerCommentModal(comment.id)" :class="[ active ? 'bg-indigo-500 text-white' : 'text-gray-900', 'group flex rounded-md items-center w-full px-2 py-2 text-sm' ]" >
                                      <pencil-icon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                      Edit
                                    </button>
                                  </menu-item>
                                  <menu-item v-slot="{ active }">
                                    <button @click="removeComment(comment.id)" :class="[ active ? 'bg-indigo-500 text-white' : 'text-gray-900', 'group flex rounded-md items-center w-full px-2 py-2 text-sm' ]" > 
                                    <trash-icon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                      Remove
                                    </button>
                                  </menu-item>
                                </div>
                              </menu-items>
                            </transition>
                          </Menu>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <add-restaurant-comments-modal :isEdit="isEdit" :restaurantId="id" v-if="openModel" :open-model="openModel" @close-modal="closeModal" />
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DescriptionLists from '@/Component/Tailwind/DescriptionLists'
    import RestaurantNav from '@/Pages/RestaurantList/RestaurantNav'
    import { reactive } from "vue";
    import JetButton from '@/Jetstream/Button'
    import { StarIcon as SolidStarIcon } from '@heroicons/vue/solid'
    import { StarIcon as OutlineStarIcon } from '@heroicons/vue/outline'
    import AddRestaurantCommentsModal from '@/Pages/RestaurantList/AddRestaurantCommentsModal'
    import moment from 'moment'
    import { ChatIcon } from '@heroicons/vue/solid'
    import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
    import { DotsVerticalIcon } from '@heroicons/vue/solid'
    import { PencilIcon } from '@heroicons/vue/solid'
    import { TrashIcon } from '@heroicons/vue/solid'

    export default {
        components: { AppLayout, DescriptionLists, JetButton, RestaurantNav, SolidStarIcon, OutlineStarIcon, AddRestaurantCommentsModal, ChatIcon, DotsVerticalIcon, Menu, MenuButton, MenuItems, MenuItem, PencilIcon, TrashIcon },
        props: {
          restaurantDetail: Object,
          id: String,
          restaurantComments: Array,
          isRelease: {
            type: Boolean,
            default: false,
          }
        },
        data() {
          return {
            openModel: false,
            isEdit: 0
          }
        },
        setup(props){
          let restaurantDetail = reactive(props.restaurantDetail)
          const handlerReleaseTime = (time) => {
            let diffsec = -(moment(time).diff(moment(), "seconds"))
            if(diffsec > 86400){
              return -(moment(time).diff(moment(), "days")) + ' days ago'
            } else if(diffsec > 3600) {
              return -(moment(time).diff(moment(), "hours")) + ' hours ago'
            } else if(diffsec > 60) {
              return -(moment(time).diff(moment(), "minutes")) + ' minutes ago'
            } else {
              return 'A few seconds ago'
            }
          }
          return { restaurantDetail, handlerReleaseTime }
        },
        methods: {
           edit(id) {
            this.$inertia.get(`/restaurant-list/edit/${id}`)
           },
           handlerCommentModal(id) {
             this.openModel = true
             this.isEdit = id
           },
          removeComment(id) {
            if(confirm('Do you want to delete this comment?')) {
                axios.delete(`/restaurant-comment-api/${id}`)
                .then(response => { 
                  if(response.data.messages === "") setTimeout(() => this.$inertia.reload(), 100 )
                  else alert(response.data.messages)
                })
             }
          },
          closeModal(val) {
             this.openModel = val
             setTimeout(() => this.$inertia.reload(), 100 )
          },
        },
    }
</script>