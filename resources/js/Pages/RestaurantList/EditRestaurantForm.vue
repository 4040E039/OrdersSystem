<template>
    <app-layout>
      <div class="py-3 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <inertia-link :href="`/restaurant-api/${id}`">
            <button class="mb-12 shadow text-gray-50 bg-gray-400 px-3 py-1 rounded">
                <chevron-double-left-icon class="h-5 w-5" />
            </button>
          </inertia-link>
          <jet-form-section @submitted="updateRestaurantInformation">
              <template #title>
                  Restaurant Information
              </template>
              <template #description>
                  Restaurant name is required
              </template>

              <template #form>
                  <!-- Name -->
                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="name" value="Name" />
                      <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                      <jet-input-error :message="form.errors.name" class="mt-2" />
                  </div>

                  <!-- Telephone -->
                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="telephone" value="Telephone" />
                      <jet-input id="telephone" type="text" class="mt-1 block w-full" v-model="form.telephone" autocomplete="telephone" />
                      <jet-input-error :message="form.errors.telephone" class="mt-2" />
                  </div>

                  <!-- Address -->
                  <div class="col-span-6">
                      <jet-label for="address" value="Address" />
                      <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address" autocomplete="address" />
                      <jet-input-error :message="form.errors.address" class="mt-2" />
                  </div>

                  <!-- Memo -->
                  <div class="col-span-6">
                      <jet-label for="memo" value="Memo" />
                      <jet-textarea id="memo" class="mt-1 block w-full" v-model="form.memo" autocomplete="memo" />
                      <jet-input-error :message="form.errors.memo" class="mt-2" />
                  </div>
              </template>

              <template #actions>
                  <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                      Saved
                  </jet-action-message>

                  <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                      Save
                  </jet-button>
              </template>
          </jet-form-section>
        </div>
      </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetButton from '@/Jetstream/Button'
    import JetInput from '@/Jetstream/Input'
    import JetTextarea from '@/Jetstream/Textarea'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetInputError from '@/Jetstream/InputError'
    import{ ChevronDoubleLeftIcon } from '@heroicons/vue/solid'

    export default {
        components: { 
          AppLayout, 
          JetButton, 
          JetInput, 
          JetTextarea, 
          JetLabel, 
          JetActionMessage, 
          JetSecondaryButton, 
          JetFormSection, 
          JetInputError, 
          ChevronDoubleLeftIcon 
        },
        props: {
          restaurant: Object,
          id: String
        },
        data() {
          return {
            form: this.$inertia.form({
              _method: 'put',
              name: this.restaurant.name,
              telephone: this.restaurant.telephone,
              address: this.restaurant.address,
              memo: this.restaurant.memo,
            }),
          }
        },
        methods: {
          updateRestaurantInformation() {
            this.form.post(`/restaurant-api/${this.id}`, {
                errorBag: 'updateRestaurantInformation',
                preserveScroll: true
            });
          },
        }
    }
</script>