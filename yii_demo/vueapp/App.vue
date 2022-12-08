<template>

    <a-typography-title>Vue Book Library App</a-typography-title>
    <router-view></router-view>

</template>

<script>
import BooksList from './components/BooksList.vue';

export default {
  components: {
    BooksList
  }
}
</script>
