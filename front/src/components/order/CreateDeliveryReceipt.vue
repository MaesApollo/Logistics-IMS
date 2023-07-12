<template>
  <div class="container-fluid">
    <pre-loader></pre-loader>
    <div class="block-header" id="delivery_receipt_button">
      <div class="row clearfix">
        <div class="col-lg-10 col-md-10">
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>

          <button class="btn btn-lg btn-default waves-effect" @click="printNow">
            Print Preview
          </button>
        </div>
      </div>
    </div>
    <div class="card" id="delivery_receipt_form">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-3 col-sm-3">
            <h4>Delivery Receipt</h4>
          </div>
          <div class="col-md-9 col-sm-9">
            <div
              class="alert alert-warning"
              v-show="delivery_receipt.status == 'for delivery'"
            >
              <b>Status:</b> For Delivery
            </div>
            <div
              class="alert alert-success"
              v-show="delivery_receipt.status == 'delivering'"
            >
              <b>Status:</b> On Shipped
            </div>
            <div
              class="alert alert-success"
              v-show="delivery_receipt.status == 'delivered'"
            >
              <b>Status:</b> Delivered
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div
            class="col-md-3 col-sm-12"
            style="margin-top:-10px"
            v-if="
              sales_order.client.class == 'INET CLIENTS' ||
                sales_order.client.class == 'DCTECH'
            "
          >
            <img src="../../img/new_email.gif" style="width:100%" />
          </div>
          <div
            class="col-md-3 col-sm-12"
            style="margin-top:-10px"
            v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
          >
            <img src="../../img/soln.gif" style="width:100%" />
          </div>
          <div class="col-md-3 col-sm-12">
            From
            <br />
            <address>
              <strong>Dctech Microservices, Inc.</strong>
              <br />Dctech Building, Ponciano Reyes Street <br />Davao City,
              8000, Philippines
            </address>
          </div>
          <div class="col-md-3 col-sm-12">
            To
            <br />
            <address>
              <strong>{{ sales_order.client.name }}</strong>
              <br />
              {{ sales_order.client.location }}
              <br />
              {{ sales_order.client.contact }}
            </address>
          </div>
          <div class="col-md-3 col-sm-12">
            <p>
              Delivery Receipt
              <b>#{{ delivery_receipt.id }}</b>
              <br />Material Request
              <b>#{{ sales_order.id }}</b>
              <br />
              Date: {{ delivery_receipt.created_at }}
              <br />
            </p>
          </div>
        </div>

        <!-- START ORDER TABLE -->
        <div class="row clearfix">
          <div class="col-md-12">
            <div class="table-wrap" style="height:auto">
              <div class="table-responsive">
                <table
                  class="table table-striped table-condensed table-hover cursor-pointer"
                  style="height:auto"
                >
                  <!-- <div class="watermark">
                    <img
                      src="../../img/logo.jpg"
                      style="width:30%;margin-top:50px"
                    />
                  </div> -->
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Description</th>
                      <!-- <th>Serial/Barcode</th> -->
                      <th>Qty</th>
                      <th>Qty Return</th>
                      <th>Note/Serial</th>
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
                      v-for="(order, index) in delivery_receipt.orders"
                      :key="index"
                      @click="edit(order)"
                      :disabled="delivery_receipt.status != 'for delivery'"
                    >
                      <td>{{ order.id }}</td>
                      <td>{{ order.name }}</td>
                      <td>{{ order.description }}</td>
                      <!-- <td>
                        <span
                          v-for="(serial, index) in order.ordered_serial"
                          :key="index"
                        >
                          {{ serial }}

                          <br />
                        </span>
                      </td> -->
                      <!-- <td v-if="order.type.name == 'Serialize'">
                        <span
                          v-for="(serial, index) in order.ordered_serial.slice(
                            0,
                            5
                          )"
                          :key="index"
                        >
                          {{ serial }}

                          <br />
                        </span>
                        <span v-if="order.ordered_serial.length > 5">
                          + {{ order.ordered_serial.length - 5 }} more
                        </span>
                      </td>
                      <td v-else></td> -->
                      <td v-if="order.type.name == 'Consumable'">
                        {{ order.delivering_qty }}
                      </td>

                      <td v-else>{{ order.ordered_serial.length }}</td>
                      <td>
                        {{ order.return_qty }}
                      </td>
                      <td>{{ order.note }}</td>
                      <td>{{ order.price }}</td>
                      <td v-if="order.type.name == 'Consumable'">
                        {{ order.price * order.delivering_qty }}
                      </td>
                      <td v-else>
                        {{ order.price * order.ordered_serial.length }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- END ORDER TABLE -->
        <div
          class="row clearfix"
          v-show="delivery_receipt.status == 'for delivery'"
        >
          <div class="col-md-12">
            <button
              class="btn btn-default waves-effect"
              data-toggle="modal"
              data-target="#salesOrderModal"
            >
              <i class="material-icons">note_add</i>
              <span>Add Items</span>
            </button>
          </div>
        </div>
        <div class="row clearfix">
          <!-- REMARKS -->
          <div class="col-md-6">
            <div class="row clearfix">
              <div class="col-md-8">
                <br />
                <label>Note:</label>
                <br />
                <p>{{ sales_order.note }}</p>
                <br />
                <label>Requested by:</label>
                <br />
                <p>{{ sales_order.requestor }}</p>
              </div>
            </div>
          </div>

          <!-- AMOUNT -->
          <div class="col-md-6">
            <div class="table-responsive">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ delivery_receipt.amount.subtotal.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Tax (0%):</th>
                    <td>{{ delivery_receipt.amount.tax.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Shipping:</th>
                    <td>{{ delivery_receipt.amount.shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ delivery_receipt.amount.total.toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <hr />
        <div class="row clearfix">
          <center>
            <strong
              ><em>RECEIVED ABOVE ITEMS IN GOOD ORDER AND CONDITION</em></strong
            >
          </center>
        </div>
        <hr />
        <div class="row clearfix">
          <div class="col-xs-2">
            <b>Encoded By:</b>
            <br />
            <br />
            <br />
            <b>Checked By:</b>
          </div>
          <div class="col-xs-4">
            {{ delivery_receipt.user.name }}
            <br />
            <br />
            <br />
            Alvin Jay P. Angcon
          </div>
          <div class="col-xs-2">
            <b>Released by:</b>
            <br />
            <br />
            <br />
            <b>Received By:</b>
          </div>
          <div class="col-xs-4">
            <b
              v-if="
                sales_order.client.class == 'INET CLIENTS' ||
                  sales_order.client.class == 'DCTECH'
              "
              >Emmanuel G. Llabore Jr.</b
            >
            <b v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
              >Gabriel Sanchez</b
            >
            <br />
            <br />
            <br />_________________________________
            <br />
            Signature over Printed Name/Date
          </div>
        </div>
      </div>
    </div>

    <!-- START SALES ORDER MODAL -->
    <div
      class="modal fade"
      id="salesOrderModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <h4 class="modal-title">
              SO#{{ sales_order.id }} ITEM ORDERED
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                @click="resetItemSelected"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </h4>
          </div>
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <div class="input-group">
                  <span>Select Product:</span>
                  <div class="form-line">
                    <select
                      class="form-control"
                      v-model="selected_item_index"
                      @change="selectItem"
                    >
                      <option disabled>-- PLEASE SELECT PRODUCT --</option>
                      <option
                        v-for="(item, index) in sales_order.orders"
                        :key="index"
                        v-bind:value="item.id"
                        v-show="!item_exist(item)"
                        >{{ item.name }} - {{ item.description }} (
                        {{ item.category.name }} )</option
                      >
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-lg-6 col-md-6">
                <div class="input-group">
                  <span>Price:</span>
                  <div class="form-line">
                    <input
                      type="text"
                      class="form-control disabled"
                      v-model="item_selected.price"
                      disabled
                    />
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="input-group">
                  <span>Qty:</span>
                  <div class="form-line">
                    <input
                      type="text"
                      class="form-control"
                      v-bind:value="item_selected.ordered_serial.length"
                      v-if="item_selected.total_qty <= 0"
                      disabled
                    />
                    <input
                      type="text"
                      class="form-control"
                      v-model="item_selected.delivering_qty"
                      v-else
                      @keyup.enter="pushSerial(item_selected.id)"
                    />
                  </div>
                  <small
                    style="font-size:8px;color:red"
                    v-show="item_selected.type.name == 'Serialize'"
                    >Type qty & click enter to add available serials</small
                  >
                </div>
              </div>
            </div>
            <div class="row clearfix" hidden>
              <!-- test -->
              <div class="col-lg-9 col-md-9">
                <div class="input-group">
                  Select Serial

                  <model-list-select
                    :list="item_selected.serials"
                    v-model="serial"
                    option-value="serial"
                    option-text="serial"
                    placeholder="Select serial available"
                  ></model-list-select>
                </div>
              </div>
              <!-- end of test -->

              <!-- <div class="col-lg-9 col-md-9">
                <div class="input-group">
                  <span>Serial:</span>
                  <div class="form-line">
                    <select
                      class="form-control"
                      v-model="serial"
                      @click="checkSerial"
                      data-live-search="true"
                    >
                      <option disabled
                        >-- PLEASE SELECT AVAILABLE SERIALS --</option
                      >

                      <option
                        v-for="(number, index) in item_selected.serials"
                        :key="index"
                        v-bind:value="number.serial"
                        >{{ number.serial }}</option
                      >
                    </select>
                  </div>

                  <small
                    class="text-success pull-left"
                    v-show="serial != '' && serial_availability"
                    >Serial is available.</small
                  >
                  <small
                    class="text-danger pull-left"
                    v-show="serial != '' && !serial_availability"
                    >Serial is not available.</small
                  >
                </div>
              </div> -->
              <div class="col-lg-3 col-md-3">
                <br />
                <button
                  class="btn btn-sm btn-info waves-effect"
                  @click="pushSerial"
                >
                  Add Serial
                </button>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-lg-4 col-md-4">
                <p>Total On-hand Qty: {{ item_selected.total_qty }}</p>
                <p>Delivered Qty: {{ item_selected.delivered_qty }}</p>
                <p>Ordered Qty: {{ item_selected.ordered_qty }}</p>
              </div>
              <div
                class="col-lg-6 col-md-6"
                v-show="item_selected.type.name == 'Serialize'"
              >
                <p>
                  Added Serial:
                  <br />
                  <span
                    v-for="(serial, index) in item_selected.ordered_serial"
                    :key="index"
                  >
                    {{ serial }}

                    <small class="font-8" hidden>
                      <a
                        href="javascript:void(0);"
                        class="col-red"
                        @click="removeSerial(serial)"
                        >remove</a
                      >
                    </small>
                    <br />
                  </span>
                </p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-lg btn-danger waves-effect"
              @click="remove(item_selected.id)"
              v-show="item_exist(item_selected)"
            >
              Remove from Delivery
            </button>
            <button
              class="btn btn-lg btn-default waves-effect"
              @click="add"
              data-dismiss="modal"
              aria-label="Close"
              v-show="item_selected.total_qty != 0"
            >
              Add to Delivery
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- END SALES ORDER MODAL -->

    <!-- PRINT -->
    <div class="card printable" id="print_form">
      <div class="body">
        <div class="row clearfix">
          <center><h2>DELIVERY RECEIPT</h2></center>
        </div>
        <div style="width:100%;display:flex">
          <div
            style="margin-top:-10px;width:25%"
            v-if="
              sales_order.client.class == 'INET CLIENTS' ||
                sales_order.client.class == 'DCTECH'
            "
          >
            <img src="../../img/new_email.gif" width="100%" />
          </div>
          <div
            style="margin-top:-10px;width:25%"
            v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
          >
            <img src="../../img/soln.gif" width="100%" />
          </div>
          <div style="width:5%"></div>
          <div style="width:25%">
            From
            <br />
            <address>
              <strong>Dctech Microservices, Inc.</strong>
              <br />Dctech Building, Ponciano Reyes Street <br />Davao City,
              8000, Philippines
            </address>
          </div>
          <div style="width:25%">
            To
            <br />
            <address>
              <strong>{{ sales_order.client.name }}</strong>
              <br />
              {{ sales_order.client.location }}
              <br />
              {{ sales_order.client.contact }}
            </address>
          </div>
          <div style="width:20%">
            <p>
              Delivery Receipt
              <b>#{{ delivery_receipt.id }}</b>
              <br />Material Request
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
            <div class="table-wrap" style="height:auto;min-height:35vh">
              <div class="table-responsive">
                <table
                  class="table table-striped table-condensed"
                  style="height:auto"
                >
                  <!-- <div class="watermark">
                    <img
                      src="../../img/logo.jpg"
                      style="width:30%;margin-top:50px"
                    />
                  </div> -->
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Description</th>
                      <!-- <th>Serial/Barcode</th> -->
                      <th>Qty</th>
                      <th>Qty Return</th>
                      <th>Note/Serial</th>
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
                      v-for="(order, index) in delivery_receipt.orders"
                      :key="index"
                    >
                      <td>{{ order.id }}</td>
                      <td>{{ order.name }}</td>
                      <td>{{ order.description }}</td>
                      <!-- <td>
                        <span
                          v-for="(serial, index) in order.ordered_serial"
                          :key="index"
                        >
                          {{ serial }}

                          <br />
                        </span>
                      </td> -->
                      <!-- <td v-if="order.type.name == 'Serialize'">
                        <span
                          v-for="(serial, index) in order.ordered_serial.slice(
                            0,
                            5
                          )"
                          :key="index"
                        >
                          {{ serial }}

                          <br />
                        </span>
                        <span v-if="order.ordered_serial.length > 5">
                          + {{ order.ordered_serial.length - 5 }} more
                        </span>
                      </td>
                      <td v-else></td> -->
                      <td v-if="order.type.name == 'Consumable'">
                        {{ order.delivering_qty }}
                      </td>
                      <td v-else>{{ order.ordered_serial.length }}</td>
                      <td>{{ order.return_qty }}</td>
                      <td>{{ order.note }}</td>
                      <td>{{ order.price }}</td>
                      <td v-if="order.type.name == 'Consumable'">
                        {{ order.price * order.delivering_qty }}
                      </td>
                      <td v-else>
                        {{ order.price * order.ordered_serial.length }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- END ORDER TABLE -->
        <div class="signatories">
          <div class="row clearfix">
            <!-- REMARKS -->
            <div class="col-md-6 col-xs-6">
              <div class="row clearfix">
                <div class="col-md-8 col-xs-8">
                  <br />
                  <label>Note:</label>
                  <br />
                  <p>{{ sales_order.note }}</p>
                  <br />
                  <label>Requested by:</label>
                  <br />
                  <p>{{ sales_order.requestor }}</p>
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
                      <td>{{ delivery_receipt.amount.subtotal.toFixed(2) }}</td>
                    </tr>
                    <tr>
                      <th>Tax (0%):</th>
                      <td>{{ delivery_receipt.amount.tax.toFixed(2) }}</td>
                    </tr>
                    <tr>
                      <th>Shipping:</th>
                      <td>{{ delivery_receipt.amount.shipping.toFixed(2) }}</td>
                    </tr>
                    <tr>
                      <th>Total:</th>
                      <td>{{ delivery_receipt.amount.total.toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <hr />
          <div class="row clearfix">
            <center>
              <strong
                ><em
                  >RECEIVED ABOVE ITEMS IN GOOD ORDER AND CONDITION</em
                ></strong
              >
            </center>
          </div>
          <hr />
          <div class="row clearfix">
            <div class="col-xs-2">
              <b>Encoded By:</b>
              <br />
              <br />
              <br />
              <b>Checked By:</b>
            </div>
            <div class="col-xs-4">
              {{ delivery_receipt.user.name }}
              <br />
              <br />
              <br />
              Alvin Jay P. Angcon
            </div>
            <div class="col-xs-2">
              <b>Released By:</b>
              <br />
              <br />
              <br />
              <b>Received By:</b>
            </div>
            <div class="col-xs-4">
              <b
                v-if="
                  sales_order.client.class == 'INET CLIENTS' ||
                    sales_order.client.class == 'DCTECH'
                "
                >Emmanuel G. Llabore Jr.</b
              >
              <b v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
                >Gabriel Sanchez</b
              >
              <br />
              <br />
              <br />_________________________________
              <br />
              Signature over Printed Name/Date
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

import PreLoader from "../PreLoader.vue";
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "pre-loader": PreLoader,
    "model-list-select": ModelListSelect
  },

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
        amount: {
          shipping: 0,
          tax: 0,
          subtotal: 0,
          total: 0
        },
        status: "",
        note: "",
        requestor: "",
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
          subtotal: 0,
          tax: 0,
          shipping: 0,
          total: 0
        }
      },
      selected_item_index: 0,
      item_selected: {
        id: "",
        type: {
          name: ""
        },
        serials: [],
        ordered_serial: [],
        price: "",
        total_qty: 0,
        delivered_qty: 0,
        ordered_qty: 0,
        delivering_qty: 0,
        diff: 0
      },
      temp_orders: [],
      roles: [],
      serial: "",
      serials: [],
      number: "",
      print: false,
      serial_availability: false,
      diff: 0
    };
  },

  beforeMount() {
    this.loadDeliveryReceipt();
    this.roles = this.$global.getRoles();
  },

  created() {},

  mounted() {
    window.onafterprint = function() {
      $(".content").css("margin-left", "315px");
      $(".content").css("margin-right", "15px");
      $(".content").css("margin-top", "100px");
      $("#print_form").css("display", "none");
      $("#delivery_receipt_button").css("display", "block");
      $("#delivery_receipt_form").css("display", "block");
      $("#leftsidebar").css("display", "inline-block");
      $(".navbar").css("display", "block");
    };
  },

  methods: {
    reset() {
      this.serial = {};
    },
    selectFromParentComponent1() {
      // select option from parent component
      this.serial = this.options[0];
    },

    loadDeliveryReceipt() {
      this.$http
        .get("api/delivery_receipt/" + this.$route.params.order)
        .then(response => {
          console.log(response.body);
          this.delivery_receipt.id = response.body.id;
          this.delivery_receipt.orders = response.body.orders;
          this.delivery_receipt.user = response.body.user;
          this.delivery_receipt.status = response.body.status;
          this.delivery_receipt.created_at = response.body.created_at;
          this.delivery_receipt.received_at = response.body.received_at;
          this.delivery_receipt.delivered_at = response.body.received_at;
          // this.delivery_receipt.orders.length = 1;
          // this.item_selected.ordered_serial.length = 1;
          // this.delivery_receipt.orders.ordered_serial.length = 1;

          this.loadSalesOrder(response.body.sales_order_id);
          this.compute();
        });
    },

    loadSalesOrder(sales_order_id) {
      this.$http
        .get("api/sales_order/receipts/" + sales_order_id)
        .then(response => {
          this.sales_order.orders = response.body.sales_order_details;
          if (response.body.client != null)
            this.sales_order.client = response.body.client;
          this.sales_order.user = response.body.user;
          this.sales_order.note = response.body.note;
          this.sales_order.requestor = response.body.requestor;
          this.sales_order.status = response.body.status;
          this.sales_order.created_at = response.body.created_at;
          this.sales_order.remarks = response.body.remarks;
          this.sales_order.id = response.body.id;

          if (this.delivery_receipt.orders.length > 0) {
            this.temp_orders = this.delivery_receipt.orders;
          }
          console.log(this.sales_order);

          $(".page-loader-wrapper").fadeOut();
        });
    },

    selectItem() {
      var temp;
      for (var index = 0; index < this.sales_order.orders.length; index++) {
        if (this.sales_order.orders[index].id == this.selected_item_index) {
          temp = this.sales_order.orders[index];
          // temp.serials = [];
        }
      }
      console.log(temp.serials);
      this.item_selected = temp;

      // let items = [];
      // this.item_selected.serials.forEach(function(item) {
      //   if (item.status == "stocked in") {
      //     items.push({
      //       serial: item.serial
      //     });
      //   }
      // });

      // this.item_selected.serials = items;
    },

    pushSerial(id) {
      this.item_selected.ordered_serial = [];
      if (
        this.item_selected.delivering_qty <= this.item_selected.total_qty &&
        this.item_selected.delivering_qty <= this.item_selected.ordered_qty
      ) {
        this.$http
          .post("api/stock/getSerialsPerItem", this.item_selected)
          .then(response => {
            console.log(response.body);
            var serials = Object.values(response.body);

            serials.forEach(item => {
              this.item_selected.ordered_serial.push(item.serial);
            });
          });
      } else {
        swal("Quantity must not exceed Ordered Qty and Total On-Hand", {
          icon: "warning"
        });
      }
    },

    checkSerial() {
      if (this.serial != "") {
        this.$http
          .get(
            "api/items/serial/check/" + this.serial + "" + this.item_selected.id
          )
          .then(response => {
            // console.log(response.body);
            this.serial_availability = response.body;

            for (
              var index = 0;
              index < this.item_selected.ordered_serial.length;
              index++
            ) {
              if (this.item_selected.ordered_serial[index] == this.serial) {
                this.serial_availability = false;
              }
            }
          });
      }
    },

    removeSerial(serial) {
      for (
        var index = 0;
        index < this.item_selected.ordered_serial.length;
        index++
      ) {
        if (this.item_selected.ordered_serial[index] == serial) {
          this.item_selected.ordered_serial.splice(index, 1);
        }
      }

      if (this.item_selected.ordered_serial.length < 1) {
        this.remove(this.item_selected.id);
        this.resetItemSelected();
      }
    },

    resetItemSelected() {
      this.selected_item_index = 0;
      this.item_selected = {
        id: "",
        type: {
          name: ""
        },
        serials: [],
        ordered_serial: [],
        price: "",
        total_qty: 0,
        delivered_qty: 0,
        ordered_qty: 0,
        delivering_qty: 0
      };
    },

    remove(id) {
      for (
        var index = 0;
        index < this.delivery_receipt.orders.length;
        index++
      ) {
        if (this.sales_order.orders[index].id == id) {
          this.delivery_receipt.orders.splice(index, 1);
        }
      }

      this.resetItemSelected();
      // $("#salesOrderModal").modal("hide");
    },

    add(item) {
      // $("#salesOrderModal").modal("hide");

      if (
        !this.item_exist(this.item_selected) &&
        this.item_selected.delivering_qty <= this.item_selected.total_qty &&
        this.item_selected.delivering_qty <= this.item_selected.ordered_qty
      ) {
        console.log(this.item_selected);
        this.delivery_receipt.orders.push(this.item_selected);
        this.compute();
      }

      this.resetItemSelected();
    },

    edit(order) {
      this.item_selected = order;
      $("#salesOrderModal").modal("show");
    },

    // save() {
    //   this.$http
    //     .put(
    //       "api/delivery_receipt/" + this.delivery_receipt.id,
    //       this.delivery_receipt
    //     )
    //     .then(response => {
    //       swal(
    //         "Delivery Receipt #" +
    //           this.delivery_receipt.id +
    //           " was succesfully updated!",
    //         {
    //           icon: "success"
    //         }
    //       );
    //     });
    // },

    confirm() {
      this.$http
        .put(
          "api/delivery_receipt/" + this.delivery_receipt.id,
          this.delivery_receipt
        )
        .then(response => {
          this.$http
            .post("api/delivery_receipt/confirm/" + this.delivery_receipt.id)
            .then(response => {
              console.log(response.body);
              this.loadDeliveryReceipt();

              swal(
                "Delivery Receipt #" +
                  this.delivery_receipt.id +
                  " was succesfully confirmed!",
                {
                  icon: "success"
                }
              );
            });
        });
    },

    printNow() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      $("#print_form").css("display", "block");
      $("#delivery_receipt_button").css("display", "none");
      $("#delivery_receipt_form").css("display", "none");
      $("#leftsidebar").css("display", "none");
      $(".navbar").css("display", "none");

      window.print();
    },
    btnDelivered() {
      //this.$route.params.order

      swal("Change the status to DELIVERED?", {
        icon: "warning",
        buttons: {
          yes: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "yes":
            this.$http
              .post(
                "api/delivery_receipt/change_status/" + this.delivery_receipt.id
              )
              .then(response => {
                this.$http.get("api/delivery_receipt").then(response => {
                  this.$global.setDeliveryReceipt(response.body);
                  $(".page-loader-wrapper").fadeOut();
                });
                this.loadDeliveryReceipt();

                swal(
                  "Delivery Receipt #" +
                    this.delivery_receipt.id +
                    " was succesfully changed!",
                  {
                    icon: "success"
                  }
                );
              });
            break;

          default:
            break;
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

      this.delivery_receipt.amount.shipping = 0;
      this.delivery_receipt.amount.tax = 0;
      this.delivery_receipt.amount.subtotal = sum;
      this.delivery_receipt.amount.total = sum;
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
            this.$router.push({
              path: "/list/sales_order"
            });
            break;

          default:
            break;
        }
      });
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
@media print {
  .printable {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
  }
}
.signatories {
  margin-top: 80px;
}

#print_form {
  display: none;
}

.watermark {
  position: absolute;
  color: lightgray;
  opacity: 0.25;
  font-size: 3em;
  width: 100%;
  top: 8%;
  text-align: center;
  z-index: 0;
}
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

.truncate {
  max-width: 1px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
