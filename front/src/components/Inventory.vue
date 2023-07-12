<template>
  <div>
    <my-items></my-items>
  </div>
</template>


<script>
import Items from './item/Items.vue'

export default {
  components: {
    'my-items': Items
  }
}
</script>