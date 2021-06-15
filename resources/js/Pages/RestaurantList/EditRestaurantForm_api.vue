<template>
    <app-layout>
        <div class="py-3 sm:py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <two-column-form>
              <template #title>
                Modify Restaurant Information
              </template>
              <template #subtitle>
                Restaurant name is required
              </template>
              <template #content>
                <div class="col-span-6 sm:col-span-3">
                  <jet-label for="name" value="Restaurant Name" />
                  <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                  <jet-label for="telephone" value="Telephone" />
                  <jet-input id="telephone" type="text" class="mt-1 block w-full" v-model="form.telephone" autocomplete="telephone" />
                </div>
                <div class="col-span-6">
                  <jet-label for="address" value="Address" />
                  <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address" autocomplete="address" />
                </div>
                <div class="col-span-6">
                  <jet-label for="memo" value="Memo" />
                  <jet-textarea id="memo" class="mt-1 block w-full" v-model="form.memo" autocomplete="memo" />
                </div>
              </template>
              <template #footer>
                <jet-button class="ml-4" @click="save()">
                    Save
                </jet-button>
              </template>
            </two-column-form>
          </div>
        </div>
        <tailwind-alert :theme="alertTheme" v-show="alertOpen" @closeAlert="closeAlert">
          <template #alertContent>
            {{ alertContent }}
          </template>
        </tailwind-alert>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetNavLink from '@/Jetstream/NavLink'
    import { reactive } from "vue";
    import JetButton from '@/Jetstream/Button'
    import TwoColumnForm from '@/Component/Tailwind/TwoColumnForm'
    import TailwindAlert from '@/Component/Tailwind/TailwindAlert'
    import JetInput from '@/Jetstream/Input'
    import JetTextarea from '@/Jetstream/Textarea'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: { 
          AppLayout, 
          JetNavLink, 
          JetButton, 
          TwoColumnForm, 
          JetInput, 
          JetTextarea, 
          JetLabel, 
          TailwindAlert 
        },
        props: {
          restaurant: Object,
          id: String
        },
        data() {
          return {
            alertOpen: false,
            alertContent: "",
            alertTheme: "",
          }
        },
        setup(props){
          const form = reactive({...this.form})
          return {
            form
          };
        },
        
        methods: {
          listErrorMessage(messages) {
            let errorMessage = ""
            for(let message in messages) {
              for(let error of messages[message]) {
                errorMessage += error
              }
            }
            return errorMessage
          },
          setTimeoutErrorMessage(messages = "") {
            this.alertOpen = true
            if(typeof(messages) === 'object' && Object.keys(messages).length) {
              this.alertContent = `fail to edit cause: ${this.listErrorMessage(messages)}`
              this.alertTheme = "danger"
            } else {
              this.alertContent = "Successfully modified"
              this.alertTheme = "success"
            }
            setTimeout( () => this.alertOpen = false, 5000 )
          },
          save() {
            axios.patch(`/restaurant-api/${this.id}`, {
              name: this.form.name,
              telephone: this.form.telephone,
              address: this.form.address,
              memo: this.form.memo,
            })
            .then(response => {
              this.setTimeoutErrorMessage(response.data.messages)
            })
          },
          closeAlert() {
            this.alertOpen = false
            this.alertContent = ""
          }
        },
    }
</script>