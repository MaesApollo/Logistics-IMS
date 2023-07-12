<template>
  <div class="container-fluid">
    <div id="not-printable">
      <div class="block-header" style="margin-top:-20px">
        <div class="row clearfix">
          <div class="col-md-12" style="display:block">
            <router-link
              tag="button"
              class="btn btn-default waves-effect"
              to="/receive_items"
              v-if="roles.create_direct_receive"
            >
              <i class="material-icons">note_add</i>
              <span>New</span>
            </router-link>
          </div>
        </div>
      </div>
      <div class="card" id="receiveCard">
        <div class="header">
          <h2>Manage Direct Receives</h2>
        </div>

        <div class="body">
          <div class="row clearfix" style="height:50px">
            <div style="width:80%">
              <div class="col-md-2">
                <div class="form-group">
                  <span>Filter By</span>
                  <div class="form-line">
                    <select class="form-control" v-model="search.filter">
                      <option value="user">User</option>
                      <option value="supplier">Supplier</option>
                      <option value="date">Date Created</option>
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
              <div class="col-md-4" v-else-if="search.filter == 'user'">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <model-list-select
                      class="search-list"
                      :list="users"
                      v-model="search.userSelected"
                      option-value="id"
                      option-text="name"
                    >
                    </model-list-select>
                  </div>
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
                      @keyup="searchSupplier"
                    >
                    </model-list-select>
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
              <span>Showing {{ receives.length }} entries</span>
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
                  <th>ID</th>
                  <th>User</th>
                  <th>Supplier</th>
                  <th>Total</th>
                  <th>Created At</th>
                  <th>Updated At</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(receive, index) in receives"
                  :key="index"
                  @click="view(index)"
                  style="cursor: pointer"
                  data-toggle="modal"
                  data-target="#reportModal"
                >
                  <td>
                    <b>{{ receive.id }}</b>
                  </td>
                  <td>
                    {{ receive.user.name }}
                  </td>
                  <td>
                    {{ receive.supplier.name }}
                  </td>
                  <td>
                    {{ formatPrice(receive.total) }}
                  </td>
                  <td>
                    {{ receive.created_at }}
                  </td>
                  <td>
                    {{ receive.updated_at }}
                  </td>
                </tr>
                <tr v-show="receives.length == 0">
                  <td colspan="6" class="text-center">No results found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="reportModal" class="modal fade" tabindex="-1">
        <center>
          <div style="width:75%">
            <div class="modal-content">
              <div class="modal-header">
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                  id="receipt-dismiss"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="body">
                  <div class="row clearfix">
                    <div class="text-center">
                      <h3>RECEIVING REPORT</h3>
                    </div>
                  </div>

                  <div
                    class="row clearfix"
                    v-show="roles.update_direct_receive"
                    hidden
                  >
                    <div class="col-md-11" style="text-align:left"></div>
                    <div class="col-md-1" style="text-align:left">
                      <a
                        href="javascript:void(0);"
                        title="Edit Row"
                        @click="receive.saveVisibility = 'show'"
                        v-show="receive.saveVisibility == 'hide'"
                      >
                        <i class="material-icons text-success">mode_edit</i>
                      </a>
                    </div>
                  </div>
                  <div class="row clearfix">
                    <div class="col-md-3 col-sm-12" style="margin-top:-10px">
                      <img src="../../img/logo.jpg" style="width:100%" />
                    </div>
                    <div class="col-md-3" style="text-align:left">
                      <br />
                      <address>
                        <strong>Dctech Microservices, Inc.</strong>
                        <br />Dctech Bldg., C. Bangoy Street <br />Davao City,
                        8000, Philippines
                      </address>
                    </div>
                    <div
                      class="col-md-3"
                      style="text-align:left"
                      v-if="
                        roles.update_direct_receive &&
                          receive.saveVisibility == 'show'
                      "
                    >
                      From:

                      <address style="text-align:left">
                        <div class="form-line">
                          <model-list-select
                            class="search-list"
                            :list="suppliers"
                            option-value="id"
                            option-text="name"
                            v-model="receive.supplier_id"
                            name="client_name"
                            v-validate="'required'"
                            placeholder="Please select supplier .."
                          >
                          </model-list-select>
                        </div>
                        <small
                          class="text-danger pull-left"
                          v-show="errors.has('client_name')"
                          >Supplier is required.</small
                        >
                      </address>
                    </div>
                    <div class="col-md-3" style="text-align:left" v-else>
                      From:

                      <address>
                        <strong>{{ receive.supplier.name }}</strong>
                        <br />
                        <span>Tel #:{{ receive.supplier.contact }}</span>
                        <br />
                        <span>{{ receive.supplier.email }}</span>
                        <br />
                        <span>{{ receive.supplier.address }}</span>
                        <br />
                        <span>Registered TIN:{{ receive.supplier.tin }}</span>
                      </address>
                    </div>
                    <div class="col-md-3">
                      <p style="text-align:left">
                        Receiving Report
                        <input
                          type="text"
                          v-model="receive.report_id"
                          v-if="
                            roles.update_direct_receive &&
                              receive.saveVisibility == 'show'
                          "
                        />
                        <input
                          type="text"
                          v-model="receive.report_id"
                          disabled
                          v-else
                        />
                        <br />
                        Date Received: {{ receive.created_at }}
                      </p>
                    </div>
                  </div>
                  <div class="row clearfix">
                    <div class="col-md-12 col-sm-12">
                      <div class="table-wrap" style="height:auto">
                        <table class="table table-stripped">
                          <thead>
                            <tr>
                              <th>Item</th>
                              <th>Code#</th>
                              <th>Qty</th>
                              <th>Unit Price</th>
                              <th>Amount</th>
                              <th>Receive To</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in receive.item" :key="item.id">
                              <td>{{ item.name }} - {{ item.description }}</td>
                              <td>{{ item.id }}</td>
                              <td>{{ item.pivot.qty }}</td>

                              <td>{{ item.pivot.price }}</td>
                              <td>
                                {{
                                  (item.pivot.price * item.pivot.qty).toFixed(4)
                                }}
                              </td>
                              <td>
                                <!-- {{ item.pivot.warehouse_id }} -->
                                <select
                                  v-model="item.pivot.warehouse_id"
                                  v-bind:name="receive.id + 'warehouse_id'"
                                  disabled
                                >
                                  <option
                                    v-for="warehouse in warehouses"
                                    :key="warehouse.id"
                                    v-bind:value="warehouse.id"
                                  >
                                    {{ warehouse.name }}
                                  </option>
                                </select>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="row clearfix">
                    <!-- NOTES -->
                    <div class="col-md-6 "></div>

                    <!-- AMOUNT -->
                    <div class="col-md-6 col-xs-6">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tbody>
                            <tr>
                              <th>Total:</th>

                              <td>{{ formatPrice(receive.total) }}</td>
                            </tr>
                            <tr>
                              <th>Recipient:</th>
                              <td>
                                <textarea
                                  type="text"
                                  class="form-control"
                                  v-model="receive.class"
                                  v-if="
                                    roles.update_direct_receive &&
                                      receive.saveVisibility == 'show'
                                  "
                                ></textarea>
                                <textarea
                                  type="text"
                                  class="form-control"
                                  v-model="receive.class"
                                  disabled
                                  v-else
                                ></textarea>
                              </td>
                            </tr>
                            <tr>
                              <th>Memo:</th>
                              <td>
                                <textarea
                                  type="text"
                                  class="form-control"
                                  v-model="receive.note"
                                  v-if="
                                    roles.update_direct_receive &&
                                      receive.saveVisibility == 'show'
                                  "
                                ></textarea>
                                <textarea
                                  type="text"
                                  class="form-control"
                                  v-model="receive.note"
                                  v-else
                                  disabled
                                ></textarea>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button
                  class="btn btn-lg btn-dark waves-effect"
                  @click="receive.saveVisibility = 'show'"
                  v-show="
                    roles.update_direct_receive &&
                      receive.saveVisibility == 'hide'
                  "
                >
                  Edit Receipt
                </button>
                <button
                  class="btn btn-lg btn-success waves-effect"
                  @click="
                    receive.saveVisibility = 'show';
                    update(receive.id);
                  "
                  v-show="
                    roles.update_direct_receive &&
                      receive.saveVisibility == 'show'
                  "
                >
                  Save Changes
                </button>
                <button
                  class="btn btn-lg btn-info waves-effect"
                  @click="printPreview"
                >
                  Print Preview
                </button>
              </div>
            </div>
          </div>
        </center>
      </div>
    </div>
    <div id="printable" style="display:none">
      <div class="modal-body">
        <div class="body">
          <div class="row clearfix">
            <div class="text-center">
              <h3>RECEIVING REPORT</h3>
            </div>
          </div>
          <br />

          <div class="row clearfix">
            <div class="col-md-3 col-sm-12" style="margin-top:-10px">
              <img src="../../img/logo.jpg" style="width:100%" />
            </div>
            <div class="col-md-3">
              <br />
              <address style="text-align:left">
                <strong>Dctech Microservices, Inc.</strong>
                <br />Dctech Bldg., C. Bangoy Street <br />Davao City, 8000,
                Philippines
              </address>
            </div>
            <div class="col-md-3" style="text-align:left">
              From:
              <address>
                <strong>{{ receive.supplier.name }}</strong>
                <br />
                <span>Tel #:{{ receive.supplier.contact }}</span>
                <br />
                <span>{{ receive.supplier.email }}</span>
                <br />
                <span>{{ receive.supplier.address }}</span>
                <br />
                <span>Registered TIN:{{ receive.supplier.tin }}</span>
              </address>
            </div>
            <div class="col-md-3">
              <p style="text-align:left">
                Receiving Report: # {{ receive.report_id }}
                <br />
                Date Received: {{ receive.created_at }}
              </p>
            </div>
          </div>
          <div class="row clearfix">
            <div class="col-md-12 col-sm-12">
              <div class="table-wrap" style="height:auto">
                <table class="table table-stripped">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Code#</th>
                      <th>Qty</th>
                      <th>Unit Price</th>
                      <th>Amount</th>
                      <th>Receive To</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in receive.item" :key="item.id">
                      <td>{{ item.name }} - {{ item.description }}</td>
                      <td>{{ item.id }}</td>
                      <td>{{ item.pivot.qty }}</td>
                      <td>{{ item.pivot.price }}</td>
                      <td>
                        {{ (item.pivot.price * item.pivot.qty).toFixed(4) }}
                      </td>
                      <td>
                        <!-- {{ item.pivot.warehouse_id }} -->
                        <select
                          v-model="item.pivot.warehouse_id"
                          v-bind:name="receive.id + 'warehouse_id'"
                          disabled
                        >
                          <option
                            v-for="warehouse in warehouses"
                            :key="warehouse.id"
                            v-bind:value="warehouse.id"
                          >
                            {{ warehouse.name }}
                          </option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row clearfix">
            <!-- NOTES -->
            <div class="col-md-6 "></div>

            <!-- AMOUNT -->
            <div class="col-md-6 col-xs-6">
              <div class="table-responsive">
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th>Total:</th>

                      <td>{{ formatPrice(receive.total) }}</td>
                    </tr>
                    <tr>
                      <th>Recipient:</th>
                      <td>{{ receive.class }}</td>
                    </tr>
                    <tr>
                      <th>Memo:</th>
                      <td>{{ receive.note }}</td>
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
      receives: [],
      roles: [],
      warehouses: [],
      users: [],
      suppliers: [],
      search: {
        filter: "supplier",
        userSelected: "",
        supplierSelected: "",
        date_from: "",
        date_to: ""
      },
      receive: {
        user: [],
        items: [],
        supplier: []
      },
      showLoading: false
    };
  },

  beforeMount() {},

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.warehouses = this.$global.getWarehouses();
    this.users = this.$global.getUsers();
    this.suppliers = this.$global.getSupplier();
    this.loadReceives();
  },

  mounted() {},

  methods: {
    loadReceives() {
      this.showLoading = true;
      this.$http.get("api/direct_receives").then(response => {
        console.log(response.body);
        this.showLoading = false;
        this.receives = response.body;
      });
    },

    searchText() {
      this.showLoading = true;
      this.$http
        .post("api/direct_receives/search", this.search)
        .then(response => {
          console.log(response.body);
          this.showLoading = false;
          this.receives = response.body;
        });
    },
    resetSearch() {
      this.search.filter = "user";
      this.search.userSelected = "";
      this.search.supplierSelected = "";
      this.loadReceives();
    },
    searchSupplier() {
      this.$http.post("api/supplier/search", this.search).then(response => {
        this.suppliers = response.body;
      });
    },
    view(index) {
      this.receive = this.receives[index];
      console.log(this.receive);
    },
    formatPrice(value) {
      var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
        maximumFractionDigits: 5
      });
      return formatter.format(value);
    },
    printPreview() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      $("#leftsidebar").css("display", "none");
      $("#not-printable").css("display", "none");
      $("#printable").css("display", "block");

      $(".navbar").css("display", "none");
      $(".col-md-3").attr("class", "col-md-3 col-xs-3");

      window.print();

      $(".col-md-3 col-xs-3").attr("class", "col-md-3");
      $(".content").css("margin-left", "315px");
      $(".content").css("margin-right", "15px");
      $(".content").css("margin-top", "100px");

      $("#leftsidebar").css("display", "block");
      $("#not-printable").css("display", "block");
      $("#printable").css("display", "none");
      $(".navbar").css("display", "block");
    },
    update(id) {
      this.$http
        .put("api/direct_receives/" + id, this.receive)
        .then(response => {
          console.log(response.body);
          // swal(
          //   "Direct Receive #" + this.receive.id + " was succesfully updated!",
          //   {
          //     icon: "success"
          //   }
          // );
          // document.getElementById("receipt-dismiss").click();
          // this.loadReceives();
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

.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}

.empty-div {
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  letter-spacing: 0.05em;
  flex-direction: column;
  /* height: 100%; */
  position: absolute;
  height: 90%;
  width: 100%;
  opacity: 0.1;
}

.date-range {
  border: none !important;
  border-bottom: 1px solid black !important;
  box-shadow: none !important;
  width: 50%;
  margin-right: 5px;
  border-radius: 0 0 0 0 !important;
}
</style>
