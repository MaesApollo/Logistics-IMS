<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix" id="purchase_button">
        <div class="col-lg-9 col-md-9">
          <button
            type="button"
            class="btn btn-default waves-effect"
            style="margin-right: 10px"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>
          <!-- SELECT SUPPLIER -->
          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            data-target="#supplierModal"
          >
            <span>Select Supplier</span>
          </button>

          <!-- APPROVAL BUTTON START -->
          <button
            type="button"
            class="btn btn-lg btn-success waves-effect"
            @click="submitApproval"
            :disabled="data.orders.length < 1"
          >
            <span>Submit For Approval</span>
          </button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-4 col-sm-4">
            <h4>PURCHASE ORDER</h4>
            <!-- <p>Date Created:{{ data.created_at }}</p> -->
          </div>
          <div class="col-md-8 col-sm-8" id="purchase_notification">
            <div class="alert alert-warning"><b>Status:</b> Draft</div>
          </div>
        </div>

        <div class="row clearfix">
          <div class="col-md-3 col-sm-12" style="margin-top:-10px">
            <img src="../../img/new_email.gif" style="width:100%" />
          </div>

          <div class="col-md-3">
            Dctech Building, Shanghai Street,<br />Matina Aplaya, Davao City<br />Davao
            Del Sur 8000, Philippines<br />Tel #: (082) 221-2380<br />VAT
            Registered TIN: 003-375-571-000
          </div>

          <div class="col-md-3">
            <div v-if="data.supplier != null">
              <b>{{ data.supplier.name }} </b><br />
              Tel #:{{ data.supplier.contact }} <br />
              {{ data.supplier.email }} <br />
              {{ data.supplier.address }}<br />
              Registered TIN:{{ data.supplier.tin }}
            </div>
            <div v-else>
              <small style="color:red">
                <i>please select supplier</i>
              </small>
            </div>
          </div>
        </div>

        <div class="row clearfix" style="margin-top:10px">
          <div style="height:40%;width:30%">
            <p>
              <b>Expected Delivery Date :</b>
              <b-form-datepicker
                id="datepicker-valid"
                :state="true"
                v-model="data.delivery_date"
                name="expected_date"
              ></b-form-datepicker>
            </p>
          </div>
        </div>

        <div class="row clearfix">
          <div class="col-md-12">
            <!-- NAVIGATION -->
            <ul class="nav nav-tabs tab-nav-right" role="tablist" id="panels">
              <li role="presentation" class="active">
                <a href="#items" data-toggle="tab" aria-expanded="true"
                  >Line Items</a
                >
              </li>
              <li role="presentation" class>
                <a href="#costs" data-toggle="tab" aria-expanded="false"
                  >Additional Costs</a
                >
              </li>
              <li role="presentation" class>
                <a href="#general" data-toggle="tab" aria-expanded="false"
                  >General</a
                >
              </li>
            </ul>
            <div class="tab-content">
              <!-- ORDERED ITEM -->
              <div role="tabpanel" class="tab-pane fade active in" id="items">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap" style="height:auto">
                      <table class="table table-stripped">
                        <thead>
                          <tr>
                            <th v-show="data.status == 'on order'"></th>
                            <th>Requested Item</th>
                            <th>Code#</th>
                            <th>Qty</th>
                            <th>Unit of Measure</th>
                            <th>Received</th>
                            <th>Unit Price</th>
                            <th>Tax Rate (%)</th>
                            <th></th>
                            <th>Line Subtotal</th>
                            <th v-show="data.status == 'draft'"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="order in data.orders"
                            :key="order.id"
                            v-show="data.orders.length > 0"
                          >
                            <td>{{ order.name }} - {{ order.description }}</td>
                            <td>{{ order.id }}</td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.qty"
                                @keyup="subtotal"
                                style="max-width: 40px"
                                v-validate="'required|min_value:1|numeric'"
                                v-bind:name="order.id + 'qty'"
                              />
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'qty')"
                                  >Qty is required</small
                                >
                              </p>
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.unit"
                                style="max-width: 120px"
                                v-bind:name="order.id + 'unit'"
                              />
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'unit')"
                                  >Unit of measure is required</small
                                >
                              </p>
                            </td>
                            <td>{{ order.total_qty_received }}</td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.price"
                                style="max-width: 100px"
                                @keyup="subtotal"
                                v-validate="'required|min_value:0|decimal:2'"
                                v-bind:name="order.id + 'price'"
                              />
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'price')"
                                  >Price is required</small
                                >
                              </p>
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.tax_rate"
                                style="max-width: 40px"
                                @keyup="subtotal"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:0|numeric'"
                                v-bind:name="order.id + 'tax'"
                                :disabled="
                                  data.status == 'approval' ||
                                    data.status == 'approved' ||
                                    data.status == 'declined' ||
                                    data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              <span v-else>{{ order.pivot.tax_rate }}</span>
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'tax')"
                                  >Tax is required</small
                                >
                              </p>
                            </td>
                            <td>
                              <span v-show="order.pivot.tax_rate > 0">{{
                                (
                                  order.pivot.price *
                                  (order.pivot.tax_rate / 100) *
                                  order.pivot.qty
                                ).toFixed(2)
                              }}</span>
                            </td>
                            <td>
                              <span
                                v-if="
                                  order.pivot.price > 0 && order.pivot.qty > 0
                                "
                                >{{
                                  (order.pivot.price * order.pivot.qty).toFixed(
                                    2
                                  )
                                }}</span
                              >
                              <span v-else>0.00</span>
                            </td>
                            <td v-show="data.status == 'draft'">
                              <a
                                href="javascript:void(0);"
                                title="remove"
                                @click="removeItem(order.id)"
                              >
                                <i
                                  class="material-icons text-danger"
                                  style="font-size: 16px !important"
                                  >delete</i
                                >
                              </a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="row clearfix">
                  <div class="col-md-12" id="btn-Add">
                    <button
                      type="button"
                      class="btn btn-sm btn-default waves-effect"
                      data-toggle="modal"
                      data-target="#itemModal"
                      v-show="data.supplier != null"
                    >
                      <i class="material-icons">note_add</i>
                      Add Items
                    </button>
                    <span class="pull-right" v-show="data.orders.length < 1"
                      >{{ data.orders.length }} item.</span
                    >
                    <span class="pull-right" v-show="data.orders.length == 1"
                      >{{ data.orders.length }} item.</span
                    >
                    <span class="pull-right" v-show="data.orders.length > 1"
                      >{{ data.orders.length }} items.</span
                    >
                    <hr />
                  </div>
                </div>
              </div>

              <!-- COSTS -->
              <div role="tabpanel" class="tab-pane fade" id="costs">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <table class="table table-stripped">
                        <thead>
                          <tr>
                            <th>Purchase Order Additional Cost Type</th>
                            <th>Notes</th>
                            <th></th>
                            <th>Amount</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Tax</td>
                            <td>
                              <input type="text" />
                            </td>
                            <td>
                              <input type="text" />
                              %
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.tax_overide"
                                @keyup="subtotal"
                              />
                            </td>
                            <td>
                              <div class="demo-checkbox">
                                <input
                                  type="checkbox"
                                  id="basic_checkbox_1"
                                  v-model="data.amount.overide"
                                  @keyup="subtotal"
                                />
                                <label for="basic_checkbox_1"
                                  >Overide tax amount</label
                                >
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>Freight Cost</td>
                            <td>
                              <input type="text" />
                            </td>
                            <td>
                              <input type="text" />
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.shipping_cost"
                                @keyup="subtotal"
                              />
                            </td>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <br />
                <hr />
              </div>

              <!-- GENERAL -->
              <div role="tabpanel" class="tab-pane fade" id="general">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <p>
                        Created by:
                        <br />
                        <br />
                        Date created:
                        <br />
                        Last update:
                      </p>
                    </div>
                  </div>
                </div>
                <hr />
              </div>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-6 col-xs-6">
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ formatPrice(data.amount.subtotal.toFixed(2)) }}</td>
                  </tr>
                  <tr>
                    <th>Freight Cost:</th>
                    <td>{{ formatPrice(data.amount.shipping.toFixed(2)) }}</td>
                  </tr>
                  <tr>
                    <th>Tax:</th>
                    <td>{{ formatPrice(data.amount.tax.toFixed(2)) }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ formatPrice(data.amount.total.toFixed(2)) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

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
          <div class="modal-header">
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="snackbar">Item Added</div>
            <form @submit.prevent="searchItem">
              <div class="row clearfix">
                <div class="col-md-4">
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
                <div class="col-md-4">
                  <br />
                  <button class="btn btn-sm bg-black waves-effect waves-light">
                    Search
                  </button>
                  <button class="btn btn-sm btn-success waves-effect">
                    Reset
                  </button>
                </div>
              </div>
            </form>
            <div class="row clearfix">
              <div class="col-md-12">
                <table
                  id="table_id"
                  class="table table-bordered table-condensed table-hover"
                >
                  <thead>
                    <tr>
                      <th>Add</th>
                      <th>Description</th>
                      <th>Code</th>
                      <th>Category</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="item in items"
                      :key="item.id"
                      style="cursor: pointer"
                    >
                      <td>
                        <button
                          class="btn btn-lg btn-success waves-effect"
                          @click="selectItem(item)"
                        >
                          Add
                        </button>
                      </td>
                      <td>{{ item.name }} - {{ item.description }}</td>
                      <td>{{ item.id }}</td>
                      <td>{{ item.category.name }}</td>
                    </tr>
                    <tr v-show="items.length < 1">
                      <td colspan="3" class="text-center">No results found.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END ITEM MODAL -->
    <!-- START SUPPLIER MODAL -->
    <div
      class="modal fade"
      id="supplierModal"
      tabindex="-1"
      role="dialog"
      data-keyboard="false"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header"></div>
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-5">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      id="search"
                      type="text"
                      class="form-control"
                      v-model="search.supplier"
                      autocomplete="off"
                      @keyup="searchSupplier"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-7">
                <br />
                <button
                  class="btn bg-black waves-effect waves-light"
                  @click="searchSupplier()"
                >
                  Search
                </button>
                <button
                  class="btn btn-success waves-effect"
                  @click="resetSupplier()"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <table
                  id="table_id"
                  class="table table-bordered table-condensed table-hover"
                >
                  <thead>
                    <tr>
                      <th class="text-center">Supplier</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="supplier in suppliers"
                      :key="supplier.id"
                      style="cursor: pointer"
                      @click="selectSupplier(supplier)"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <td>{{ supplier.name }}</td>
                    </tr>
                    <tr v-show="suppliers.length < 1">
                      <td class="text-center">No results found.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END SUPPLIER MODAL-->
  </div>
</template>
<script>
var moment = require("moment");
moment().format();

import swal from "sweetalert";
import PreLoader from "../PreLoader.vue";

export default {
  components: {
    // "date-picker": datePicker,
    "pre-loader": PreLoader
  },

  data() {
    return {
      suppliers: [],
      items: [],
      authenticatedUser: [],
      roles: [],
      search: {
        item: null,
        supplier: null
      },
      data: {
        supplier: null,
        orders: [],
        amount: {
          total: 0,
          subtotal: 0,
          shipping: 0,
          tax: 0,
          tax_overide: 0,
          overide: 0,
          shipping_cost: 0
        },
        user: [],
        delivery_date: "",
        status: "draft"
      },
      warehouses: [],
      file: "File"
    };
  },

  beforeMount() {},

  created() {
    this.suppliers = this.$global.getSupplier();
    this.warehouses = this.$global.getWarehouses();
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.loadItems();
  },

  mounted() {},

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    submitApproval() {
      swal("Submit now for approval?", {
        buttons: {
          submit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "submit":
            this.$validator.validateAll().then(result => {
              this.data.user = this.authenticatedUser;
              // console.log(this.data);
              this.$http
                .post("api/purchase_order/submit_approval", this.data)
                .then(response => {
                  console.log(response.body);

                  swal("Submitted for approval!", {
                    icon: "success"
                  });
                });

              this.$root.$emit("Sidebar");
              this.$router.push({
                path: "/purchase_orders"
              });
              // if (result) {
              //   this.$http
              //     .put(
              //       "api/purchase_order/" + this.$route.params.purchase_order,
              //       this.data
              //     )
              //     .then(response => {});

              //   this.$http
              //     .post(
              //       "api/purchase_order/submit_approval/" +
              //         this.$route.params.purchase_order
              //     )
              //     .then(response => {
              //       this.$http.get("api/purchase_order").then(response => {
              //         this.$global.setPurchaseOrders(response.body);
              //       });

              //       this.$http
              //         .get("api/users/" + this.authenticatedUser.id)
              //         .then(response => {
              //           this.$global.setRoles(response.body.roles);
              //         });

              //       this.getPurchaseOrder();
              //       this.roles = this.$global.getRoles();
              //       swal("Submitted for approval!", {
              //         icon: "success"
              //       });
              //     });
              // }
            });

            break;

          default:
            break;
        }
      });
    },
    submitSupplier() {
      swal("Order now?", {
        buttons: {
          order: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "order":
            this.$http
              .post(
                "api/purchase_order/submit_supplier/" +
                  this.$route.params.purchase_order
              )
              .then(response => {
                this.$http.get("api/purchase_order").then(response => {
                  this.$global.setPurchaseOrders(response.body);
                });

                this.$http
                  .get("api/users/" + this.authenticatedUser.id)
                  .then(response => {
                    this.$global.setRoles(response.body.roles);
                  });

                this.getPurchaseOrder();
                this.roles = this.$global.getRoles();
                swal("Submitted to supplier!", {
                  icon: "success"
                });
              });

            break;

          default:
            break;
        }
      });
    },
    savePurchaseOrder() {
      this.$http
        .put(
          "api/purchase_order/" + this.$route.params.purchase_order,
          this.data
        )
        .then(response => {
          this.$http.get("api/purchase_order").then(response => {
            this.$global.setPurchaseOrders(response.body);
          });

          swal(
            "Purchase Order #" +
              this.$route.params.purchase_order +
              " was succesfully updated!",
            {
              icon: "success"
            }
          );
        });
    },

    searchItem() {
      this.$http.post("api/items/search", this.search).then(response => {
        this.items = response.body;
      });
    },
    selectSupplier(supplier) {
      this.data.supplier = supplier;
    },

    selectItem(item) {
      var exists = false;

      for (var i = 0; i < this.data.orders.length; i++) {
        if (this.data.orders[i].id == item.id) {
          exists = true;
        }
      }

      if (!exists) {
        this.$http.get("api/items/" + item.id).then(response => {
          this.data.orders.push(response.body);
        });
      }
      var x = document.getElementById("snackbar");
      x.className = "show";
      setTimeout(function() {
        x.className = x.className.replace("show", "");
      }, 2000);
    },
    removeItem(id) {
      swal({
        title: "",
        text: "Remove this item in the list?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          for (var index = 0; index < this.data.orders.length; index++) {
            if (this.data.orders[index].id == id) {
              this.data.orders.splice(index, 1);
            }
          }

          this.subtotal();
        }
      });
    },
    removeFile() {
      swal({
        title: "",
        text: "Remove uploaded file?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.data.upload = [];
        }
      });
    },
    exit() {
      swal("Are you sure you want to go back?", {
        icon: "warning",
        buttons: {
          exit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "exit":
            this.$router.go(-1);
            break;

          default:
            break;
        }
      });
    },
    subtotal() {
      var sum = 0,
        tax = 0,
        shipping = 0,
        temp_tax = 0;

      for (var index = 0; index < this.data.orders.length; index++) {
        if (
          this.data.orders[index].pivot.price &&
          this.data.orders[index].pivot.qty
        ) {
          sum +=
            this.data.orders[index].pivot.price *
            this.data.orders[index].pivot.qty;
        }
        if (this.data.orders[index].pivot.tax_rate) {
          temp_tax = this.data.orders[index].pivot.tax_rate / 100;
          tax +=
            temp_tax *
            this.data.orders[index].pivot.price *
            this.data.orders[index].pivot.qty;
        }
      }

      if (this.data.amount.overide) {
        tax = 0;

        if (this.data.amount.tax_overide) {
          tax = parseFloat(this.data.amount.tax_overide);
        }
      }

      if (this.data.amount.shipping_cost) {
        shipping = parseFloat(this.data.amount.shipping_cost);
      }

      console.log(sum);
      this.data.amount.subtotal = sum;
      this.data.amount.tax = tax;
      this.data.amount.shipping = shipping;
      this.data.amount.total = sum + tax + parseFloat(shipping);
    },

    formatPrice(value) {
      var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2
      });
      return formatter.format(value);
    },
    searchSupplier() {
      this.$http.post("api/supplier/search", this.search).then(response => {
        this.suppliers = response.body;
      });
    },
    resetSupplier() {
      this.search.supplier = "";
      this.searchSupplier();
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

.table-wrap {
  height: 300px;
  overflow-y: auto;
  overflow-x: auto;
}

select {
  padding: 3px 0px !important;
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

/* for row in receiving report */
.reportRow {
  width: 50%;
  text-align: left;
  float: left;
  margin-top: 14px;
  line-height: 100%;
}
.receives {
  width: 100%;
  background: lightblue;
}
#snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 40%;
  top: 100px;
  font-size: 17px;
}

#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 1s, fadeout 1s 1s;
  animation: fadein 1s, fadeout 1s 1s;
}

@-webkit-keyframes fadein {
  from {
    top: 10px;
    opacity: 0;
  }
  to {
    top: 100px;
    opacity: 1;
  }
}

@keyframes fadein {
  from {
    top: 10px;
    opacity: 0;
  }
  to {
    top: 100px;
    opacity: 1;
  }
}

@-webkit-keyframes fadeout {
  from {
    top: 100px;
    opacity: 1;
  }
  to {
    top: 190px;
    opacity: 0;
  }
}

@keyframes fadeout {
  from {
    top: 100px;
    opacity: 1;
  }
  to {
    top: 190px;
    opacity: 0;
  }
}
</style>
