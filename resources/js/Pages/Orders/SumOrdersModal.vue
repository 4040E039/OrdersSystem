<template>
    <jet-dialog-modal max-width="3xl" :show="openModel" @close="closeModal()">
        <template #title>
           Sum Order Modal
        </template>

        <template #content>
          <div v-show="restaurant">
            <div class="grid gap-4 py-3 text-center grid-cols-5 bg-gray-500 text-white rounded-t-md" >
              <div>Name</div>
              <div class="col-span-2">Telephone</div>
              <div class="col-span-2">Address</div>
            </div>
            <div class="divide-y divide-gray-300 font-semibold border-2 border-gray-400 rounded-b-md mb-3">
              <div class="grid gap-4 py-3 text-center grid-cols-5" >
                <div>{{ restaurant.restaurant_name }}</div>
                <div class="col-span-2">{{ restaurant.restaurant_telephone }}</div>
                <div class="col-span-2">{{ restaurant.restaurant_address }}</div>
              </div>
            </div>
          </div>
          <div v-show="sumOrder">
            <div class="grid gap-4 py-3 text-center grid-cols-5 bg-gray-500 text-white rounded-t-md">
              <div>Order Item</div>
              <div>Order Quantity</div>
              <div>Order Cost</div>
              <div class="col-span-2">Memo</div>
            </div>
            <div class="pb-4 divide-y divide-gray-300 font-semibold border-2 border-gray-400 rounded-b-md">
              <div v-for="(item, index) of sumOrder" :key="index" class="grid gap-4 py-3 text-center grid-cols-5 " >
                <div>{{ item.order_item }}</div>
                <div>{{ item.order_quantity_sum }}</div>
                <div>$  {{ item.order_cost_sum }}</div>
                <div class="col-span-2">{{ item.memo }}</div>
              </div>
              <div v-show="handlerTotal" class="grid gap-4 py-3 mt-8 text-center grid-cols-5" >
                <div class="col-span-1"></div>
                <div class="col-span-2 text-indigo-700 text-lg font-bold text-left">Total</div>
                <div class="col-span-2 text-indigo-700 text-lg font-bold">$ {{handlerTotal}}</div>
              </div>
            </div>
          </div>
        </template>
        <template #footer>
            <jet-button type="button" class="ml-4" @click="closeModal()">
                Close
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetTextarea from '@/Jetstream/Textarea'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import { onMounted, ref, reactive, watch } from "vue";
    import _ from 'lodash'
    
    export default {
        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetLabel,
            JetTextarea,
        },
        emits: ["close-modal"],
        props: {
          openModel: Boolean,
          raiseOrderId: String,
        },
        setup(props) {
          const sumOrder = ref(false)
          const restaurant = ref(false)
          const handlerTotal = ref(0)
          const modalParam = reactive(props)

          const getSumOrder = async () => {
            // init
            sumOrder.value = false
            restaurant.value = false
            handlerTotal.value = 0

            if(props.openModel) {
              await axios.get(`/orders-api/sum/${props.raiseOrderId}`).then(response => {
                sumOrder.value = response.data.orders
                restaurant.value = response.data.restaurant
                for(let row of response.data.orders) handlerTotal.value += _.ceil(row.order_cost_sum, 2)
              })
            }
          }

          onMounted(getSumOrder)
          watch(modalParam, getSumOrder)
          return {
            sumOrder,
            restaurant,
            handlerTotal
          };
        },
        methods: {
          closeModal() {
            this.$emit('close-modal', false)
          }
        },
    }
</script>