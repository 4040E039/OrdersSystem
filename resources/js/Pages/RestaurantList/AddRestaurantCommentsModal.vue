<template>
    <!-- Add Restaurant Modal -->
    <jet-dialog-modal :show="openModel" @close="closeModal()">
        <template #title>
            Add Restaurant Comments
        </template>

        <template #content>
             <!-- Score -->
            <div class="mt-3">
                <jet-label for="score" value="Score" />
                <button type="button" v-for="index of 5" :key="index" class="focus:outline-none mt-2" @mouseover="handlerScore(index)">
                  <solid-star-icon v-if="form.score >= index" class="w-10 h-10 text-yellow-400"/>
                  <outline-star-icon v-if="form.score < index" class="w-10 h-10 text-yellow-400"/>
                </button>
                <jet-input-error :message="errors.score" class="mt-2" />
            </div>
            <!-- Message -->
            <div class="mt-3">
                <jet-label for="message" value="Message" />
                <jet-textarea id="message" class="mt-1 block w-full" v-model="form.message" autocomplete="message" />
                <jet-input-error :message="errors.message" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <jet-button type="text" class="ml-4" v-if="handlerSaveButtonDisplay" @click="save()">
                Release
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
    import { reactive, onMounted, ref } from "vue";
    import { StarIcon as SolidStarIcon } from '@heroicons/vue/solid'
    import { StarIcon as OutlineStarIcon } from '@heroicons/vue/outline'

    export default {
        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetLabel,
            JetTextarea,
            SolidStarIcon,
            OutlineStarIcon
        },
        emits: ["close-modal"],
        props: {
          openModel: Boolean,
          restaurantId: String,
          isEdit: Number
        },
        setup(props) {
          const handlerSaveButtonDisplay = ref(false)
          const form = reactive({
            score: 0,
            message: null,
            restaurantId: props.restaurantId
          })
          const errors = reactive({
            score: null,
            message: null,
          })
          if(props.isEdit) {
            const getRestaurantComment = async () => {
              await axios.get(`/restaurant-comment-api/${props.isEdit}`).then(response => {
                form.score = response.data.score
                form.message = response.data.message
                handlerSaveButtonDisplay.value = true
              })
            }
            onMounted(getRestaurantComment)
          } else handlerSaveButtonDisplay.value = true
          return {
            form,
            errors,
            handlerSaveButtonDisplay
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
              axios.put(`/restaurant-comment-api/${this.isEdit}`, {...this.form})
              .then(response => {
                if(response.data.messages === "") this.closeModal()
                  else this.handlerErrorMessage(response.data.messages)
              })
            } else {
              axios.post(route('restaurant-comment-api.store'), {...this.form})
              .then(response => {
                if(response.data.messages === "") this.closeModal()
                  else this.handlerErrorMessage(response.data.messages)
              })
            }
          },
          closeModal() {
            this.$emit('close-modal', false)
          },
          handlerScore(i) {
            this.form.score = i
          }
        },
    }
</script>