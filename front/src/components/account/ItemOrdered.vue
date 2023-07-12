<template>
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="card">
          <div class="header">
            <h2>
              <span><i class="material-icons">history</i> ORDER HISTORY</span>
            </h2>
          </div>
          <div class="body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th>Delivery Receipt No.</th>
                    <th>Itemcode</th>
                    <!-- <th>Serial</th> -->
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Date Created</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items">
                    <td>
                      <a
                        :href="'/delivery_receipt/' + item.delivery_receipt.id"
                        target="_blank"
                      >
                        {{ item.delivery_receipt.id }}
                      </a>
                    </td>
                    <td>{{ item.id }}</td>
                    <!-- <td>{{ item.note }}</td> -->
                    <td>{{ item.name }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.price }}</td>
                    <td>{{ item.delivery_receipt.created_at }}</td>
                  </tr>
                  <tr v-show="items.length == 0">
                    <td colspan="7" class="text-center">No orders found.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: []
    };
  },

  created() {
    this.getOrders();
  },

  methods: {
    getOrders() {
      this.$http
        .post("api/sales_order/client/" + this.$route.params.client)
        .then(response => {
          this.items = response.body;
          console.log(response.body);
        });
    }
  }
};
</script>
<style scoped>
i {
  font-size: 18px;
  vertical-align: middle;
  margin-bottom: 5px;
}
</style>
