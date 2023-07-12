<template>
  <div class="container-fluid">
    <div class="not-printable" id="not-printable">
      <div class="block-header" id="block-header">
        <div class="row clearfix">
          <div class="col-md-12" style="display:block;margin-top:-20px">
            <button
              class="btn btn-default waves-effect"
              @click="createNewAccountability"
              data-toggle="modal"
              data-target="#modalAddAccountability"
              v-if="roles.create_accountability"
            >
              <i class="material-icons">note_add</i>
              <span>Create New</span>
            </button>
          </div>
        </div>
      </div>
      <div class="card" id="card">
        <div class="header">
          <h2>Manage Accountability</h2>
        </div>

        <div class="body">
          <!-- START SEARCH PANEL -->
          <form @submit.prevent="searchSalesOrder">
            <div class="row clearfix" style="height:50px">
              <div style="width:100%">
                <div class="col-md-1">
                  <div class="form-group">
                    <span>Sort By</span>
                    <div class="form-line">
                      <select class="form-control" v-model="search.sort">
                        <option value="1">Latest</option>
                        <option value="2">Oldest</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <span>Filter By</span>
                    <div class="form-line">
                      <select class="form-control" v-model="search.filter">
                        <option value="number">Request ID</option>
                        <option value="client">Client</option>
                        <option value="date">Date Created</option>
                        <option value="status">Status</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-7" v-if="search.filter == 'date'">
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
                <div class="col-md-4" v-else-if="search.filter == 'client'">
                  <div class="form-group">
                    <div class="form-line">
                      <span>Search</span>

                      <input
                        type="text"
                        class="form-control"
                        autocomplete="off"
                        v-model="search.text"
                        @keyup="searchText"
                        placeholder="Type client name"
                      />
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
                        placeholder="All"
                      />
                    </div>
                  </div>
                </div>
                <div class="col-md-4" v-else-if="search.filter == 'status'">
                  <div class="form-group">
                    <div class="form-line">
                      <span>Search</span>
                      <select
                        class="form-control"
                        v-model="search.statusSelected"
                      >
                        <option value="approval">For Approval</option>
                        <option value="approved">Order: Ongoing</option>
                        <option value="declined">Order Declined</option>
                        <option value="order complete">Order Fulfilled </option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <br />
                  <button class="btn btn-sm bg-black waves-effect waves-light">
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
          </form>
          <div class="row clearfix">
            <div class="col-md-10"></div>
            <div class="col-md-2">
              <span>Showing {{ accountabilities.length }} entries</span>
            </div>
          </div>
          <!-- END SEARCH PANEL -->
          <div class="row clearfix">
            <div class="col-md-12">
              <!-- START ORDER LIST TABLE -->
              <div class="table-wrap">
                <div class="row clearfix" v-if="showLoading" style="width:100%">
                  <td colspan="14" class="text-center">
                    <img src="../../img/bars.gif" height="50" />
                    <br />
                    Fetching list...
                  </td>
                </div>
                <div class="table-responsive" v-else>
                  <table
                    class="table table-striped table-hover"
                    id="SalesOrderTable"
                    ref="SalesOrderTable"
                  >
                    <thead class="thead-dark">
                      <tr>
                        <th>ID</th>
                        <th>Accountable</th>
                        <th>Designation</th>
                        <th>Area</th>
                        <th>Date Released</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="accountable in accountabilities"
                        :key="accountable.id"
                        style="cursor: pointer;"
                        @click="getIndex(accountable)"
                        data-toggle="modal"
                        data-target="#modalAddAccountability"
                      >
                        <td>
                          <b>{{ accountable.id }}</b>
                        </td>
                        <td>{{ accountable.accountable }}</td>
                        <td>{{ accountable.department }}</td>
                        <td>{{ accountable.area }}</td>
                        <td>
                          {{
                            accountable.date_accounted | moment("MMMM D YYYY")
                          }}
                        </td>
                      </tr>
                      <!-- </router-link> -->
                      <tr v-show="accountabilities.length == 0">
                        <td colspan="7" class="text-center">
                          <small class="col-red">
                            <i>No accountability found.</i>
                          </small>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- END ORDER LIST TABLE-->
              <br />
              <p>{{ accountabilities.length }} accountability found.</p>
            </div>
          </div>
        </div>
      </div>
      <!-- create material request -->
      <div
        id="modalAddAccountability"
        class="modal fade"
        tabindex="-1"
        style="margin-top:-20px;display:none"
      >
        <center>
          <div style="width:75%">
            <div class="modal-content">
              <div class="modal-header">
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                  id="request-dismiss"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div id="snackbar">Item Added.</div>
                <div class="body body-material">
                  <div id="print-body">
                    <div class="row clearfix">
                      <div class="col-md-6 col-sm-6">
                        <h4 style="text-align:left">ACCOUNTABILITY</h4>
                      </div>
                    </div>
                    <br />
                    <div class="row clearfix">
                      <div class="col-md-3 col-sm-12" style="margin-top:-10px">
                        <img
                          src="../../img/new_logo.jpg"
                          style="width:100%"
                          class="logo"
                        />
                      </div>

                      <div class="col-md-3">
                        <br />
                        <address style="text-align:left">
                          <strong>Dctech Microservices, Inc.</strong>
                          <br />Dctech Bldg., C. Bangoy Street <br />Davao City,
                          8000, Philippines
                        </address>
                      </div>

                      <div class="col-md-3">
                        <br />
                        <address style="text-align:left">
                          Employee: <strong>{{ request.accountable }}</strong>
                          <br />
                          <span>Area: {{ request.area }}</span>
                          <br />
                          <span>Department: {{ request.department }}</span>
                        </address>
                      </div>
                      <div class="col-md-3">
                        <br />
                        <p style="text-align:left">
                          Reference
                          <b># {{ request.id }}</b>
                          <br />
                          Date Released:
                          {{ request.date_accounted | moment("MMMM D YYYY") }}
                        </p>
                      </div>
                    </div>
                    <br />
                    <br />
                    <div class="row clearfix" v-if="request.status == 'draft'">
                      <div class="col-md-1">
                        <p style="text-align:left;margin-top:15px">
                          Accountable:
                        </p>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="form-line" style="margin-left:-15px">
                            <input
                              type="text"
                              class="form-control"
                              autocomplete="off"
                              v-model="request.accountable"
                              placeholder="Type employee name"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2"></div>
                      <div class="col-md-1">
                        <p style="text-align:left;margin-top:15px">
                          Location:
                        </p>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="form-line" style="margin-left:-15px">
                            <input
                              type="text"
                              class="form-control"
                              autocomplete="off"
                              v-model="request.area"
                              placeholder="Type employee area/location"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row clearfix" v-if="request.status == 'draft'">
                      <div class="col-md-1">
                        <p style="text-align:left;margin-top:15px">
                          Designation:
                        </p>
                      </div>
                      <div class="col-md-4" v-if="request.status == 'draft'">
                        <div class="form-group">
                          <div class="form-line" style="margin-left:-15px">
                            <input
                              type="text"
                              class="form-control"
                              autocomplete="off"
                              v-model="request.department"
                              placeholder="Type employee designation/department"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2"></div>
                      <div class="col-md-5" v-if="request.status == 'draft'">
                        <div class="form-line" style="width:92%;float:left">
                          <model-list-select
                            style="float:right"
                            class="search-list"
                            :list="items"
                            option-value="id"
                            option-text="description"
                            :custom-text="getItemDesc"
                            v-model="item"
                            placeholder="Please select items.."
                          >
                          </model-list-select>
                        </div>
                        <div style="width:8%;float:right">
                          <button
                            class="btn btn-success waves-effect"
                            title="Add this item"
                            @click="selectItem(item)"
                          >
                            <i class="material-icons">note_add</i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <br />
                    <br />
                    <div class="row clearfix">
                      <div class="col-md-12 col-sm-12">
                        <div class="table-wrap" style="height:auto;">
                          <table class="table table-stripped">
                            <thead>
                              <tr>
                                <th>Quantity</th>
                                <th>Description</th>
                                <th>Remarks</th>
                              </tr>
                            </thead>
                            <tbody v-if="this.request.status == 'draft'">
                              <tr
                                v-for="order in request.item_accountability_details"
                                :key="order.id"
                              >
                                <td>
                                  <input
                                    type="text"
                                    v-model="order.qty"
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
                                <td>{{ order.description }}</td>
                                <td>
                                  <input
                                    type="text"
                                    v-model="order.remarks"
                                    v-validate="'required'"
                                    v-bind:name="order.id + 'remarks'"
                                  />
                                  <p>
                                    <small
                                      class="text-danger"
                                      v-show="errors.has(order.id + 'remarks')"
                                      >Remarks is required</small
                                    >
                                  </p>
                                </td>

                                <td>
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
                            <tbody v-else>
                              <tr
                                v-for="order in request.item_accountability_details"
                                :key="order.id"
                              >
                                <td>{{ order.pivot.qty }}</td>
                                <td>{{ order.description }}</td>
                                <td>{{ order.pivot.remarks }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                    <div
                      class="row clearfix"
                      style="margin-top:35px;margin-left:15px;margin-right:15px"
                    >
                      <center>
                        <p class="text-justify lh-sm">
                          I acknowledge receipt of the
                          <strong>Items(s)</strong> specified above and accept
                          full responsibility. <br />
                          <br />
                          I am fully aware that the accountability and its
                          proper maintenance is my responsibility and that I may
                          be called upon to explain for the defects; loss or
                          impairment of any kind. In case impairment is due to
                          my fault or negligence,<strong>
                            DCTECH MICRO SERVICES, INC
                          </strong>
                          is authorized to take necessary steps to recover
                          damages through payroll deductions or other means.
                          <br />
                          <br />

                          I understand that, if due to transfer, resignation or
                          termination, I am required to turnover or surrender
                          the above mentioned item to the Logistics - Head
                          Office.
                        </p>
                      </center>
                    </div>
                    <br />
                    <div class="row clearfix signatories">
                      <div class="row clearfix">
                        <div class="col-md-1"></div>
                        <div class="col-md-3">
                          <br />
                          <address style="text-align:left">
                            Accountable:<br />
                            <center>
                              ___________________________<br />
                              Signature Over Printed Name
                            </center>
                          </address>
                        </div>
                        <div class="col-md-1"></div>

                        <div class="col-md-3">
                          <br />
                          <address style="text-align:left">
                            Noted By: <br />
                            <center>
                              Ailyn T. Marimon <br />
                              Immediate Supervisor/ Department Head
                            </center>
                          </address>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-3">
                          <br />
                          <address style="text-align:left">
                            Released By: <br />
                            <center>
                              {{ released_by }} <br />
                              Logistics
                            </center>
                          </address>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button
                      class="btn btn-lg btn-success waves-effect"
                      v-if="request.status == 'draft'"
                      @click="save"
                    >
                      Save
                    </button>
                    <button
                      v-else
                      class="btn btn-lg btn-info waves-effect preview"
                      @click="printPreview"
                    >
                      Print Preview
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </center>
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
      accountabilities: [],
      roles: [],
      authenticatedUser: [],
      items: [],
      showLoading: false,
      search: {
        client: "",
        text: "",
        sort: "1",
        filter: "client",
        number: "",
        clientSelected: "",
        statusSelected: "",
        date_from: "",
        date_to: ""
      },
      request: {
        id: "",
        item_accountability_details: [],
        accountable: "",
        area: "",
        department: "",
        date_accounted: "",
        remarks: "",
        status: "",
        released_by: {
          name: ""
        }
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

      selected_item_index: 0,
      item_selected: {
        id: "",
        name: "",
        description: "",
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
        remarks: "",
        diff: 0
      },
      released_by: ""
    };
  },

  created() {
    this.getAccountabilities();
    this.loadItems();
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
  },

  mounted() {},

  methods: {
    getAccountabilities() {
      this.showLoading = true;
      this.$http.get("api/accountability").then(response => {
        // console.log(response.body);
        this.showLoading = false;
        this.accountabilities = response.body;
      });
    },
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    searchSalesOrder() {
      this.showLoading = true;
      this.$http.post("api/sales_order/search", this.search).then(response => {
        this.showLoading = false;
        this.accountabilities = response.body;
        console.log(response.body);
      });
    },
    searchText() {
      var filter, table, tr, targetTableColCount;
      filter = this.search.text.toUpperCase();
      table = document.getElementById("SalesOrderTable");
      tr = table.getElementsByTagName("tr");

      for (var i = 0; i < tr.length - 1; i++) {
        var rowData = "";

        if (i == 0) {
          targetTableColCount = 9; //table.rows.item(i).cells.length;

          continue; //do not execute further code for header row.
        }
        for (var colIndex = 0; colIndex < targetTableColCount; colIndex++) {
          //console.log(table.rows.item(i).cells.item(colIndex).textContent);
          rowData += table.rows.item(i).cells.item(colIndex).textContent;
        }

        if (rowData.toUpperCase().indexOf(filter) == -1) {
          table.rows.item(i).style.display = "none";
        } else {
          table.rows.item(i).style.display = "table-row";
        }
      }
    },
    searchClient() {
      this.$http.post("api/client/search", this.search).then(response => {
        this.clients = response.body;
      });
    },
    resetSearch() {
      this.search.sort = "1";
      this.search.filter = "number";
      this.search.number = "";
      this.search.clientSelected = "";
      this.search.statusSelected = "";
      this.search.date_from = "";
      this.search.date_to = "";
      this.searchSalesOrder();
    },
    createNewAccountability() {
      this.request.item_accountability_details = [];
      this.request.accountable = "";
      this.request.status = "draft";
      this.request.area = "";
      this.request.department = "";
      this.request.date_accounted = "";
      this.request.remarks = "";
      this.request.released_by = [];
      this.request.id = "";
    },
    selectItem(item) {
      console.log(item);
      var item_id = 0;
      var item_des = "";
      var item_qty = "";
      var item_remarks = "";

      item_id = item.id;
      item_des = item.description;
      item_qty = "0";
      item_remarks = "";

      this.execute(item, item_id, item_des, item_qty, item_remarks);
    },
    execute(item, item_id, item_des, item_qty, item_remarks) {
      if (!this.item_exist(item)) {
        this.request.item_accountability_details.push({
          id: item_id,
          description: item_des,
          qty: item_qty,
          remarks: item_remarks
        });
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function() {
          x.className = x.className.replace("show", "");
        }, 2000);
      } else {
        swal("That item is already in the list", {
          icon: "error"
        });
      }
    },
    item_exist(item) {
      let bool = false;

      for (
        var i = 0;
        i < this.request.item_accountability_details.length;
        i++
      ) {
        if (this.request.item_accountability_details[i].id == item.id) {
          bool = true;
        }
      }

      return bool;
    },
    removefromOrder(id) {
      for (
        var index = 0;
        index < this.request.item_accountability_details.length;
        index++
      ) {
        if (this.request.item_accountability_details[index].id == id) {
          this.request.item_accountability_details.splice(index, 1);
        }
      }
    },

    getIndex(item) {
      console.log(item);
      this.request.item_accountability_details =
        item.item_accountability_details;
      this.request.accountable = item.accountable;
      this.request.status = item.status;
      this.request.area = item.area;
      this.request.department = item.department;
      this.request.date_accounted = item.date_accounted;
      this.request.remarks = item.remarks;
      this.released_by = item.released_by.name;
      this.request.id = item.id;
    },

    getItemDesc(item) {
      return `${item.name} - ${item.description}`;
    },

    printPreview() {
      this.$htmlToPaper("print-body");
      // $(".content").css("margin-left", "0px");
      // $(".content").css("margin-right", "0px");
      // $(".content").css("margin-top", "25px");
      // $("#leftsidebar").css("display", "none");
      // $("#block-header").css("display", "none");
      // $(".navbar").css("display", "none");
      // $(".col-md-3").attr("class", "col-md-3 col-xs-3");

      // window.print();

      // $(".col-md-3 col-xs-3").attr("class", "col-md-3");
      // $(".content").css("margin-left", "315px");
      // $(".content").css("margin-right", "15px");
      // $(".content").css("margin-top", "100px");
      // $("#leftsidebar").css("display", "block");
      // $("#block-header").css("display", "block");
      // $(".navbar").css("display", "block");
    },

    save() {
      this.request.released_by = this.authenticatedUser.id;
      console.log(this.request);

      if (this.request.item_accountability_details.length > 0) {
        console.log("not empty");

        this.$validator.validateAll().then(result => {
          if (result) {
            this.$http
              .post("api/accountability/submit_accountable", this.request)
              .then(response => {
                console.log(response.body);

                swal("Accountability saved!", {
                  icon: "success"
                });

                document.getElementById("request-dismiss").click();
                this.getAccountabilities();
              });
          }
        });
      } else
        swal("Orders cannot be Empty!", {
          icon: "error"
        });
    }
  }
};
</script>

