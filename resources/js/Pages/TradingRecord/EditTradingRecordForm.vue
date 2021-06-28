<template>
    <app-layout>
      <div class="py-3 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <inertia-link :href="`/trading-record-api/${id}`">
            <button class="mb-12 shadow text-gray-50 bg-gray-400 px-3 py-1 rounded">
                <chevron-double-left-icon class="h-5 w-5" />
            </button>
          </inertia-link>
          <jet-form-section @submitted="updateTradingRecordInformation">
              <template #title>
                  Trading Record
              </template>
              <template #description>
                  Trading Item and Trading Cost is required
              </template>

              <template #form>
                  <!-- Trading Item -->
                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="trading_item" value="Trading Item" />
                      <jet-input id="trading_item" type="text" class="mt-1 block w-full" v-model="form.trading_item" autocomplete="Trading Item" />
                      <jet-input-error :message="form.errors.trading_item" class="mt-2" />
                  </div>

                  <!-- Trading Cost -->
                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="trading_cost" value="Trading Cost" />
                      <jet-input id="trading_cost" type="text" class="mt-1 block w-full" v-model="form.trading_cost" autocomplete="Trading Cost" />
                      <jet-input-error :message="form.errors.trading_cost" class="mt-2" />
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
          trading_record: Object,
          id: String
        },
        data() {
          return {
            form: this.$inertia.form({
              _method: 'put',
              trading_item: this.trading_record.trading_item,
              trading_cost: this.trading_record.trading_cost,
              memo: this.trading_record.memo,
            }),
          }
        },
        methods: {
          updateTradingRecordInformation() {
            this.form.post(`/trading-record-api/${this.id}`, {
                errorBag: 'updateTradingRecordInformation',
                preserveScroll: true
            });
          },
        }
    }
</script>