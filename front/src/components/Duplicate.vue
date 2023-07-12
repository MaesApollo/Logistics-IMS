<template>
  <div>
    <sales-order></sales-order>
  </div>
</template>

<script>
import CreateSalesOrder from "./order/CreateSalesOrder.vue";

export default {
  components: {
    "sales-order": CreateSalesOrder
  }
};
</script>