<style scoped>
.alert-default {
  background-color: #d3d3d3;
  padding: 10px;
}
.status_panel {
  float: right;
}
.itemButtons {
  float: left;
}
.ready {
  font-size: 20px;
}

.modal {
  margin-top: 80px;
}

.table-wrap {
  height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
  border: 1px solid #eee;
}
.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
.date-range {
  border: none !important;
  border-bottom: 1px solid black !important;
  box-shadow: none !important;
  width: 50%;
  margin-right: 5px;
  border-radius: 0 0 0 0 !important;
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

.alert {
  border-radius: 4px;
  padding: 10px;
}

.alert-warning {
  background-color: #636363 !important;
}

.alert-approval {
  background-color: #e2ac08;
}
.alert-default {
  background-color: gray;
}

@media screen {
  .not-printable {
    display: block;
  }
  .printable {
    display: none;
  }
}

@media print {
  .not-printable {
    display: none;
  }
  .logo {
    width: 25%;
  }

  .signatories {
    margin-top: 100px;
    margin-left: 15px;
  }

  .preview {
    visibility: hidden;
  }
}
.signatories {
  margin-top: 100px;
}
#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 1s, fadeout 1s 1s;
  animation: fadein 1s, fadeout 1s 1s;
}
#snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -100px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 50%;
  top: 100px;
  font-size: 17px;
}
</style>
