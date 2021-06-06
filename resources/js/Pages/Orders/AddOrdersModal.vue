<template>

    <!-- Add  Order Modal -->
    <jet-dialog-modal :show="openModel" @close="closeModal()">
        <template #title>
           Add Order Modal
        </template>

        <template #content>
          <div class="mt-3">
          <!-- Order Item -->
              <jet-label for="OrderItem" value="Order Item" />
              <jet-input type="text" id="order_item" class="mt-1 block w-full" v-model="form.order_item" autocomplete="order_item" />
              <jet-input-error :message="errors.order_item" class="mt-2" />
          </div>
          <!-- Order Quantity -->
          <div class="mt-3">
              <jet-label for="order_quantity" value="Order Quantity" />
              <jet-input type="number" min=0 id="order_quantity" class="mt-1 block w-full" v-model="form.order_quantity" autocomplete="order_quantity" />
              <jet-input-error :message="errors.order_quantity" class="mt-2" />
          </div>
          <!-- Order Cost -->
          <div class="mt-3">
              <jet-label for="order_cost" value="Unit Order Cost" />
              <jet-input type="number" min=0 id="order_cost" class="mt-1 block w-full" v-model="form.order_cost" autocomplete="order_cost" />
              <jet-input-error :message="errors.order_cost" class="mt-2" />
          </div>
          <!-- Memo -->
          <div class="mt-3 mb-3">
              <jet-label for="memo" value="Memo" />
              <jet-textarea id="memo" class="mt-1 block w-full" v-model="form.memo" autocomplete="memo" />
          </div>
        </template>
        <template #footer>
            <jet-button type="text" class="ml-4" @click="save()">
                Save
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
    import { reactive, onMounted } from "vue";
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
          raiseOrderId: Number,
          isEdit: Number
        },
        setup(props) {
          const form = reactive({
            order_item: null,
            order_quantity: null,
            order_cost: null,
            memo: null,
            raise_order_id: props.raiseOrderId,
          })
          const errors = reactive({
            order_item: null,
            order_quantity: null,
            order_cost: null,
            memo: null,
          })
          if(props.isEdit) {
            const getOrder = async () => {
              await axios.get(`/orders-api/${props.isEdit}`).then(response => {
                form.order_item = response.data.order_item
                form.order_quantity = response.data.order_quantity
                form.order_cost = response.data.order_cost
                form.memo = response.data.memo
              })
            }
            onMounted(getOrder)
          }
          return {
            form,
            errors,
          };
        },
        methods: {
          handlerErrorMessage(errorMessage) {
            for(let index in errorMessage) {
              this.errors[index] = ""
              for(let val of errorMessage[index]) {
                if(val) this.errors[index] += `${val} `
              }
            }
          },
          save() {
            if(this.isEdit) {
              axios.put(`/orders-api/${this.isEdit}`, {form: this.form})
              .then(response => {
                if(response.data.messages === "") this.closeModal()
                else this.handlerErrorMessage(response.data.messages)
              })
            } else {
              axios.post(route('orders-api.store'), {form: this.form})
              .then(response => {
                if(response.data.messages === "") this.closeModal()
                else this.handlerErrorMessage(response.data.messages)
              })
            }
          },
          closeModal() {
            this.$emit('close-modal', false)
          }
        },
    }
</script>