<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <restaurant-nav :id="id"/>
            </h2>
        </template>
        <div class="py-3 sm:py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container mx-auto px-4"> 
              <section class="py-4 px-2">
                <viewer :images="photoList" class="flex flex-wrap w-full space-x-4">
                  <label style="height: 205px;" for="images" class="relative sm:w-1/5 mb-8 w-full">
                    <div class="w-full h-full flex items-center justify-center border-dashed border-2 border-gray-400 cursor-pointer hover:bg-gray-200 focus:ring-gray-300 transition">
                      <plus-icon class="h-5 w-5 text-gray-500" />
                      <input id="images" name="images" multiple="multiple" @change="uploadPhoto" accept="image/*" type="file" class="sr-only" />
                    </div>
                    </label>
                    <div style="height: 205px"  v-for="url of urlList" :key="url.id" class="sm:w-1/5 mx-4 mb-8 relative">
                      <img title="click me view photo" :src="url.path" class="rounded shadow-md cursor-pointer w-full h-full">
                      <button type="text" class="p-0.5 absolute -top-3 -right-3 bg-white text-red-500 border-transparent rounded-full z-10 transition font-semibold" @click="remove(url.id)">
                        <x-circle-icon class="h-7 w-7 text-red-500" />
                      </button>
                    </div>
                  </viewer>
              </section>
            </div>
          </div>
        </div>
        <tailwind-alert :theme="alertTheme" v-show="alertOpen" @closeAlert="closeAlert">
          <template #alertContent>
            {{ alertContent }}
          </template>
        </tailwind-alert>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DescriptionLists from '@/Component/Tailwind/DescriptionLists'
    import RestaurantNav from '@/Pages/RestaurantList/RestaurantNav'
    import JetButton from '@/Jetstream/Button'
    import { PlusIcon } from '@heroicons/vue/solid'
    import { XCircleIcon } from '@heroicons/vue/solid'
    import 'viewerjs/dist/viewer.css'
    import Viewer from "v-viewer/src/component"
    import TailwindAlert from '@/Component/Tailwind/TailwindAlert'

    export default {
        components: { 
          AppLayout, 
          DescriptionLists, 
          JetButton, 
          RestaurantNav, 
          PlusIcon, 
          Viewer, 
          XCircleIcon, 
          TailwindAlert 
        },
        props: {
          id: String,
          urlList: Array,
          photoList: Array
        },
        data() {
          return {
            alertOpen: false,
            alertContent: "",
            alertTheme: "",
          }
        },
        methods: {
            listErrorMessage(messages) {
            let errorMessage = ""
            for(let message in messages) {
              for(let error of messages[message]) {
                errorMessage += error
              }
            }
            return errorMessage
          },
          setTimeoutErrorMessage(messages, name = "") {
            this.alertOpen = true
            if(typeof(messages) === 'object' && Object.keys(messages).length && name !== "") {
              this.alertContent = `${name} fail to uploaded cause: ${this.listErrorMessage(messages)}`
              this.alertTheme = "danger"
            }
            setTimeout( () => this.alertOpen = false, 5000 )
          },
           uploadPhoto(event) {
              let count = event.target.files.length
              let reloadCount = 0
              while(count--) {
                const file = event.target.files[count];
                const form = new FormData();
                form.append('images', file);
                form.append('file_name', file.name);
                form.append('id', this.id);
                axios.post(route('restaurant-photos-api.store'), form).then(response => {
                  if(response.data.messages !== "") {
                     this.setTimeoutErrorMessage(response.data.messages, file.name)
                  }
                  reloadCount++
                  if(reloadCount === event.target.files.length) setTimeout(() => this.$inertia.reload(), 100 )
                });
              }
           },
           remove(id) {
             if(confirm('Do you want to delete this photo?')) {
              axios.delete(`/restaurant-photos-api/${id}`).then(response => {
                if(response.data.messages === "") setTimeout(() => this.$inertia.reload(), 100 )
                else alert(response.data.messages)
              });
             }
           },
          closeAlert() {
            this.alertOpen = false
            this.alertContent = ""
          }
        },
    }
</script>