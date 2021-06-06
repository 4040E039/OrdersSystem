<template>
  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 bg-gray-200 ">
      <h3 class="text-lg leading-6 font-semibold text-gray-900">
        <slot name="title"></slot>
      </h3>
      <p class="mt-1 max-w-2xl text-sm text-gray-500">
        <slot name="subtitle"></slot>
      </p>
    </div>
    <div class="border-t border-gray-200">
      <dl>
        <slot name="content"></slot>
      </dl>
    </div>
  </div>
</template>

<script>
import { PaperClipIcon } from '@heroicons/vue/solid'

export default {
  components: {
    PaperClipIcon,
  },
}
</script>