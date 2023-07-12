<template>
  <div class="container-fluid">
    <div class="block-header" style="margin-top:-20px">
      <div class="row clearfix">
        <div class="col-md-12" style="display:block">
          <button
            type="button"
            class="btn btn-default waves-effect"
            v-if="roles.create_purchase_order"
            @click="createPurchaseOrder"
          >
            <i class="material-icons">note_add</i>
            <span>Create New</span>
          </button>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Purchase Orders</h2>
      </div>

      <div class="body">
        <div class="row clearfix" style="height:50px">
          <div style="width:80%">
            <div class="col-md-2">
              <div class="form-group">
                <span>Filter By</span>
                <div class="form-line">
                  <select class="form-control" v-model="search.filter">
                    <option value="number">Purchase No.</option>
                    <option value="supplier">Supplier</option>
                    <option value="date">Date Created</option>
                    <option value="status">Status</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-8" v-if="search.filter == 'date'">
              <div>
                <span>Search</span>
              </div>
              <div class="form-group" style="display:flex;">
                <b-form-datepicker
                  id="datepicker-valid"
                  :state="true"
                  v-model="search.date_from"
                  class="date-range"
                  placeholder="Date From"
                ></b-form-datepicker>
                <b-form-datepicker
                  id="datepicker-valid"
                  :state="true"
                  v-model="search.date_to"
                  class="date-range"
                  placeholder="Date To"
                ></b-form-datepicker>
              </div>
            </div>
            <div class="col-md-4" v-else-if="search.filter == 'supplier'">
              <div class="form-group">
                <div class="form-line">
                  <span>Search</span>
                  <model-list-select
                    class="search-list"
                    :list="suppliers"
                    v-model="search.supplierSelected"
                    option-value="id"
                    option-text="name"
                  >
                  </model-list-select>
                </div>
              </div>
            </div>
            <div class="col-md-4" v-else-if="search.filter == 'number'">
              <div class="form-group">
                <div class="form-line">
                  <span>Search</span>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.number"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-4" v-else-if="search.filter == 'status'">
              <div class="form-group">
                <div class="form-line">
                  <span>Search</span>
                  <select class="form-control" v-model="search.statusSelected">
                    <option value="draft">Draft</option>
                    <option value="approval">For Approval</option>
                    <option value="approved">Order Approved</option>
                    <option value="declined">Declined</option>
                    <option value="on order">On Order</option>
                    <option value="order complete">Order Complete</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <br />
              <button
                class="btn btn-sm bg-black waves-effect waves-light"
                @click="searchText"
              >
                Filter
              </button>
              <button
                class="btn btn-sm btn-success waves-effect"
                @click="resetSearch"
              >
                Reset
              </button>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-10"></div>
          <div class="col-md-2">
            <span>Showing {{ purchase_orders.length }} entries</span>
          </div>
        </div>

        <div class="table-wrap">
          <div class="row clearfix" v-if="showLoading" style="width:100%">
            <td colspan="14" class="text-center">
              <img src="../../img/bars.gif" height="50" />
              <br />
              Fetching list...
            </td>
          </div>
          <table
            class="table table-striped"
            id="itemTable"
            ref="itemTable"
            v-else
          >
            <thead class="thead-dark">
              <tr>
                <th>Purchase Order</th>
                <th>Supplier</th>
                <th>Received</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <router-link
                tag="tr"
                v-for="purchase_order in purchase_orders"
                :key="purchase_order.id"
                :to="'/purchase_order/' + purchase_order.id"
                style="cursor: pointer"
              >
                <td>
                  <b>{{ purchase_order.id }}</b>
                </td>
                <td v-if="purchase_order.supplier">
                  {{ purchase_order.supplier.name }}
                </td>
                <td v-else>
                  <small class="col-red">
                    <i>no supplier selected</i>
                  </small>
                </td>
                <td
                  v-if="
                    purchase_order.total_qty_received > 0 &&
                      purchase_order.total_qty_ordered > 0
                  "
                >
                  {{
                    (purchase_order.total_qty_received /
                      purchase_order.total_qty_ordered) *
                      100
                  }}%
                </td>
                <td v-else>0%</td>
                <td>
                  <small>{{ formatPrice(purchase_order.total) }}</small>
                </td>
                <td class="bg-grey" v-show="purchase_order.status == 'draft'">
                  <span>Draft</span>
                </td>
                <td
                  class="bg-yellow"
                  v-show="purchase_order.status == 'approval'"
                >
                  <span>For Approval</span>
                </td>
                <td
                  class="bg-green"
                  v-show="purchase_order.status == 'approved'"
                >
                  <span>Order Approved</span>
                </td>
                <td class="bg-red" v-show="purchase_order.status == 'declined'">
                  <span>Order Declined</span>
                </td>
                <td
                  class="bg-green"
                  v-show="purchase_order.status == 'on order'"
                >
                  <span>On Order</span>
                </td>
                <td
                  class="bg-green"
                  v-show="purchase_order.status == 'order complete'"
                >
                  <span>Order Fulfilled</span>
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
import { ModelListSelect } from "vue-search-select";
var moment = require("moment");
moment().format();
export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      authenticatedUser: [],
      purchase_orders: [],
      suppliers: [],
      search: {
        filter: "supplier",
        number: "",
        supplierSelected: "",
        statusSelected: "",
        date_from: "",
        date_to: ""
      },
      showLoading: false,
      roles: []
    };
  },

  beforeMount() {},

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.suppliers = this.$global.getSupplier();
    this.roles = this.$global.getRoles();
    this.loadPurchase();
  },

  mounted() {},

  methods: {
    loadPurchase() {
      this.showLoading = true;
      this.$http.get("api/purchase_order").then(response => {
        this.purchase_orders = response.body;
        this.showLoading = false;
      });
    },
    createPurchaseOrder() {
      swal("Create a new purchase order ?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            // this.$http
            //   .post("api/purchase_order", this.authenticatedUser)
            //   .then(response => {
            //     this.$http.get("api/purchase_order").then(response => {
            //       this.$global.setPurchaseOrders(response.body);
            //     });

            //     this.$router.push({
            //       path: "purchase_order/" + response.body.id
            //     });
            //   });

            this.$router.push({
              path: "/new_purchase_order"
            });
            break;

          default:
            break;
        }
      });
    },
    formatPrice(value) {
      var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2
      });
      return formatter.format(value);
    },
    searchText() {
      this.showLoading = true;
      this.$http
        .post("api/purchase_order/search", this.search)
        .then(response => {
          console.log(response.body);
          this.showLoading = false;
          this.purchase_orders = response.body;
        });
    },
    resetSearch() {
      this.search.filter = "number";
      this.search.number = "";
      this.search.supplierSelected = "";
      this.search.statusSelected = "";
      this.search.date_to = "";
      this.search.date_from = "";
      this.purchase_orders = this.$global.getPurchaseOrders();
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

.date-range {
  border: none !important;
  border-bottom: 1px solid black !important;
  box-shadow: none !important;
  width: 50%;
  margin-right: 5px;
  border-radius: 0 0 0 0 !important;
}

.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
</style>
