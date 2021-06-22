<template>
<div>      
	<div class="min-h-screen bg-yellow-800 bg-opacity-50 overflow-x-hidden flex flex-col justify-center ">
    <div class="w-full text-center mb-5">
      <h1 class="font-bold text-5xl text-white">Welcome !</h1>
    </div>
    <div class="flex items-center justify-center flex-wrap">
        <inertia-link v-if="canRegister" :href="route('login')" class="cursor-pointer p-12">
            <div class="py-8 rounded-md shadow-lg bg-white hover:bg-yellow-800 hover:bg-opacity-75 hover:text-white  transition duration-400">
                <div class="flex items-center justify-center w-48">
                    <div class="p-5 font-bold text-xl text-center space-y-2">
                        <login-icon class="h-12 w-12 mx-auto" />
                        <p>Log in</p>
                    </div>
                </div>
            </div>
        </inertia-link>
        <inertia-link v-if="canRegister" :href="route('register')" class="cursor-pointer p-12">
            <div class="py-8 rounded-md shadow-lg bg-white hover:bg-yellow-800 hover:bg-opacity-75 hover:text-white  transition duration-400">
                <div class="flex items-center justify-center w-48">
                    <div class="p-5 font-bold text-xl space-y-2">
                        <clipboard-list-icon class="h-12 w-12 mx-auto" />
                        <p class="text-center">Register</p>
                    </div>
                </div>
            </div>
        </inertia-link>
    </div>
  </div>
</div>
</template>

<script>
    import { LoginIcon } from '@heroicons/vue/solid'
    import { ClipboardListIcon } from '@heroicons/vue/solid'

    export default {
        components: {
          LoginIcon,
          ClipboardListIcon
        },
        props: {
            canLogin: Boolean,
            canRegister: Boolean,
            laravelVersion: String,
            phpVersion: String,
        }
    }
</script>
