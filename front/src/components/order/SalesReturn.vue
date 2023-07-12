<template>
  <div class="container-fluid">
    <!-- <pre-loader></pre-loader> -->
    <div class="row clearfix">
      <div class="col-lg-12 col-md-12">
        <div class="block-header">
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>

          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            data-target="#itemModal"
            v-show="dataStock.clientFrom != ''"
          >
            <span>Select Product</span>
          </button>

          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            @click="clientModalChange('from')"
            data-target="#clientModal"
          >
            <span>Select Client</span>
          </button>

          <!-- <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            @click="clientModalChange('to')"
            data-target="#clientModal"
          >
            <span>Select To</span>
          </button> -->

          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            @click="clear"
          >
            <span>Clear Data</span>
          </button>
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect"
            @click="saleReturn_submit"
            :disabled="
              !roles.create_sales_return ||
                dataStock.clientFrom.id == null ||
                itemsDis.length == 0
            "
          >
            <span>Submit</span>
          </button>
        </div>
        <div class="card">
          <div class="body">
            <div class="row clearfix">
              <div class="col-md-12 col-sm-12">
                <h4>Sales Return</h4>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-2">
                <b>Date Received:</b>

                <date-picker
                  v-model="dataStock.date_receive"
                  v-validate="'required'"
                  name="date"
                  :config="options"
                  autocomplete="off"
                  style="cursor:pointer;"
                ></date-picker>
                <small class="text-danger pull-left" v-show="errors.has('date')"
                  >Date is required.</small
                >
              </div>

              <div class="col-md-2">
                <b>Returnee:</b>
                <br />
                <input
                  ref="returnee"
                  name="returnee"
                  v-validate="'required'"
                  class="form-control"
                  type="text"
                  style="cursor:pointecr;"
                  v-model="dataStock.returnee"
                />
                <small
                  class="text-danger pull-left"
                  v-show="errors.has('returnee')"
                  >Returnee name is required.</small
                >
              </div>
              <div class="col-md-3">
                <b>From:</b>
                <br />
                <strong>{{ dataStock.clientFrom.name }}</strong>
                <br />
                <span>{{ dataStock.clientFrom.address }}</span>
                <br />
                <span>{{ dataStock.clientFrom.contact }}</span>
              </div>

              <div class="col-md-3">
                <b>To:</b>
                <br />
                <!-- <strong>{{ dataStock.clientTo.name }}</strong>
                <br />
                <span>{{ dataStock.clientTo.address }}</span>
                <br />
                <span>{{ dataStock.clientTo.contact }}</span> -->
                <strong>Logistics</strong>
              </div>
            </div>

            <!-- START Return TABLE -->
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Qty</th>
                          <th>Note</th>
                          <th>Receive To</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in itemsDis" :key="item.index">
                          <td>{{ item.id }}</td>

                          <!-- add this to check serial existence @keyup="check_serial_exist(index)" -->
                          <!-- <small
                              v-show="item.type == 'serial'"
                              id="serial_exist_tip"
                              >Serial is needed.</small
                            > -->

                          <td>{{ item.name }}</td>
                          <td>{{ item.desc }}</td>
                          <td>
                            <input
                              class="form-control"
                              type="text"
                              v-model="item.qty"
                              v-validate="'required|min_value:1|numeric'"
                            />
                          </td>
                          <td>
                            <textarea
                              type="text"
                              class="form-control"
                              v-model="item.note"
                            ></textarea>
                          </td>

                          <td>
                            <select
                              class="form-control"
                              v-model="item.received_to"
                              v-validate="'required'"
                            >
                              <option
                                v-for="warehouse in warehouses"
                                :key="warehouse.id"
                                v-bind:value="warehouse.id"
                                @select="selectWarehouse(warehouse.id)"
                                >{{ warehouse.name }}</option
                              >
                            </select>
                            <p>
                              <small
                                class="text-danger"
                                v-show="errors.has(item.id + 'receive_to')"
                                >Receiving is required</small
                              >
                            </p>
                          </td>
                          <td>
                            <button
                              class="btn btn-danger"
                              @click="removeItemAdded(index)"
                            >
                              Remove
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- END Return TABLE -->

            <!-- REMARKS -->
            <div class="row clearfix">
              <div class="col-md-6">
                <span>Remarks:</span>
                <textarea
                  ref="remarks"
                  name="remarks"
                  type="text"
                  class="form-control"
                  rows="5"
                  v-model="dataStock.remarks"
                ></textarea>
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
                              name="search"
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
                                style="cursor:pointer;"
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
                    <div id="snackbar">Item Added.</div>
                    <div class="row clearfix">
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="form-line">
                            <span>Search</span>
                            <input
                              id="search"
                              v-model="search.item"
                              type="text"
                              class="form-control"
                              autocomplete="off"
                              @keyup="searchItem"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-8" style="display:block">
                        <br />
                        <button
                          class="btn btn-sm bg-black waves-effect waves-light"
                          @click="searchItem()"
                        >
                          Search
                        </button>
                        <button
                          class="btn btn-sm btn-success waves-effect"
                          @click="reset()"
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
                              <th>Select</th>
                              <th>Description</th>
                              <th>Code</th>
                              <th>Category</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in items" :key="item.id">
                              <td>
                                <button
                                  class="btn btn-lg btn-success waves-effect"
                                  @click="selectItem(item)"
                                  style="cursor:pointer;"
                                >
                                  Select
                                </button>
                              </td>
                              <td>{{ item.name }} - {{ item.description }}</td>
                              <td>{{ item.id }}</td>
                              <td>{{ item.category.name }}</td>
                            </tr>
                            <tr v-show="items.length < 1">
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
            <!-- END ITEM MODAL -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var moment = require("moment");
