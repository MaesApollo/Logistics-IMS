<template>
  <div class="container-fluid">
    <pre-loader></pre-loader>
    <div class="block-header" id="sales_order_button" style="margin-top:-20px">
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

          <!-- SAVE BUTTON -->
          <!-- <button
            class="btn btn-lg btn-info waves-effect"
            @click="saveSalesOrder"
            :hidden="
              sales_order.status == 'approval' ||
                sales_order.status == 'approved' ||
                sales_order.status == 'order complete' ||
                !roles.create_sales_order
            "
          >
            Save
          </button> -->
          <button
            class="btn btn-lg btn-default waves-effect"
            @click="clear"
            :hidden="
              sales_order.status == 'approval' ||
                sales_order.status == 'approved' ||
                sales_order.status == 'order complete'
            "
          >
            Clear Order
          </button>

          <!-- CLIENT BUTTON -->
          <button
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            data-target="#clientModal"
            :hidden="
              sales_order.status == 'approval' ||
                sales_order.status == 'approved' ||
                sales_order.status == 'declined' ||
                sales_order.status == 'order complete'
            "
          >
            Select Client
          </button>

          <!-- APPROVAL BUTTON -->
          <button
            class="btn btn-lg btn-info waves-effect"
            :hidden="
              sales_order.orders.length < 1 ||
                sales_order.status == 'approval' ||
                sales_order.status == 'approved' ||
                sales_order.status == 'declined' ||
                sales_order.status == 'order complete' ||
                sales_order.client.name == '' ||
                !roles.create_sales_order
            "
            @click="sendforApproval"
          >
            Send for Approval
          </button>

          <!--Delete Draft Button -->
          <button
            type="button"
            class="btn btn-lg btn-danger waves-effect"
            :hidden="sales_order.status != 'draft'"
            @click="deleteDraft(sales_order.id)"
          >
            <span>Delete Draft</span>
          </button>

          <!-- DELIVERY BUTTON -->
          <button
            class="btn btn-lg btn-default waves-effect"
            :disabled="
              sales_order.status == 'draft' ||
                sales_order.status == 'approval' ||
                sales_order.status == 'declined' ||
                sales_order.status == 'order complete' ||
                !roles.create_delivery_receipt
            "
            :hidden="sales_order.status != 'approved'"
            @click="createDeliveryReceipt"
          >
            Create Delivery Receipt
          </button>

          <!-- PRINT BUTTON -->
          <button
            class="btn btn-lg btn-default waves-effect"
            @click="printPreview"
          >
            Print Preview
          </button>

          <div
            v-show="sales_order.status == 'approval'"
            style="float:right; display:block;margin-right:-170px"
          >
            <button
              type="button"
              class="btn btn-lg btn-success waves-effect"
              v-show="
                sales_order.status == 'approval' && roles.approved_sales_order
              "
              @click="accept"
            >
              <span>Accept</span>
            </button>
            <button
              type="button"
              class="btn btn-lg btn-danger waves-effect"
              v-show="
                sales_order.status == 'approval' && roles.approved_sales_order
              "
              @click="decline"
            >
              <span>Decline</span>
            </button>
          </div>
        </div>
        <!-- <div class="col-lg-2 col-md-2">
          <div class="pull-right">
            <button
              type="button"
              class="btn btn-lg btn-success waves-effect"
              v-show="
                sales_order.status == 'approval' && roles.approved_sales_order
              "
              @click="accept"
            >
              <span>Accept</span>
            </button>
            <button
              type="button"
              class="btn btn-lg btn-danger waves-effect"
              v-show="
                sales_order.status == 'approval' && roles.approved_sales_order
              "
              @click="decline"
            >
              <span>Decline</span>
            </button>
          </div>
        </div> -->
      </div>
    </div>
    <div class="card">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-4 col-sm-4">
            <h4>MATERIAL REQUEST</h4>
          </div>

          <div class="col-md-8 col-sm-8" id="sales_return_notification">
            <div
              class="alert alert-warning"
              v-show="sales_order.status == 'draft'"
            >
              <b>Status:</b> Draft
            </div>
            <div
              class="alert alert-approval"
              v-show="sales_order.status == 'approval'"
            >
              <b>Status:</b> For Approval
            </div>
            <div
              class="alert alert-success"
              v-show="sales_order.status == 'approved'"
            >
              <b>Status:</b> Order Approved
            </div>
            <div
              class="alert alert-danger"
              v-show="sales_order.status == 'declined'"
            >
              <b>Status:</b> Order Declined
            </div>
            <div
              class="alert alert-success"
              v-show="sales_order.status == 'on order'"
            >
              <b>Status:</b> On Order
            </div>
            <div
              class="alert alert-success"
              v-show="sales_order.status == 'order complete'"
            >
              <b>Status:</b> Order fulfilled
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
          <div class="col-md-3 col-sm-12" style="margin-top:-10px" v-else>
            <img src="../../img/soln.gif" style="width:100%" />
          </div>
          <div class="col-md-3">
            From:
            <br />
            <address>
              <strong>Dctech Microservices, Inc.</strong>
              <br />Dctech Bldg., C. Bangoy Street <br />Davao City, 8000,
              Philippines
            </address>
          </div>
          <div class="col-md-3">
            To:
            <address>
              <strong>{{ sales_order.client.name }}</strong>
              <br />
              <span>{{ sales_order.client.location }}</span>
              <br />
              <span>{{ sales_order.client.contact }}</span>
            </address>
          </div>
          <div class="col-md-3">
            <p>
              Material Request
              <b>#{{ $route.params.order }}</b>
              <br />
              Date Created: {{ sales_order.created_at }}
            </p>
          </div>
        </div>

        <!-- START ORDER TABLE -->
        <div class="row clearfix">
          <div class="col-md-12">
            <div class="table-wrap" style="height:auto">
              <div class="table-responsive">
                <div class="watermark">
                  <img
                    src="../../img/logo.jpg"
                    style="width:30%;margin-top:50px"
                  />
                </div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Qty</th>
                      <th>Delivered Qty</th>
                      <th>Price</th>
                      <th>Amount</th>
                      <th
                        v-show="
                          sales_order.status == 'draft' ||
                            sales_order.status == 'approval'
                        "
                      ></th>
                    </tr>
                  </thead>
                  <tbody v-if="this.sales_order.orders.length == 0">
                    <tr>
                      <td colspan="9" class="text-center">
                        <small class="col-red">
                          <i>No orders yet.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr v-for="order in sales_order.orders" :key="order.id">
                      <td>{{ order.id }}</td>
                      <td>{{ order.name }}</td>
                      <td>{{ order.description }}</td>
                      <td>
                        <input
                          type="text"
                          v-model="order.ordered_qty"
                          @keyup="compute"
                          v-if="
                            sales_order.status == 'draft' ||
                              sales_order.status == 'approval'
                          "
                          v-validate="'required|min_value:1|numeric'"
                          v-bind:name="order.id + 'qty'"
                          :disabled="sales_order.status != 'draft'"
                        />
                        <span v-else>{{ order.ordered_qty }}</span>
                        <p>
                          <small
                            class="text-danger"
                            v-show="errors.has(order.id + 'qty')"
                            >Qty is required</small
                          >
                        </p>
                      </td>
                      <td>{{ order.delivered_qty }}</td>
                      <td>
                        <input
                          type="text"
                          v-model="order.price"
                          @keyup="compute"
                          v-if="
                            sales_order.status == 'draft' ||
                              sales_order.status == 'approval'
                          "
                          v-validate="'required|min_value:0|decimal:2'"
                          v-bind:name="order.id + 'price'"
                          :disabled="sales_order.status != 'draft'"
                        />
                        <span v-else>{{ order.price }}</span>
                        <p>
                          <small
                            class="text-danger"
                            v-show="errors.has(order.id + 'price')"
                            >Price is required</small
                          >
                        </p>
                      </td>
                      <td class="amount">
                        {{ (order.price * order.ordered_qty).toFixed(2) }}
                      </td>
                      <td v-show="sales_order.status == 'draft'">
                        <a
                          href="javascript:void(0);"
                          title="remove"
                          @click="removefromOrder(order.id)"
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
        </div>
        <!-- END ORDER TABLE -->
        <div class="row clearfix" id="item_buttons">
          <div class="col-md-12">
            <button
              class="btn btn-default waves-effect"
              data-toggle="modal"
              data-target="#itemModal"
              :hidden="sales_order.client.name == ''"
              v-show="sales_order.status == 'draft'"
            >
              <i class="material-icons">note_add</i>
              <span>Add Items</span>
            </button>
            <button
              class="btn btn-default waves-effect"
              data-toggle="modal"
              data-target="#itemGroupModal"
              :hidden="sales_order.client.name == ''"
              v-show="sales_order.status == 'draft'"
            >
              <i class="material-icons">note_add</i>
              <span>Add Group</span>
            </button>
          </div>
        </div>

        <div class="row clearfix">
          <!-- NOTES -->
          <div class="col-md-6 col-xs-6">
            <b>Note:</b>
            <div
              class="input-group"
              v-if="
                sales_order.status == 'draft' ||
                  sales_order.status == 'approval'
              "
            >
              <div class="form-line">
                <textarea
                  type="text"
                  class="form-control"
                  v-model="sales_order.note"
                  :disabled="sales_order.status != 'draft'"
                ></textarea>
              </div>
            </div>
            <p v-else>{{ sales_order.note }}</p>
            <br />
            <b>Requested By:</b>
            <div
              class="input-group"
              v-if="
                sales_order.status == 'draft' ||
                  sales_order.status == 'approval'
              "
            >
              <div class="form-line">
                <textarea
                  type="text"
                  class="form-control"
                  v-model="sales_order.requestor"
                  :disabled="sales_order.status != 'draft'"
                ></textarea>
              </div>
            </div>
            <p v-else>{{ sales_order.requestor }}</p>
            <br />
            <span>Encoded By:{{ sales_order.user.name }}</span>
          </div>

          <!-- AMOUNT -->
          <div class="col-md-6 col-xs-6">
            <div class="table-responsive">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ sales_order.amount.subtotal.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Tax:</th>
                    <td>{{ sales_order.amount.tax.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Shipping:</th>
                    <td>{{ sales_order.amount.shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ sales_order.amount.total.toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
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
                      @keyup="searchClient"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-7">
                <br />
                <button
                  class="btn bg-black waves-effect waves-light"
                  @click="searchClient()"
                >
                  Search
                </button>
                <button
                  class="btn btn-success waves-effect"
                  @click="resetClient()"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <table
                    class="table table-stripped table-condensed table-hover"
                  >
                    <thead>
                      <tr>
                        <th class="text-center">Client</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="client in clients"
                        :key="client.id"
                        style="cursor: pointer"
                        @click="selectClient(client.id)"
                        data-dismiss="modal"
                        aria-label="Close"
                      >
                        <td>{{ client.name }}</td>
                      </tr>
                      <tr v-show="clients.length < 1">
                        <td colspan="5" class="text-center">
                          <small class="col-red">
                            <i>No clients found.</i>
                          </small>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="footer"></div>
        </div>
      </div>
    </div>
    <!-- END CLIENT MODAL-->
    <!-- START ITEM TABLE MODAL -->
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
            <div id="snackbar">Item Added.</div>
            <div class="row clearfix">
              <div class="col-md-4">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      id="search"
                      type="text"
                      class="form-control"
                      v-model="itemSearch.item"
                      autocomplete="off"
                      @keyup="searchItem"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <br />
                <button
                  class="btn btn-sm bg-black waves-effect waves-light"
                  @click="searchItem"
                >
                  Search
                </button>
                <button
                  class="btn btn-sm btn-success waves-effect"
                  @click="resetItem"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <table
                    class="table table-stripped table-condensed table-hover"
                    id="tblSearchItem"
                  >
                    <thead>
                      <tr>
                        <th>Add</th>
                        <th>Name</th>
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
                            @click="addtoOrder(item, 1)"
                          >
                            ADD
                          </button>
                        </td>
                        <td>{{ item.name }}</td>
                        <td>- {{ item.description }}</td>
                        <td>{{ item.id }}</td>
                        <td>{{ item.category.name }}</td>
                      </tr>
                      <tr v-if="items.length == 0">
                        <td colspan="3" class="text-center">
                          No results found.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- END ITEM MODAL -->

    <!-- ADD ITEM GROUP MODAL -->
    <div
      class="modal fade"
      id="itemGroupModal"
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
            <div id="snackbar2">Item Group Added.</div>

            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <table
                    class="table table-stripped table-condensed table-hover"
                    id="tblSearchItemGroup"
                  >
                    <thead>
                      <tr>
                        <th>Add</th>
                        <th>Id</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="item in itemGroups"
                        :key="item.id"
                        style="cursor: pointer"
                      >
                        <td>
                          <button
                            class="btn btn-lg btn-success waves-effect"
                            @click="addtoOrder(item, 2)"
                          >
                            ADD
                          </button>
                        </td>
                        <td>{{ item.id }}</td>

                        <td>{{ item.group_name }}</td>
                      </tr>
                      <tr v-if="item.length == 0">
                        <td colspan="3" class="text-center">
                          No results found.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modalLoading">
      <!-- Place at bottom of page -->
    </div>
  </div>
</template>

<script>
var moment = require("moment");
moment().format();

import swal from "sweetalert";
import PreLoader from "../PreLoader.vue";

export default {
  components: {
    "pre-loader": PreLoader
  },

  data() {
    return {
      sales_order: {
        id: 0,
        orders: [],
        client: {
          name: "",
          location: "",
          contact: "",
          class: ""
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
      item: {
        item: {
          name: "",
          description: "",
          qty: 0
        },
        type: {
          name: ""
        },
        category: {
          name: ""
        },
        warehouse: {
          name: ""
        },
        total_qty: ""
      },
      search: {
        item: "",
        client: "",
        serial: ""
      },
      itemSearch: {
        item: ""
      },
      barcode: {
        serial: ""
      },
      items: [],
      itemGroups: [],
      clients: [],
      authenticatedUser: [],
      roles: [],
      date: ""
    };
  },

  created() {
    this.loadItems();
    this.getGroups();
    this.getClients();
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
  },

  mounted() {},

  watch: {},

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    createDuplicate() {
      var body = $("body");
      console.log(this.$route.params);
      swal("Create a copy?", {
        icon: "warning",
        buttons: {
          exit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "exit":
            body.addClass("loading");
            this.$http
              .post("api/sales_order", this.authenticatedUser)
              .then(response => {
                this.$http
                  .post("api/sales_order/" + this.sales_order.id)
                  .then(response => {
                    this.$global.setSalesOrder(response.body);
                  });
                // this.$router.push({
                //   path: "/sales_order/" + response.body.id
                // });

                window.location.href = "/sales_order/" + response.body.id;
              });
            break;

          default:
            break;
        }
      });
    },

    getGroups() {
      this.$http.post("api/items/showItemGroup").then(response => {
        this.itemGroups = response.body;
        console.log(response.body);
      });
    },
    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },
    load() {
      console.log(this.$route.params);

      this.$http
        .get("api/sales_order/" + this.$route.params.order)
        .then(response => {
          console.log(response.body.client);
          if (response.body.client != null)
            this.sales_order.client = response.body.client;
          this.sales_order.orders = response.body.sales_order_details;
          this.sales_order.user = response.body.user;
          this.sales_order.note = response.body.note;
          this.sales_order.requestor = response.body.requestor;
          this.sales_order.status = response.body.status;
          this.sales_order.created_at = response.body.created_at;
          this.sales_order.remarks = response.body.remarks;
          this.sales_order.id = response.body.id;

          this.compute();
          $(".page-loader-wrapper").fadeOut();
        });
    },

    searchItem() {
      this.$http
        .post("api/sales_order/searchItem", this.itemSearch)
        .then(response => {
          this.items = response.body;
          console.log(response.body);
        });
    },

    // searchItem() {
    //   var filter, table, tr, targetTableColCount;
    //   filter = this.search.item.toUpperCase();
    //   table = document.getElementById("tblSearchItem");
    //   tr = table.getElementsByTagName("tr");
    //   for (var i = 0; i < tr.length; i++) {
    //     var rowData = "";

    //     if (i == 0) {
    //       targetTableColCount = table.rows.item(i).cells.length;
    //       continue; //do not execute further code for header row.
    //     }
    //     for (var colIndex = 0; colIndex < targetTableColCount; colIndex++) {
    //       //console.log(table.rows.item(i).cells.item(colIndex).textContent);
    //       rowData += table.rows.item(i).cells.item(colIndex).textContent;
    //     }

    //     if (rowData.toUpperCase().indexOf(filter) == -1) {
    //       table.rows.item(i).style.display = "none";
    //     } else {
    //       table.rows.item(i).style.display = "table-row";
    //     }
    //   }
    // },
    realtime() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },

    readBarcode() {
      this.$http
        .get("api/items/barcode/" + this.barcode.serial)
        .then(response => {
          this.item = response.body;
        });
    },

    selectClient(id) {
      this.$http.get("api/client/" + id).then(response => {
        this.sales_order.client = response.body;
        // $("#clientModal").modal("hide");
      });
    },

    searchClient() {
      this.$http.post("api/client/search", this.search).then(response => {
        this.clients = response.body;
      });
    },

    addtoOrder(item, flag) {
      var groupItems = [];
      var item_id = 0;
      var item_name = "";
      var item_des = "";
      var item_qty = "";

      if (flag == 1) {
        item_id = item.id;
        item_name = item.name;
        item_des = item.description;
        item_qty = "0";

        this.execute(item, flag, item_id, item_name, item_des, item_qty);
      } else if (flag == 2) {
        var length = item.items.length;
        groupItems = item.items;
        var qty = item.items.qty;

        // console.log(groupItems[0]);
        // console.log(qty);

        for (var index = 0; index < length; index++) {
          this.execute(
            groupItems[index],
            flag,
            groupItems[index].id,
            groupItems[index].name,
            groupItems[index].description,
            groupItems[index].pivot.qty
          );
        }
      }
    },

    execute(item, flag, item_id, item_name, item_des, item_qty) {
      if (!this.item_exist(item, flag)) {
        this.sales_order.orders.push({
          id: item_id,
          name: item_name,
          description: item_des,
          ordered_qty: item_qty,
          delivered_qty: "",
          price: "0",
          amount: "0"
        });
        this.compute();

        if (flag == 1) {
          var x = document.getElementById("snackbar");
          x.className = "show";
          setTimeout(function() {
            x.className = x.className.replace("show", "");
          }, 2000);
        } else var x = document.getElementById("snackbar2");
        x.className = "show";
        setTimeout(function() {
          x.className = x.className.replace("show", "");
        }, 2000);

        // this.itemSearch.item = "";
        // this.searchItem();
      } else {
        swal("That item is already in the list", {
          icon: "error"
        });
      }
    },

    removefromOrder(id) {
      for (var index = 0; index < this.sales_order.orders.length; index++) {
        if (this.sales_order.orders[index].id == id) {
          this.sales_order.orders.splice(index, 1);
        }
      }
    },

    deleteDraft(id) {
      console.log(id);
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data.",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http.delete("api/sales_order/" + id).then(response => {
            console.log(response.body);
            if (response.body == "ok") {
              swal("Draft has been deleted!", {
                icon: "success",
                window: history.back()
              });
            } else {
              swal("Cancelled Deletion");
            }
          });
        }
      });
    },

    saveSalesOrder() {
      this.$http
        .put("api/sales_order/" + this.sales_order.id, this.sales_order)
        .then(response => {
          swal(
            "Materil Request #" +
              this.sales_order.id +
              " was succesfully updated!",
            {
              icon: "success"
            }
          );
        });
    },

    sendforApproval() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/sales_order/" + this.sales_order.id, this.sales_order)
            .then(response => {
              console.log(response.body);
              this.load();
            });

          this.$http
            .post("api/sales_order/submit_approval/" + this.sales_order.id)
            .then(response => {
              console.log(response.body);
              this.load();
              swal("Submitted for approval!", {
                icon: "success"
              });
            });
        }
      });
    },

    accept() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/sales_order/" + this.sales_order.id, this.sales_order)
            .then(response => {});

          this.$http
            .post("api/sales_order/accept/" + this.sales_order.id)
            .then(response => {
              this.load();
              swal("Material Request #" + this.sales_order.id + " accepted!", {
                icon: "success"
              });
            });
        }
      });
    },

    decline() {
      this.$http
        .put("api/sales_order/" + this.sales_order.id, this.sales_order)
        .then(response => {});

      this.$http
        .post("api/sales_order/decline/" + this.sales_order.id)
        .then(response => {
          this.load();
          swal("Material Request #" + this.sales_order.id + " declined!", {
            dangerMode: true
          });
        });
    },

    compute() {
      var sum = 0;

      for (var index = 0; index < this.sales_order.orders.length; index++) {
        sum +=
          this.sales_order.orders[index].price *
          this.sales_order.orders[index].ordered_qty;
      }

      this.sales_order.amount.shipping = 0;
      this.sales_order.amount.tax = 0;
      this.sales_order.amount.subtotal = sum;
      this.sales_order.amount.total = sum;
    },

    resetClient() {
      this.search.client = "";
      this.searchClient();
    },

    resetItem() {
      this.itemSearch.item = "";
      this.searchItem();
    },

    clear() {
      this.sales_order.orders = [];
      this.sales_order.client = [];
      this.sales_order.amount.shipping = 0;
      this.sales_order.amount.tax = 0;
      this.sales_order.amount.subtotal = 0;
      this.sales_order.amount.total = 0;
      this.sales_order.note = "";
      this.sales_order.requestor = "";
      this.showNotification(
        "bg-black",
        "Material Request was cleared!",
        "top",
        "left",
        "animated bounceInLeft",
        "animated fadeOutLeft"
      );
    },
    printPreview() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      //$("#print_form").css("display", "block");
      $("#sales_order_button").css("display", "none");
      $("#item_buttons").css("display", "none");
      $("#sales_return_notification").css("display", "none");
      $("#leftsidebar").css("display", "none");
      $(".navbar").css("display", "none");
      $(".col-md-3").attr("class", "col-md-3 col-xs-3");

      window.print();

      $(".col-md-3 col-xs-3").attr("class", "col-md-3");
      $(".content").css("margin-left", "315px");
      $(".content").css("margin-right", "15px");
      $(".content").css("margin-top", "100px");
      $("#sales_order_button").css("display", "block");
      $("#item_buttons").css("display", "block");
      $("#sales_return_notification").css("display", "block");
      $("#leftsidebar").css("display", "block");
      $(".navbar").css("display", "block");
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

    item_exist(item, flag) {
      let bool = false;

      for (var i = 0; i < this.sales_order.orders.length; i++) {
        if (this.sales_order.orders[i].id == item.id) {
          bool = true;
        }
      }

      return bool;
    },

    index_exist() {
      let index = 0;

      for (var i = 0; i < this.sales_order.orders.length; i++) {
        if (this.sales_order.orders[i].item_id == this.item.item_id) {
          index = i;
        }
      }

      return index;
    },

    serial_exist(index) {
      let bool = false;

      for (
        var i = 0;
        i < this.sales_order.orders[index].ordered_serial.length;
        i++
      ) {
        if (
          this.sales_order.orders[index].ordered_serial[i] == this.item.serial
        ) {
          bool = true;
        }
      }

      return bool;
    },

    item_isEmpty() {
      let bool = false;

      if (this.item.total_qty < 1) bool = true;

      return bool;
    },

    createDeliveryReceipt() {
      var body = $("body");
      swal("Create a Delivery Receipt for SO#" + this.sales_order.id + "?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            body.addClass("loading");
            this.$http
              .post(
                "api/delivery_receipt/create/" + this.sales_order.id,
                this.authenticatedUser
              )
              .then(response => {
                body.removeClass("loading");
                this.$router.push({
                  path: "/delivery_receipt/" + response.body
                });
              });

            break;

          default:
            break;
        }
      });
    },

    showNotification(
      colorName,
      text,
      placementFrom,
      placementAlign,
      animateEnter,
      animateExit
    ) {
      var allowDismiss = false;

      $.notify(
        {
          message: text
        },
        {
          type: colorName,
          allow_dismiss: allowDismiss,
          newest_on_top: true,
          timer: 300,
          placement: {
            from: placementFrom,
            align: placementAlign
          },
          animate: {
            enter: animateEnter,
            exit: animateExit
          },
          template:
            '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' +
            (allowDismiss ? "p-r-35" : "") +
            '" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">Ã—</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            "</div>" +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            "</div>"
        }
      );
    }
  }
};
</script>

<style scoped>
@media screen {
  .watermark {
    display: none;
  }
}

@media print {
  .watermark {
    position: absolute;
    color: lightgray;
    opacity: 0.25;
    font-size: 3em;
    width: 100%;
    top: 8%;
    text-align: center;
    z-index: 0;
    display: block;
  }
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

.table-wrap2 {
  height: 200px;
  border: 1px solid #eee;
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

.modalLoading {
  display: none;
  position: fixed;
  z-index: 1000;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(255, 255, 255, 0.8) url("../../img/loading.gif") 50% 50%
    no-repeat;
}

body.loading .modalLoading {
  overflow: hidden;
}

body.loading .modalLoading {
  display: block;
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
#snackbar2 {
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

#snackbar2.show {
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
