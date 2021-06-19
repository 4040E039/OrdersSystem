<template>
    <!-- Loading -->
    <tailwind-loading v-if="! handlerDisplay && openModel" />
    <!-- Add / Edit RaiseOrder Modal -->
    <jet-dialog-modal :show="openModel" v-else @close="closeModal()">
        <template #title>
           {{isEdit ? 'Edit' : 'Add'}} RaiseOrder Modal
        </template>

        <template #content>
          <!-- Start Time -->
          <div v-if="! isEdit" class="col-span-6 sm:col-span-12 mt-3">
              <jet-label for="start_time" value="Open hours" />
              <date-picker :min-date="new Date()" v-model="form.start_time" mode="dateTime" is24hr>
                <template v-slot="{ inputValue, inputEvents }">
                  <jet-input
                    class="mt-1 block w-full border p-2 focus:outline-none focus:border-blue-300"
                    :value="inputValue"
                    v-on="inputEvents"
                  />
                </template>
              </date-picker>
              <jet-input-error :message="form.errors.start_time" class="mt-2" />
            </div>
            <!-- OpenDuration -->
            <div class="mt-3">
                <jet-label for="OpenDuration" value="Open Duration" />
                <jet-input type="number" min=0 id="open_duration" class="mt-1 block w-full" v-model="form.open_duration" autocomplete="open_duration" />
                <jet-input-error :message="form.errors.open_duration" class="mt-2" />
            </div>
            <div v-if="! isEdit" class="mt-3">
                <jet-label class="mb-1" for="choose_restaurant" value="Choose Restaurant" />
                <vue-next-select v-model="form.restaurant_selected" :options="options" search-placeholder="search restaurant" searchable clear-on-close close-on-select label-by="restaurant_name" />
                <jet-input-error :message="form.errors.restaurant_selected" class="mt-2" />
            </div>
            <div class="mt-3">
                <jet-label for="raise_order_theme" value="RaiseOrder Theme" />
                <jet-input id="raise_order_theme" type="text" class="mt-1 block w-full" v-model="form.raise_order_theme" autocomplete="raise_order_theme" />
                <jet-input-error :message="form.errors.raise_order_theme" class="mt-2" />
            </div>
            <!-- Memo -->
            <div class="mt-3 mb-3">
                <jet-label for="memo" value="Memo" />
                <jet-textarea id="memo" class="mt-1 block w-full" v-model="form.memo" autocomplete="memo" />
            </div>
        </template>

        <template #footer>
            <jet-button type="text" class="ml-4"  @click="storeRaiseOrders" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
    import TailwindLoading from '@/Component/Tailwind/TailwindLoading'
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetTextarea from '@/Jetstream/Textarea'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import { reactive, ref, onMounted, watch } from 'vue'
    import { DatePicker } from 'v-calendar';
    import moment from 'moment'
    import VueNextSelect from 'vue-next-select'
    import '@/../css/SearchSelect.css'
    import { Inertia } from '@inertiajs/inertia'

    export default {
        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetLabel,
            JetTextarea,
            DatePicker,
            VueNextSelect,
            TailwindLoading
        },
        emits: ["close-modal"],
        props: {
          openModel: Boolean,
          isEdit: Number
        },
        setup(props) {
          const handlerDisplay = ref(false)
          const modalParam = reactive(props)
          const form = Inertia.form({
            _method: null,
            start_time: null,
            raise_order_theme: null,
            open_duration: null,
            memo: null,
            restaurant_selected: null,
          })

          const options = ref([])
          const getRestaurants = async () => {
            if(props.openModel && ! props.isEdit) {
                await axios.post(route('restaurant-api.index')).then(response => {
                options.value = response.data
              })
            }
          }

          const getRaiseOrders = async () => {
            if(props.openModel) {
              handlerDisplay.value = false
              if(props.isEdit) {
                await axios.get(`/raise-orders-api/${props.isEdit}`).then(response => {
                  form.start_time = response.data.start_time
                  form.raise_order_theme = response.data.raise_order_theme
                  form.open_duration = moment(response.data.end_time).diff(response.data.start_time, "minute");
                  form.memo = response.data.memo
                  form.restaurant_selected = response.data.restaurant_id
                  handlerDisplay.value = true
                })
              } else {
                form.start_time = moment().format('YYYY-MM-DD HH:mm:ss'),
                form.raise_order_theme = null
                form.open_duration = null
                form.memo = null
                form.restaurant_selected  = null
                handlerDisplay.value = true
              }
            }
          }

          onMounted(getRaiseOrders)
          watch(modalParam, getRestaurants)
          watch(modalParam, getRaiseOrders)
          return {
            form,
            options,
            handlerDisplay
          };
        },
        methods: {
          storeRaiseOrders() {
            let path = ""
            if(this.isEdit) {
              this.form._method = 'put'
              path = `/raise-orders-api/${this.isEdit}`
            } else {
              this.form._method = 'post'
              path = route('raise-orders-api.store')
            }
            this.form.post(path , {
                errorBag: 'storeRaiseOrders',
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
            });
          },
          closeModal() {
            this.form.clearErrors()
            this.form.reset()
            this.$emit('close-modal', false)
          }
        },
    }
</script>