moment().format();

import swal from "sweetalert";
import PreLoader from "../PreLoader.vue";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    datePicker,
    "pre-loader": PreLoader
  },
  data() {
    return {
      suppliers: [],
      items: [],
      itemsDis: [],
      clients: [],
      authenticatedUser: [],
      roles: [],
      warehouses: [],
      fromOrToModal: "",
      search: {
        item: null,
        client: null
      },
      dataStock: {
        id: 0,
        items: {},
        clientFrom: [],
        date_receive: "",
        returnee: "",
        status: "For Approval",
        remarks: "",
        received_to: ""
      },
      data_receives: {
        purchase_order_id: null,
        date_receive: null,
        receives: []
      },
      warehouses: [],
      options: {
        format: "YYYY-MM-DD",
        useCurrent: false
      }
    };
  },

  created() {
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
    this.getClients();
    this.loadItems();
    this.warehouses = this.$global.getWarehouses();
  },

  mounted() {},

  methods: {
    loadItems() {
      this.$http.post("api/items/limited").then(response => {
        this.items = response.body;
      });
    },
    getClients() {
      this.$http.post("api/client/limited").then(response => {
        this.clients = response.body;
      });
    },
    selectItem(item) {
      if (this.checkValueExist(item.id, this.itemsDis)) {
        this.itemsDis.push({
          id: item.id,
          name: item.name,
          desc: item.description,
          qty: "1",
          status: "For Approval",
          note: item.note,
          received_to: null
        });

        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function() {
          x.className = x.className.replace("show", "");
        }, 2000);

        // $("#itemModal").modal("hide");
      } else {
        swal("That item is already in the list", {
          icon: "error"
        });
      }
    },
    checkValueExist(value, arr) {
      var status = true;

      for (var i = 0; i < arr.length; i++) {
        const name = arr[i].id;
        if (name == value) {
          status = false;
          break;
        }
      }

      return status;
    },
    reset() {
      this.search.item = "";
      this.searchItem();
    },
    resetClient() {
      this.search.client = "";
      this.searchClient();
    },
    searchClient() {
      this.$http.post("api/client/search", this.search).then(response => {
        this.clients = response.body;
      });
    },
    searchItem() {
      this.$http
        .post("api/sales_order/searchItem", this.search)
        .then(response => {
          this.items = response.body;
        });
    },
    selectClient(id) {
      this.$http.get("api/client/" + id).then(response => {
        if (this.fromOrToModal == "from") {
          this.dataStock.clientFrom = response.body;
        } else {
          this.dataStock.clientTo = response.body;
        }
        // $("#clientModal").modal("hide");
      });
    },
    selectWarehouse(id) {
      this.$http.get("api/warehouse/" + id).then(response => {
        this.dataStock.received_to = response.body;
      });
    },

    clientModalChange(mode) {
      this.fromOrToModal = mode;
    },

    saleReturn_submit() {
      this.dataStock.items = this.itemsDis;
      this.dataStock.user = this.authenticatedUser;

      this.$validator.validateAll().then(result => {
        if (result) {
          swal("Return this items?", {
            buttons: {
              receive: "Yes",
              cancel: true
            }
          }).then(value => {
            switch (value) {
              case "receive":
                this.dataStock.items = this.itemsDis;
                this.dataStock.user = this.authenticatedUser;
                this.$http
                  .post("api/SalesReturns", this.dataStock)
                  .then(response => {
                    console.log(response.body);

                    // this.clear();
                    // if (response.body == "Data doesn't exist!") {
                    //   swal({
                    //     title: "Error",
                    //     text: response.body,
                    //     icon: "error",
                    //     dangerMode: true
                    //   });
                    // } else {
                    //   swal("Item Returned.", {
                    //     icon: "success"
                    //   });

                    // this.$http.get("api/SalesReturns").then(response => {
                    //   this.$global.setSalesReturn(response.body);
                    // });
                    // this.$http.get("api/items").then(response => {
                    //   this.$global.setItems(response.body);

                    //   this.$router.push({
                    //     path: "/list/sales_return"
                    //   });
                    // });

                    this.$root.$emit("Sidebar");
                    swal("Created Successfully!", {
                      icon: "success"
                    });
                    this.$router.push({
                      path: "/list/sales_return"
                    });
                  });
                break;
              default:
                break;
            }
          });
        }
      });
    },

    check_serial_exist(index) {
      //console.log(this.itemsDis[index].serial);
      if (this.itemsDis[index].serial != "") {
        this.$http
          .get("api/items/serial/checkSerial/" + this.itemsDis[index].serial)
          .then(response => {
            if (response.body) {
              document.getElementById("serial_exist_tip").innerHTML =
                "Serial Available";
              document.getElementById("serial_exist_tip").style.color = "grey";
            } else {
              document.getElementById("serial_exist_tip").innerHTML =
                "Serial not found";
              document.getElementById("serial_exist_tip").style.color = "red";
            }
          });
      }
    },

    removeItemAdded(index) {
      this.itemsDis.splice(index, 1);
    },

    clear() {
      this.itemsDis = [];
      this.dataStock = {
        clientFrom: [],
        clientTo: [],
        date_receive: "",
        returnee: "",
        remarks: ""
      };
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
              path: "/list/sales_return"
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
#Stock_serialCode {
  display: none;
}

#serial_exist_tip {
  font-size: 10px;
  color: red;
}
input {
  width: 150px;
}
#inputQty {
  width: 50px;
}

textarea {
  resize: none;
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
</style>
