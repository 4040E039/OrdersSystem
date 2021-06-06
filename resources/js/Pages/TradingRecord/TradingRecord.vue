<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Trading Record
            </h2>
        </template>

        <div class="py-3 sm:py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-end mb-4">
                    <inertia-link :href="route('trading-record.chat')">
                      <button class="p-2 mr-2 text-center"> 
                        <chart-bar-icon class="w-5 h-5 text-gray-500"/>
                      </button>
                    </inertia-link>
                  <jet-search-input placeholder="search trading record" @search="search"/>
                </div>
                <div>
                  <tailwind-table :table-header="tableHeaderData" :table-list="tradingRecord" :has-action="action" @detail="viewDetail" @remove="remove">
                  </tailwind-table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmsPassword from '@/Jetstream/ConfirmsPassword'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import { ref, onMounted, watch } from "vue";
    import TailwindTable from '@/Component/Tailwind/TailwindTable'
    import JetSearchInput from '@/Jetstream/SearchInput'
    import { ChartBarIcon } from '@heroicons/vue/solid'

    export default {
        components: {
            JetActionSection,
            JetButton,
            JetConfirmsPassword,
            JetDangerButton,
            JetSecondaryButton,
            AppLayout,
            TailwindTable,
            JetSearchInput,
            ChartBarIcon
        },
        data() {
            return {
              tableHeaderData: ["Item", "Cost"],
              action: ['detail', 'remove']
            }
        },
        setup(){
          let loading = ref(false)
          let tradingRecord = ref([])
          let searchValue = ref("")
          const getTradingRecord = async () => {
            if(loading.value) return
            await axios.post(route('trading-record-api.index'),{
              search: searchValue.value
            }).then(response => {
                tradingRecord.value = response.data
                loading.value = true
            })
          }

          onMounted(getTradingRecord)
          watch(loading, getTradingRecord)
          return { tradingRecord, getTradingRecord, loading, searchValue }
        },
        methods: {
          viewDetail(id) {
            this.$inertia.get(`trading-record-list/${id}`)
          },
          search(data) {
            this.searchValue = data
            this.loading = false
          },
          remove(id) {
             if(confirm('Do you want to delete this trading record?')) {
                axios.delete(`trading-record-api/${id}`)
                .then(response => {
                  if(response.data.messages === "") this.loading = false 
                  else alert(response.data.messages)
                })
             }
          },
        },
    }
</script>
