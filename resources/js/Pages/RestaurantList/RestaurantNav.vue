<template>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <div class="space-x-8 ml-10 flex -my-px">
          <jet-nav-link :href="restaurantDetailUrl" :active="route().current('restaurant-list.detail')">
              Restaurant Detail
          </jet-nav-link>
          <jet-nav-link :href="restaurantPhotosUrl" :active="route().current('restaurant-list.photos')">
              Restaurant Photos
          </jet-nav-link>
        </div>
    </h2>
</template>

<script>
    import JetNavLink from '@/Jetstream/NavLink'
    import { ref } from "vue";

    export default {
        components: { JetNavLink },
        props: {
          id: String
        },
        setup(props){
          const restaurantDetailUrl = ref(`/restaurant-list/${props.id}`)
          const restaurantPhotosUrl = ref(`/restaurant-list/photos/${props.id}`)
          return {restaurantPhotosUrl, restaurantDetailUrl }
        },
    }
</script>