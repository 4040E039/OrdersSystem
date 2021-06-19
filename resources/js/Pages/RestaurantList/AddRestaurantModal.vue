<template>

    <!-- Add Restaurant Modal -->
    <jet-dialog-modal :show="openModel" @close="closeModal()">
      <template #title>
          Add Restaurant
      </template>
      <template #content>
          <!-- Name -->
          <div class="mt-3">
              <jet-label for="name" value="Restaurant Name" />
              <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
              <jet-input-error :message="form.errors.name" class="mt-2" />
          </div>
          <!-- Telephone -->
          <div class="mt-3">
              <jet-label for="telephone" value="Telephone" />
              <jet-input id="telephone" type="text" class="mt-1 block w-full" v-model="form.telephone" autocomplete="telephone" />
              <jet-input-error :message="form.errors.telephone" class="mt-2" />
          </div>
          <!-- Address -->
          <div class="mt-3">
              <jet-label for="address" value="Address" />
              <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address" autocomplete="address" />
              <jet-input-error :message="form.errors.address" class="mt-2" />
          </div>
          <!-- Memo -->
          <div class="mt-3">
              <jet-label for="memo" value="Memo" />
              <jet-textarea id="memo" class="mt-1 block w-full" v-model="form.memo" autocomplete="memo" />
              <jet-input-error :message="form.errors.memo" class="mt-2" />
          </div>
      </template>
      <template #footer>
          <jet-button @click="storeRestaurantInformation"  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
        },
        data() {
          return {
            form: this.$inertia.form({
              name: null,
              telephone: null,
              address: null,
              memo: null,
            }),
          }
        },
        methods: {
          storeRestaurantInformation() {
            this.form.post(route('restaurant-api.store'), {
                errorBag: 'storeRestaurantInformation',
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