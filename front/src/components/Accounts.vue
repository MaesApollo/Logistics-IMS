<template>
  <div>
    <my-accounts></my-accounts>
  </div>
</template>


<script>
import Accounts from './account/ManageAccounts.vue'

export default {
  components: {
    'my-accounts': Accounts
  }
}
</script>