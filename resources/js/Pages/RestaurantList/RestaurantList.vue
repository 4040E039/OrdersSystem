<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Restaurant List
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-end mb-4">
                    <jet-search-input placeholder="search theme name" @search="search"/>
                    <jet-button type="text" class="ml-4" @click="addRestaurant()">
                        Add Restaurant
                    </jet-button>
                </div>
                <div>
                  <tailwind-table :table-header="tableHeaderData" :table-list="restaurants" :has-action="action" @detail="viewDetail" @remove="remove">
                  </tailwind-table>
                </div>
            </div>
        </div>
        <add-restaurant-modal :open-model="openModel" @close-modal="closeModal" />
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import TailwindTable from '@/Component/Tailwind/TailwindTable'
    import AddRestaurantModal from '@/Pages/RestaurantList/AddRestaurantModal'
    import JetSearchInput from '@/Jetstream/SearchInput'
    import { ref, onMounted, watch } from "vue";
    
    export default {
        components: { 
          JetButton, 
          AppLayout, 
          TailwindTable, 
          AddRestaurantModal, 
          JetSearchInput 
        },
        data() {
          return {
            openModel: false,
            tableHeaderData: ["Name", "Telephone", "Address"],
            action: ['detail', 'remove']
          }
        },
        setup(){
          let loading = ref(false)
          let restaurants = ref([])
          let searchValue = ref("")
          const getRestaurants = async () => {
            if(loading.value) return
            await axios.get(`restaurant-api?search=${searchValue.value}`)
            .then(response => {
                restaurants.value = response.data
                loading.value = true
            })
          }

          onMounted(getRestaurants)
          watch(loading, getRestaurants)
          return { restaurants, getRestaurants, loading, searchValue }
        },

        methods: {
          addRestaurant() {
             this.openModel = true
          },
          closeModal(val) {
             this.openModel = val
             this.loading = val
          },
          viewDetail(id) {
            this.$inertia.get(`restaurant-api/${id}`)
          },
          remove(id) {
             if(confirm('Do you want to delete this restaurant?')) {
                axios.delete(`restaurant-api/${id}`)
                .then(response => {
                  if(response.data.messages === "") this.loading = false 
                  else alert(response.data.messages)
                })
             }
          },
          search(data) {
            this.searchValue = data
            this.loading = false
          }
        }
    }
</script>