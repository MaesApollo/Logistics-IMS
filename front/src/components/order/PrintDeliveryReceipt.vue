<template>
  <div class="container-fluid">
    <div class="card">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-4 col-xs-4">
            <h4>Delivery Receipt</h4>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-3 col-xs-3">
            <img src="../../img/logo.jpg" />
          </div>
          <div class="col-md-3 col-xs-3">
            From
            <br />
            <address>
              <strong>Dctech Microservices, Inc.</strong>
              <br />Dctech Building, Ponciano Reyes Street
              <br />Davao City, 8000, Philippines
            </address>
          </div>
          <div class="col-md-3 col-xs-3">
            To
            <br />
            <address>
              <strong>{{ sales_order.client.name }}</strong>
              <br />
              {{ sales_order.client.address }}
              <br />
              {{ sales_order.client.contact }}
            </address>
          </div>
          <div class="col-md-3 col-xs-3">
            <p>
              Delivery Receipt
              <b>#{{ delivery_receipt.id }}</b>
              <br />Sales Order
              <b>#{{ sales_order.id }}</b>
              <br />
              Date: {{ delivery_receipt.created_at }}
              <br />
            </p>
          </div>
        </div>

        <!-- START ORDER TABLE -->
        <div class="row clearfix">
          <div class="col-md-12 col-xs-12">
            <div class="table-wrap">
              <div class="table-responsive">
                <table class="table table-striped table-condensed">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Serial/Barcode</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody v-if="this.delivery_receipt.orders.length == 0">
                    <tr>
                      <td colspan="9" class="text-center">
                        <small class="col-red">
                          <i>No orders yet.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr
                      v-for="order in delivery_receipt.orders"
                      :key="order.id"
                      @click="edit(order)"
                    >
                      <td>{{ order.id }}</td>
                      <td>{{ order.name }}</td>
                      <td>{{ order.description }}</td>
                      <td>
                        <span v-for="serial in order.ordered_serial" :key="serial">
                          {{ serial }}
                          <br />
                        </span>
                      </td>
                      <td v-if="order.type.name == 'Consumable'">{{ order.delivering_qty }}</td>
                      <td v-else>{{ order.ordered_serial.length }}</td>
                      <td>{{ order.price }}</td>
                      <td
                        v-if="order.type.name == 'Consumable'"
                      >{{ order.price * order.delivering_qty }}</td>
                      <td v-else>{{ order.price * order.ordered_serial.length }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- END ORDER TABLE -->
        <div class="row clearfix">
          <!-- REMARKS -->
          <div class="col-md-6 col-xs-6">
            <div class="row clearfix">
              <div class="col-md-8 col-xs-8">
                <br />
                <label>Note:</label>
                <br />
                <p>{{ sales_order.note }}</p>
              </div>
            </div>
          </div>

          <!-- AMOUNT -->
          <div class="col-md-6 col-xs-6">
            <div class="table-responsive">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ this.delivery_receipt.amount.subtotal }}</td>
                  </tr>
                  <tr>
                    <th>Tax (0%):</th>
                    <td>{{ this.delivery_receipt.amount.tax }}</td>
                  </tr>
                  <tr>
                    <th>Shipping:</th>
                    <td>{{ this.delivery_receipt.amount.shipping }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ this.delivery_receipt.amount.total }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <hr />
        <div class="row clearfix">
          <div class="col-xs-2">
            <b>Prepared By:</b>
            <br />
            <br />
            <br />
            <b>Checked By:</b>
          </div>
          <div class="col-xs-4">
            {{ delivery_receipt.user.name }}
            <br />
            <br />
            <br />_________________________________
          </div>
          <div class="col-xs-2">
            <b>Date Received:</b>
            <br />
            <br />
            <br />
            <b>Received By:</b>
          </div>
          <div class="col-xs-4">
            _________________________________
            <br />
            <br />
            <br />_________________________________
          </div>
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
    return {
      sales_order: {
        orders: [],
        client: {
          name: "",
          address: "",
          contact: ""
        },
        user: [],
        amount: [],
        status: "",
        note: "",
        remarks: "",
        id: ""
      },
      delivery_receipt: {
        id: "",
        orders: [],
        user: [],
        created_at: null,
        delivered_at: null,
        received_at: null,
        status: "",
        amount: {
          subtotal: "0.00",
          tax: "0.00",
          shipping: "0.00",
          total: "0.00"
        }
      },
      temp_orders: []
    };
  },

  beforeMount() {
    this.loadDeliveryReceipt();
  },

  created() {},

  methods: {
    loadDeliveryReceipt() {
      this.$http
        .get("api/delivery_receipt/" + this.$route.params.order)
        .then(response => {
          this.delivery_receipt.id = response.body.id;
          this.delivery_receipt.orders = response.body.orders;
          this.delivery_receipt.user = response.body.user;
          this.delivery_receipt.status = response.body.status;
          this.delivery_receipt.created_at = response.body.created_at;
          this.delivery_receipt.received_at = response.body.received_at;
          this.delivery_receipt.delivered_at = response.body.received_at;
          this.loadSalesOrder(response.body.sales_order_id);
          this.compute();
        });
    },

    loadSalesOrder(sales_order_id) {
      this.$http
        .get("api/sales_order/receipts/" + sales_order_id)
        .then(response => {
          if (response.body.client != null)
            this.sales_order.client = response.body.client;

          this.sales_order.orders = response.body.sales_order_details;
          this.sales_order.user = response.body.user;
          this.sales_order.note = response.body.note;
          this.sales_order.status = response.body.status;
          this.sales_order.created_at = response.body.created_at;
          this.sales_order.remarks = response.body.remarks;
          this.sales_order.id = response.body.id;

          if (this.delivery_receipt.orders.length > 0) {
            this.temp_orders = this.delivery_receipt.orders;
          }
        });
    },

    compute() {
      var sum = 0;
      for (
        var index = 0;
        index < this.delivery_receipt.orders.length;
        index++
      ) {
        if (this.delivery_receipt.orders[index].type.name == "Serialize") {
          sum +=
            parseFloat(this.delivery_receipt.orders[index].price) *
            parseFloat(
              this.delivery_receipt.orders[index].ordered_serial.length
            );
        } else {
          sum +=
            parseFloat(this.delivery_receipt.orders[index].price) *
            parseFloat(this.delivery_receipt.orders[index].delivering_qty);
        }
      }

      this.delivery_receipt.amount.shipping = "0.00";
      this.delivery_receipt.amount.tax = "0.00";
      this.delivery_receipt.amount.subtotal = sum.toFixed(2);
      this.delivery_receipt.amount.total = sum.toFixed(2);
    },

    item_exist(item) {
      let bool = false;

      for (var i = 0; i < this.delivery_receipt.orders.length; i++) {
        if (this.delivery_receipt.orders[i].id == item.id) {
          bool = true;
        }
      }

      return bool;
    },

    index_exist(item) {
      let index = 0;

      for (var i = 0; i < this.delivery_receipt.orders.length; i++) {
        if (this.delivery_receipt.orders[i].item_id == item.item_id) {
          index = i;
        }
      }

      return index;
    },

    serial_exist(index, item) {
      let bool = false;

      for (
        var i = 0;
        i < this.delivery_receipt.orders[index].ordered_serial.length;
        i++
      ) {
        if (
          this.delivery_receipt.orders[index].ordered_serial[i] == item.serial
        ) {
          bool = true;
        }
      }

      return bool;
    }
  }
};
</script>

<style scoped>
.alert {
  border-radius: 4px;
}

.alert-warning {
  background-color: #636363 !important;
}

.alert-approval {
  background-color: #e2ac08;
}

.alert-default {
  background-color: #d3d3d3;
}

.card .header {
  color: #000;
  padding: 20px;
  position: relative;
  border-bottom: 1px solid rgba(204, 204, 204, 0.7);
}

textarea {
  resize: none;
}

input {
  width: 50px;
}

.table-wrap {
  height: 500px;
  border: 1px solid #eee;
  overflow-y: auto;
  overflow-x: hidden;
}

.table-wrap-sales-order {
  height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
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

.cursor-pointer {
  cursor: pointer;
}

.font-8 {
  font-size: 11px;
}
</style>

