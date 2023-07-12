<template>
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="card">
          <div class="header">
            <h2>
              <i class="material-icons">edit</i>
              <span>EDIT SALES ORDER</span>
            </h2>
          </div>

          <div class="body">
            <div class="row clearfix">
              <div class="col-md-2">
                <br />
                <img src="../../img/logo.jpg" />
              </div>
              <div class="col-md-3">
                <br />From
                <br />
                <br />
                <address>
                  <strong>Dctech Microservices, Inc.</strong>
                  <br />Dctech Building, Ponciano Reyes Street <br />Davao City,
                  8000, Philippines
                </address>
              </div>
              <div class="col-md-3">
                <br />To
                <br />
                <br />
                <address>
                  <strong>{{ order.client.name }}</strong>
                  <br />
                  {{ order.client.address }}
                  <br />
                  {{ order.client.contact }}
                </address>
              </div>
              <div class="col-md-3">
                <br />Sales Order
                <b>#{{ order.id }}</b>
                <br />
                <p>Date: {{ date }}</p>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-4">
                <button
                  class="btn btn-sm bg-black waves-effect waves-light font-bold"
                  data-toggle="modal"
                  data-target="#clientModal"
                >
                  SELECT CLIENT
                </button>

                <button
                  class="btn btn-sm bg-black waves-effect waves-light font-bold"
                  data-toggle="modal"
                  data-target="#itemModal"
                >
                  SELECT ITEM
                </button>

                <!-- START CLIENT MODAL -->
                <div
                  class="modal fade"
                  id="clientModal"
                  tabindex="-1"
                  role="dialog"
                  data-backdrop="static"
                  data-keyboard="false"
                >
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-body">
                        <div class="row clearfix">
                          <div class="col-md-12">
                            <button
                              type="button"
                              class="close"
                              data-dismiss="modal"
                              aria-label="Close"
                            >
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        </div>
                        <div class="row clearfix">
                          <div class="col-md-5">
                            <div class="form-group">
                              <div class="form-line">
                                <span>Search</span>
                                <input
                                  id="search"
                                  type="text"
                                  class="form-control"
                                  v-model="search.client"
                                  autocomplete="off"
                                />
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3">
                            <br />
                            <button
                              class="btn btn-sm bg-black waves-effect waves-light font-bold"
                              @click="searchClient()"
                            >
                              Search
                            </button>

                            <button
                              class="btn btn-sm btn-success waves-effect font-bold"
                              @click="clearSearch()"
                            >
                              Reset
                            </button>
                          </div>
                        </div>

                        <table
                          class="table table-bordered table-condensed table-hover"
                        >
                          <thead>
                            <tr>
                              <th>Client Name</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="client in clients"
                              :key="client.id"
                              style="cursor:pointer;"
                              @click="selectClient(client.id)"
                            >
                              <td>{{ client.name }}</td>
                            </tr>
                            <tr v-show="clients.length == 0">
                              <td colspan="5" class="text-center">
                                No results found.
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="footer"></div>
                    </div>
                  </div>
                </div>
                <!-- END CLIENT MODAL-->
                <!-- START ITEM MODAL -->
                <div
                  class="modal fade"
                  id="itemModal"
                  tabindex="-1"
                  role="dialog"
                  data-backdrop="static"
                  data-keyboard="false"
                >
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-body">
                        <form @submit.prevent="searchItem">
                          <div class="row clearfix">
                            <div class="col-md-12">
                              <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                              >
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                          </div>
                          <div class="row clearfix">
                            <div class="col-md-5">
                              <div class="form-group">
                                <div class="form-line">
                                  <span>Search</span>
                                  <input
                                    id="search"
                                    type="text"
                                    class="form-control"
                                    v-model="search.item"
                                    autocomplete="off"
                                  />
                                </div>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <br />
                              <button
                                class="btn btn-sm bg-black waves-effect waves-light font-bold"
                              >
                                Search
                              </button>

                              <button
                                class="btn btn-sm btn-success waves-effect font-bold"
                                @click="clearSearch"
                              >
                                Reset
                              </button>
                            </div>
                          </div>
                        </form>

                        <table
                          class="table table-bordered table-condensed table-hover"
                        >
                          <thead>
                            <tr>
                              <th v-for="column in columns" :key="column">
                                {{ column }}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="item in items"
                              :key="item.id"
                              class="disabled"
                              style="cursor: pointer;"
                              @click="
                                selectItem(
                                  item.id,
                                  item.quantity,
                                  item.description
                                )
                              "
                            >
                              <td>{{ item.id }}</td>
                              <td>{{ item.name }}</td>
                              <td>{{ item.description }}</td>
                              <td>{{ item.category }}</td>
                              <td>{{ item.quantity }}</td>
                              <td class="bg-red" v-show="item.percent < 1">
                                No Stock
                              </td>
                              <td
                                class="bg-orange"
                                v-show="item.percent > 0 && item.percent < 31"
                              >
                                Low Stock
                              </td>
                              <td class="bg-green" v-show="item.percent > 30">
                                High Stock
                              </td>
                            </tr>
                            <tr v-show="items.length == 0">
                              <td colspan="6" class="text-center">
                                No results found.
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="footer"></div>
                    </div>
                  </div>
                </div>
                <!-- END ITEM MODAL -->
              </div>
            </div>

            <!-- START ORDER TABLE -->
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Itemcode</th>
                        <th>Name</th>
                        <th width="30%">Description</th>
                        <th width="15%">Serial #</th>
                        <th width="5%">Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="item in order.sales_order_details"
                        :key="item.id"
                      >
                        <td>{{ item.id }}</td>
                        <td>{{ item.name }}</td>
                        <td>
                          {{ item.description }}
                          <a
                            href="javascript:void(0);"
                            class="pull-right"
                            title="add qty/serial"
                            data-toggle="modal"
                            data-target="#serialModal"
                            @click="getSerial(item.id)"
                            v-if="item.type == 'Serialize'"
                          >
                            <i class="material-icons text-primary"
                              >arrow_drop_down</i
                            >
                          </a>

                          <a
                            href="javascript:void(0);"
                            class="pull-right"
                            title="add qty/serial"
                            data-toggle="modal"
                            data-target="#qtyModal"
                            @click="getQuantity(item.id)"
                            v-else
                          >
                            <i class="material-icons text-primary"
                              >arrow_drop_down</i
                            >
                          </a>
                        </td>
                        <td>
                          <span
                            class="clearfix"
                            v-for="serial in item.serials"
                            :key="serial.id"
                          >
                            {{ serial }}
                            <i
                              class="material-icons text-danger pull-right"
                              style="cursor: pointer;"
                              @click="removeSerial(item.id, serial)"
                              title="remove"
                              >remove</i
                            >
                            <br />
                          </span>
                        </td>
                        <td>{{ item.qty }}</td>
                        <td>{{ item.price }}</td>
                        <td class="amount">
                          {{ (item.price * item.qty).toFixed(2) }}
                        </td>
                        <td>
                          <a
                            href="javascript:void(0);"
                            title="remove"
                            @click="removeItem(item.id)"
                          >
                            <i
                              class="material-icons text-danger"
                              style="font-size: 16px !important;"
                              >delete</i
                            >
                          </a>
                        </td>
                      </tr>
                      <tr v-show="order.sales_order_details.length == 0">
                        <td colspan="8" class="text-center">
                          No items ordered.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- END ORDER TABLE -->
            <!-- START SERIAL MODAL -->
            <div
              class="modal fade"
              id="serialModal"
              tabindex="-1"
              role="dialog"
              data-backdrop="static"
              data-keyboard="false"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <div class="row clearfix">
                      <div class="col-md-11">
                        <div class="form-group">
                          <div class="form-line">
                            <span>Search</span>
                            <input
                              type="text"
                              class="form-control"
                              autocomplete="off"
                              v-model="search.serial"
                              @keyup="searchSerial"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-1">
                        <button
                          type="button"
                          class="close"
                          data-dismiss="modal"
                          aria-label="Close"
                        >
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table
                        class="table table-bordered table-hover"
                        id="serialTable"
                      >
                        <tbody>
                          <tr
                            v-for="serial in serials.warehouses"
                            :key="serial.id"
                            style="cursor: pointer;"
                            @click="
                              selectSerial(
                                serial.pivot.serial,
                                serial.pivot.item_id,
                                serial.pivot.status
                              )
                            "
                          >
                            <td>{{ serial.pivot.serial }}</td>
                            <td>{{ serial.pivot.status }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="footer"></div>
                </div>
              </div>
            </div>
            <!-- END SERIAL MODAL -->
            <!-- START QUANTITY MODAL -->
            <div
              class="modal fade"
              tabindex="-1"
              role="dialog"
              id="qtyModal"
              data-backdrop="static"
              data-keyboard="false"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <div class="row clearfix">
                      <div class="col-md-12">
                        <button
                          type="button"
                          class="close"
                          data-dismiss="modal"
                          aria-label="Close"
                        >
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                    </div>
                    <div class="row clearfix">
                      <div class="col-md-12">
                        <form @submit.prevent="putQuantity(item.id)">
                          <div class="table-responsive">
                            <table class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>WAREHOUSE</th>
                                  <th width="15%">QTY</th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr
                                  v-for="warehouse in item.warehouses"
                                  :key="warehouse.id"
                                >
                                  <td
                                    width="80%"
                                    v-show="warehouse.pivot.qty > 0"
                                  >
                                    {{ warehouse.name }}
                                  </td>
                                  <td v-show="warehouse.pivot.qty > 0">
                                    {{ warehouse.pivot.qty }}
                                  </td>
                                  <td v-show="warehouse.pivot.qty > 0">
                                    <div
                                      v-for="order in order.sales_order_details"
                                      :key="order.id"
                                    >
                                      <div v-if="order.id == item.id">
                                        <div
                                          v-for="order_warehouse in order.warehouses"
                                          :key="order_warehouse.id"
                                        >
                                          <div
                                            v-if="
                                              order_warehouse.warehouse_id ==
                                                warehouse.id
                                            "
                                          >
                                            <input
                                              type="number"
                                              v-model="order_warehouse.qty"
                                            />
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <button
                            type="submit"
                            class="btn btn-lg bg-black font-bold waves-effect waves-light pull-right"
                          >
                            ADD
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- END QUANTITY MODAL -->
            <div class="row clearfix">
              <!-- REMARKS -->
              <div class="col-md-6">
                <div class="row clearfix">
                  <div class="col-md-8">
                    <br />
                    <label>Remarks</label>
                    <div class="input-group">
                      <div class="form-line">
                        <textarea
                          type="text"
                          class="form-control"
                          rows="5"
                          v-model="order.remarks"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- AMOUNT -->
              <div class="col-md-6">
                <h3>Amount</h3>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <th>Subtotal:</th>
                        <td>{{ amount.subtotal }}</td>
                      </tr>
                      <tr>
                        <th>Tax (0%):</th>
                        <td>{{ amount.tax }}</td>
                      </tr>
                      <tr>
                        <th>Shipping:</th>
                        <td>{{ amount.shipping }}</td>
                      </tr>
                      <tr>
                        <th>Total:</th>
                        <td>{{ amount.total }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- BUTTON FOOTER -->
            <div class="row clearfix">
              <div class="col-md-12">
                <button
                  class="btn btn-lg btn-danger font-bold waves-effect waves-light pull-left"
                  v-show="order.status == 'pending'"
                  @click="cancelOrder(order.id)"
                >
                  CANCEL ORDER
                </button>

                <button
                  class="btn btn-lg btn-success font-bold waves-effect waves-light pull-right"
                  v-show="
                    order.status == 'pending' &&
                      order.sales_order_details.length > 0
                  "
                  @click="updateOrder(order.id)"
                >
                  SAVE & VERIFY
                </button>
              </div>
            </div>
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
      item: [],
      order: [],
      clients: [],
      items: [],
      serials: [],
      warehouses: [],
      columns: ["Itemcode", "Name", "Description", "Category", "Qty"],
      amount: {
        subtotal: "0.00",
        tax: "0.00",
        shipping: "0.00",
        total: "0.00"
      },
      search: {
        item: null,
        client: null,
        serial: null,
        category: "All",
        type: "All"
      },
      date: null
    };
  },

  created() {
    this.date = moment().format("MM/DD/YYYY");
    this.getClients();
    this.warehouses = this.$global.getWarehouses();
    this.getOrder();
    this.loadItems();
  },

  watch: {
    // $route(to, from) {
    //   this.date = moment().format("MM/DD/YYYY");
    //   this.items = this.$global.getItems();
    //   this.clients = this.$global.getClients();
    //   this.warehouses = this.$global.getWarehouses();
    //   this.getOrder();
    // }
  },

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },
    liveUpdate() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },

    getOrder(id) {
      this.$http
        .get("api/sales_order/" + this.$route.params.order)
        .then(response => {
          this.order = response.body;
          this.subtotal();
        });
    },

    getSerial(id) {
      this.serials = "";
      this.$http.get("api/items/" + id).then(response => {
        this.serials = response.body;
      });
    },

    getQuantity(id) {
      this.$http.get("api/items/" + id).then(response => {
        this.item = response.body;
      });
    },

    selectSerial(serial, id, status) {
      if (status == "Stocked In") {
        for (var i = 0; i < this.order.sales_order_details.length; i++) {
          if (this.order.sales_order_details[i].id == id) {
            if (this.order.sales_order_details[i].serials.length > 0) {
              var exists = false;

              for (
                var x = 0;
                x < this.order.sales_order_details[i].serials.length;
                x++
              ) {
                if (this.order.sales_order_details[i].serials[x] == serial) {
                  exists = true;
                }
              }

              if (!exists) {
                this.order.sales_order_details[i].qty += 1;
                this.order.sales_order_details[i].serials.push(serial);
                // $("#serialModal").modal("hide");
              } else {
                swal(serial + " already selected!", { dangerMode: true });
              }
            } else {
              this.order.sales_order_details[i].qty += 1;
              this.order.sales_order_details[i].serials.push(serial);
              // $("#serialModal").modal("hide");
            }
          }
        }
      } else {
        swal("Can't add this serial!", { dangerMode: true });
      }

      this.subtotal();
    },

    selectClient(id) {
      this.$http.get("api/client/" + id).then(response => {
        this.$global.setToClient(response.body);
        this.order.client = response.body;
        // $("#clientModal").modal("hide");
      });
    },

    selectItem(id, qty, description) {
      var exists = false;

      for (var i = 0; i < this.order.sales_order_details.length; i++) {
        if (this.order.sales_order_details[i].id == id) {
          exists = true;
        }
      }

      if (!exists && qty > 0) {
        this.$http.get("api/items/" + id).then(response => {
          this.order.sales_order_details.push(response.body);

          for (var i = 0; i < this.order.sales_order_details.length; i++) {
            if (
              this.order.sales_order_details[i].type == "Consumable" &&
              this.order.sales_order_details[i].id == id
            ) {
              for (
                var x = 0;
                x < this.order.sales_order_details[i].warehouses.length;
                x++
              ) {
                this.order.sales_order_details[i].warehouses[x].pivot.qty = 0;
              }
            }
          }

          // $("#itemModal").modal("hide");
        });
      } else {
        if (qty <= 0) {
          swal(description + " has no stock!", { dangerMode: true });
        }
        if (exists) {
          swal(description + " already selected!", { dangerMode: true });
        }
      }
    },

    putQuantity(id) {
      var sum = 0;
      var isLesser = false;

      for (
        var index = 0;
        index < this.order.sales_order_details.length;
        index++
      ) {
        if (this.order.sales_order_details[index].id == id) {
          for (
            var x = 0;
            x < this.order.sales_order_details[index].warehouses.length;
            x++
          ) {
            for (var i = 0; i < this.item.warehouses.length; i++) {
              if (
                this.item.warehouses[i].id ==
                this.order.sales_order_details[index].warehouses[x].warehouse_id
              ) {
                if (
                  this.order.sales_order_details[index].warehouses[x].qty <=
                  this.item.warehouses[i].pivot.qty
                ) {
                  isLesser = true;
                } else {
                  isLesser = false;
                }
              }
            }

            if (isLesser) {
              sum += parseInt(
                this.order.sales_order_details[index].warehouses[x].qty
              );
              this.order.sales_order_details[index].qty = sum;
              // $("#qtyModal").modal("hide");
            } else {
              swal("Insufficient stock!", { dangerMode: true });
              this.order.sales_order_details[index].warehouses[x].qty = 0;
            }
          }
        }
      }

      this.subtotal();
    },

    searchItem() {
      this.$http.post("api/items/search", this.search).then(response => {
        this.items = response.body;
      });
    },

    searchClient() {
      this.$http.post("api/client/search", this.search).then(response => {
        this.clients = response.body;
      });
    },

    searchSerial() {
      var filter, table, tr, td, i;
      filter = this.search.serial.toUpperCase();
      table = document.getElementById("serialTable");
      tr = table.getElementsByTagName("tr");

      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
          if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    },

    removeItem(id) {
      for (
        var index = 0;
        index < this.order.sales_order_details.length;
        index++
      ) {
        if (this.order.sales_order_details[index].id == id) {
          this.order.sales_order_details.splice(index, 1);
        }
      }
    },

    removeSerial(id, serial) {
      for (
        var index = 0;
        index < this.order.sales_order_details.length;
        index++
      ) {
        if (this.order.sales_order_details[index].id == id) {
          for (
            var serial_index = 0;
            serial_index < this.order.sales_order_details[index].serials.length;
            serial_index++
          ) {
            if (
              this.order.sales_order_details[index].serials[serial_index] ==
              serial
            ) {
              this.order.sales_order_details[index].serials.splice(
                serial_index,
                1
              );
              this.order.sales_order_details[index].qty--;
            }
          }
        }
      }
    },

    clearSearch() {
      this.search.item = "";
      this.search.client = "";
      this.searchItem();
      this.searchClient();
    },

    clearSalesOrder() {
      this.transact.order = [];
      this.transact.clients = [];
      this.transact.remarks = "";
      this.orders = [];
      this.client = [];
      this.$global.setToClient([]);
      this.$global.setOrder([]);
      this.showNotification(
        "bg-blue",
        "Sales order was cleared!",
        "top",
        "right",
        "animated bounceInRight",
        "animated fadeOutRight"
      );
    },

    cancelOrder(id) {
      swal({
        title: "Cancel Sales Order No. " + id + "?",
        text: "Warning, this would cancel the order permanently!",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http.put("api/sales_order/cancel/" + id).then(response => {
            this.order.status = "declined";

            swal("Sales Order No. " + id + " was sucessfully canceled!", {
              icon: "success"
            });

            // $("#salesOrderModal").modal("hide");
          });
        }
      });
    },

    subtotal() {
      var sum = 0;
      for (
        var index = 0;
        index < this.order.sales_order_details.length;
        index++
      ) {
        sum +=
          parseFloat(this.order.sales_order_details[index].price) *
          parseFloat(this.order.sales_order_details[index].qty);
      }

      this.amount.shipping = "0.00";
      this.amount.total = "0.00";
      this.amount.subtotal = sum.toFixed(2);
      this.amount.total = sum.toFixed(2);
    },

    updateOrder(id) {
      this.$http
        .put("api/sales_order/" + id, this.order)
        .then(response => {
          this.order.status = "verified";

          // $("#salesOrderModal").modal("hide");

          swal("Sales Order No. " + id + " was sucessfully save & verified!", {
            icon: "success"
          });
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          }).then(value => {
            if (value) {
            }
          });
        });
    }
  }
};
</script>

<style scoped>
i {
  font-size: 22px;
  vertical-align: middle;
  margin-bottom: 5px;
}

textarea {
  resize: none;
}
input {
  width: 50px;
}
</style>
