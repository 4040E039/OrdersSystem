<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              Trading Record Detail
            </h2>
        </template>
        <div class="py-3 sm:py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 font-semibold">
            <description-lists class="border border-gray-300">
              <template #title>
                {{ tradingRecordDetail['Trading Item'] }}
                <jet-button type="button" class="float-right" @click="edit(id)">
                  Edit
                </jet-button>
              </template>
              <template #content>
                <div v-for="(item, index) of tradingRecordDetail" :key="index" class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    {{ index }}
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ item }}
                  </dd>
                </div>
              </template>
            </description-lists>
          </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DescriptionLists from '@/Component/Tailwind/DescriptionLists'
    import RestaurantNav from '@/Pages/RestaurantList/RestaurantNav'
    import { reactive } from "vue";
    import JetButton from '@/Jetstream/Button'

    export default {
        components: { 
          AppLayout, 
          DescriptionLists, 
          JetButton, 
          RestaurantNav 
        },
        props: {
          tradingRecordDetail: Object,
          id: String
        },
        setup(props){
          let tradingRecordDetail = reactive(props.tradingRecordDetail)
          return { tradingRecordDetail }
        },
        methods: {
           edit(id) {
            this.$inertia.get(`/trading-record-api/${id}/edit`)
           }
        },
    }
</script>