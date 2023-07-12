<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix"></div>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Supplier Payment</h2>
      </div>
      <div class="body">
        <div class="table-wrap">
          <table class="table table-striped table-condensed table-hover">
            <thead>
              <tr>
                <th>Status</th>
                <th>Purchase Order</th>
                <th>Supplier</th>
                <th>Expected Delivery</th>
                <th>Created by</th>
              </tr>
            </thead>
            <tbody>
              <router-link
                tag="tr"
                v-for="purchase_order in purchase_orders"
                :key="purchase_order.id"
                v-show="purchase_order.status == 'approved'"
                :to="'/supplier_bills/' + purchase_order.id"
                style="cursor: pointer"
              >
                <td>
                  <b-button variant="danger">Unpaid</b-button>
                </td>
                <td>
                  <router-link :to="'/purchase_order/' + purchase_order.id">
                    <a href="javascript:void(0);">{{ purchase_order.id }}</a>
                  </router-link>
                </td>
                <td v-if="purchase_order.supplier">
                  {{ purchase_order.supplier.name }}
                </td>
                <td v-else>
                  <small class="col-red">
                    <i>no supplier selected</i>
                  </small>
                </td>
                <td v-if="purchase_order.delivery_date">
                  <small>{{
                    purchase_order.delivery_date.substring(0, 10)
                  }}</small>
                </td>
                <td v-else>
                  <small class="col-red">
                    <i>no date selected</i>
                  </small>
                </td>

                <td v-if="purchase_order.user">
                  {{ purchase_order.user.name }}
                </td>
              </router-link>
              <tr v-show="purchase_orders.length == 0">
                <td colspan="6" class="text-center">No results found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
var moment = require("moment");
moment().format();

export default {
  data() {
    return { authenticatedUser: [], purchase_orders: [] };
  },

  beforeMount() {},

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.purchase_orders = this.$global.getPurchaseOrders();
  },

  mounted() {},

  methods: {
    createPurchaseOrder() {
      swal("Create a new purchase order ?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$http
              .post("api/purchase_order", this.authenticatedUser)
              .then(response => {
                this.$http.get("api/purchase_order").then(response => {
                  this.$global.setPurchaseOrders(response.body);
                });

                this.$router.push({
                  path: "purchase_order/" + response.body.id
                });
              });
            break;

          default:
            break;
        }
      });
    }
  }
};
</script>

<style scoped>
.table-wrap {
  height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
  border: 1px solid #eee;
}

/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
