<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Trading Record Chat
            </h2>
        </template>

        <div class="py-3 sm:py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white py-5">
              <div class="flex justify-between">
                <div class="text-lg">Monthly Cost</div>
                <bar-chart v-if="state.isLoaded" :width="500" :height="500" :chartData="this.state.chartData" :chartOptions="this.state.chartOptions" />
              </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetActionSection from '@/Jetstream/ActionSection'
    import BarChart from '@/Component/Chart/BarChart'

    export default {
        components: {
            JetActionSection,
            AppLayout,
            BarChart
        },
        data () {
          return {
            state: {
              chartData: null,
              chartOptions: {
                responsive: false,
                scales: {
                  yAxes: [{
                    ticks: {
                      beginAtZero: true
                    }
                  }]
                }
              },
              isLoaded: false
            }
          }
        },
        mounted () {
          this.state.isLoaded = false
          axios.get(route('trading-record-api.monthlyCost'))
          .then(response => {
            this.state.chartData = response.data
            this.state.isLoaded = true
          })
        }
    }
</script>
