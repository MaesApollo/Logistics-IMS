<template>
  <div class="container-fluid">
    <pre-loader></pre-loader>
    <div class="block-header">
      <div class="row clearfix">
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
          <button
            type="button"
            class="btn btn-default waves-effect"
            style="margin-right: 10px"
            data-toggle="modal"
            data-target="#paymentModal"
            :hidden="!roles.accounting_only"
          >
            <i class="material-icons">add_circle</i>
            <span>Add Payment</span>
          </button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-12 col-sm-12">
            <h4>Purchase Order</h4>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-2 col-sm-12">
            <img src="../../img/logo.jpg" />
          </div>

          <div class="col-md-2 col-sm-12 text-center">
            <h5>PO#{{ $route.params.purchase_order }}</h5>
            <p v-if="data.supplier != null">{{ data.supplier.name }}</p>
            <p v-else>
              <small>
                <i>select supplier</i>
              </small>
            </p>
            <button
              type="button"
              class="btn btn-sm btn-info waves-effect disabled"
              v-if="
                data.status == 'on order' ||
                  data.status == 'order complete' ||
                  data.status == 'declined'
              "
            >
              <span>Supplier</span>
            </button>
          </div>
          <div class="col-md-8 col-sm-12">
            <div class="row clearfix">
              <div class="col-md-12">
                <div
                  class="alert alert-approval"
                  v-show="data.status == 'approved'"
                >
                  <b>Status:</b> Pending Payment
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6 col-sm-12">
                <p>
                  <b>Expected Delivery Date:</b>
                  <b-form-datepicker
                    id="datepicker-valid"
                    :state="true"
                    v-model="data.delivery_date"
                    name="expected_date"
                    :disabled="
                      data.status == 'on order' ||
                        data.status == 'order complete' ||
                        data.status == 'declined'
                    "
                  ></b-form-datepicker>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-12">
            <!-- NAVIGATION -->
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
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
                    <div
                      class="alert alert-default"
                      v-show="
                        data.status == 'on order' &&
                          data_receives.receives.length < 1
                      "
                    >
                      <button
                        type="button"
                        class="btn btn-default waves-effect disabled"
                      >
                        <span>Receive checked items</span>
                      </button>
                    </div>
                    <div
                      class="alert alert-default"
                      v-show="
                        data.status == 'on order' &&
                          data_receives.receives.length > 0
                      "
                    >
                      <button
                        type="button"
                        class="btn btn-default waves-effect"
                        data-toggle="modal"
                        data-target="#receiveModal"
                      >
                        <span>Receive checked items</span>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <table class="table table-stripped">
                        <thead>
                          <tr>
                            <th v-show="data.status == 'on order'"></th>
                            <th>Requested Item</th>
                            <th>Code#</th>
                            <th>Qty</th>
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
                            <td v-show="data.status == 'on order'">
                              <input
                                type="checkbox"
                                v-bind:id="order.id"
                                class="chk-col-light-blue"
                                v-bind:value="order"
                                v-model="data_receives.receives"
                                v-show="
                                  order.pivot.qty != order.total_qty_received
                                "
                              />

                              <input
                                type="text"
                                v-model="order.pivot.qty"
                                @keyup="subtotal"
                                style="max-width: 40px"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:1|numeric'"
                                v-bind:name="order.id + 'qty'"
                              />

                              <label v-bind:for="order.id"></label>
                            </td>
                            <td>{{ order.name }} - {{ order.description }}</td>
                            <td>{{ order.id }}</td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.qty"
                                @keyup="subtotal"
                                style="max-width: 40px"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:1|numeric'"
                                v-bind:name="order.id + 'qty'"
                              />
                              <span v-else>{{ order.pivot.qty }}</span>
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'qty')"
                                  >Qty is required</small
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
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:0|decimal:2'"
                                v-bind:name="order.id + 'price'"
                              />
                              <span v-else>{{ order.pivot.price }}</span>
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
                  <div class="col-md-12">
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
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              %
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.tax_overide"
                                @keyup="subtotal"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <div class="demo-checkbox">
                                <input
                                  type="checkbox"
                                  id="basic_checkbox_1"
                                  v-model="data.amount.overide"
                                  @keyup="subtotal"
                                  :disabled="
                                    data.status == 'on order' ||
                                      data.status == 'order complete'
                                  "
                                />
                                <label for="basic_checkbox_1"
                                  >Overide tax amount</label
                                >
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>Shipping</td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.shipping_cost"
                                @keyup="subtotal"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
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
                        Created by: {{ data.user.name }}
                        <br />
                        <br />
                        Date created: {{ data.created_at }}
                        <br />
                        Last update: {{ data.updated_at }}
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
          <div class="col-md-5">
            <p>
              <small class="text-danger" v-show="errors.has('qty')"
                >Quantity is required, minimum value 1.</small
              >
              <small class="text-danger" v-show="errors.has('price')">
                <br />Price is required, minimum value 0.
              </small>
              <small class="text-danger" v-show="errors.has('tax')">
                <br />Tax is required, minimum value 0.
              </small>
            </p>
          </div>

          <div class="col-md-6">
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ data.amount.subtotal.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Shipping:</th>
                    <td>{{ data.amount.shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Tax:</th>
                    <td>{{ data.amount.tax.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ data.amount.total.toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- START PAYMENT MODAL -->

    <div
      class="modal fade"
      id="paymentModal"
      tabindex="-1"
      aria-hidden="true"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="header">
              <h3>Add New Payment</h3>
            </div>
            <form>
              <div class="body">
                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>Invoice ID</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>Total</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>Paid</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>New Paid</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>Mode of Payment</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div class="col-sm-12">
                    <br />
                    <span>Remarks</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          autocomplete="off"
                          autofocus="on"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button type="button" class="btn btn-success">Pay</button>
          </div>
        </div>
      </div>
    </div>
    <!-- END ITEM MODAL -->
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
        item: null
      },
      data: {
        supplier: [],
        orders: [],
        amount: {
          total: 0,
          subtotal: 0,
          shipping: 0,
          tax: 0
        },
        upload: [],
        user: [],
        delivery_date: "",
        status: "",
        created_at: "",
        updated_at: ""
      },
      data_receives: {
        purchase_order_id: null,
        date_receive: null,
        receives: []
      },
      warehouses: []
    };
  },

  beforeMount() {},

  created() {
    this.getPurchaseOrder();
    this.loadItems();
    this.suppliers = this.$global.getSupplier();
    this.warehouses = this.$global.getWarehouses();
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
  },

  mounted() {},

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    getPurchaseOrder() {
      this.$http
        .get("api/purchase_order/" + this.$route.params.purchase_order)
        .then(response => {
          this.data.delivery_date = response.body.delivery_date;
          this.data.orders = response.body.item;
          this.data.supplier = response.body.supplier;
          this.data.amount.tax = response.body.tax;
          this.data.amount.shipping_cost = response.body.shipping_fee;
          this.data.status = response.body.status;
          this.data.user = response.body.user;
          this.data.created_at = response.body.created_at;
          this.data.updated_at = response.body.updated_at;
          this.subtotal();
          $(".page-loader-wrapper").fadeOut();
        });
    },
    clear() {
      this.purchase_order.orders = [];
    },
    acceptPurchaseOrder() {
      swal("Accept this order?", {
        buttons: {
          accept: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "accept":
            this.$validator.validateAll().then(result => {
              if (result) {
                this.$http
                  .put(
                    "api/purchase_order/" + this.$route.params.purchase_order,
                    this.data
                  )
                  .then(response => {});

                this.$http
                  .post(
                    "api/purchase_order/accept/" +
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
                    swal("Purchased order accepted!", {
                      icon: "success"
                    });
                  });
              }
            });
            break;

          default:
            break;
        }
      });
    },
    declinePurchaseOrder() {
      swal({
        text:
          "Decline Purchase Order #" + this.$route.params.purchase_order + "?",
        dangerMode: true,
        buttons: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .post(
              "api/purchase_order/decline/" + this.$route.params.purchase_order
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
              swal("Purchase order declined!", {
                dangerMode: true
              });
            });
        }
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
              if (result) {
                this.$http
                  .put(
                    "api/purchase_order/" + this.$route.params.purchase_order,
                    this.data
                  )
                  .then(response => {});

                this.$http
                  .post(
                    "api/purchase_order/submit_approval/" +
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
                    swal("Submitted for approval!", {
                      icon: "success"
                    });
                  });
              }
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
    receiveItem() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.data_receives.purchase_order_id = this.$route.params.purchase_order;
          this.$http.post("api/stocks", this.data_receives).then(response => {
            this.getPurchaseOrder();
            swal("Ordered Item Successfully Received!", {
              icon: "success"
            });
            // $("#receiveModal").modal("hide");

            this.data_receives.receives = [];

            this.$http.get("api/purchase_order").then(response => {
              this.$global.setPurchaseOrders(response.body);
            });

            this.$http.get("api/items").then(response => {
              this.$global.setItems(response.body);
            });
          });
        }
      });
    },
    pdfChanged(e) {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.data.upload = e.target.files[0];

          var fileReader = new FileReader();

          fileReader.readAsDataURL(e.target.files[0]);

          fileReader.onload = e => {
            this.data.upload.file = e.target.result;
          };
        }
      });
    },
    searchItem() {
      this.$http.post("api/items/search", this.search).then(response => {
        this.items = response.body;
      });
    },
    selectSupplier(supplier) {
      this.data.supplier = supplier;
      // $("#supplierModal").modal("hide");
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

      // $("#itemModal").modal("hide");
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

.footer {
}
</style>
