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
            </div>
            <!-- Telephone -->
            <div class="mt-3">
                <jet-label for="telephone" value="Telephone" />
                <jet-input id="telephone" type="text" class="mt-1 block w-full" v-model="form.telephone" autocomplete="telephone" />
            </div>
            <!-- Address -->
            <div class="mt-3">
                <jet-label for="address" value="Address" />
                <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address" autocomplete="address" />
            </div>
            <!-- Memo -->
            <div class="mt-3">
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
    import { reactive } from "vue";

    export default {
        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetLabel,
            JetTextarea
        },
        emits: ["close-modal"],
        props: {
          openModel: Boolean,
        },
        setup() {
          const form = reactive({
            name: null,
            telephone: null,
            address: null,
            memo: null
          })
          return {
            form
          };
        },
        methods: {
          save() {
            axios.post(route('restaurant-api.store'), {...this.form})
            .then(response => {
              if(response.data.messages === "") this.closeModal()
            })
          },
          closeModal() {
            this.$emit('close-modal', false)
          }
        },
    }
</script>