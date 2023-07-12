<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-md-4">
          <button
            class="btn btn-default waves-effect"
            @click="createSalesReturn"
            :disabled="!roles.create_sales_return"
          >
            <i class="material-icons">note_add</i>
            <span>Create New Sales Return</span>
          </button>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Sales Return</h2>
      </div>
      <div class="body">
        <!-- START SEARCH FORM -->
        <form @submit.prevent="searchSalesReturn">
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
                      <option value="number">ID</option>
                      <option value="client">Client</option>
                      <option value="returnee">Returnee</option>
                      <option value="date">Date Created</option>
                      <option value="status">Status</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6" v-if="search.filter == 'date'">
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
                      @keyup="searchSalesReturnText"
                      placeholder="Type client name"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-4" v-else-if="search.filter == 'returnee'">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      v-model="search.returnee"
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
                      <option value="draft">Draft</option>
                      <option value="approval">For Approval</option>
                      <option value="approved">Order: Ongoing</option>
                      <option value="declined">Order Declined</option>
                      <option value="on order">On Order</option>
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
            <span>Showing {{ sales_returns.length }} entries</span>
          </div>
        </div>
        <!-- END SEARCH FORM -->
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
                  class="table table-striped table-condensed table-hover"
                  id="SalesReturnTable"
                >
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Client From</th>
                      <th>Returnee</th>
                      <th>Status</th>
                      <th>Remarks</th>
                      <th>Date Return</th>
                    </tr>
                  </thead>
                  <tbody>
                    <router-link
                      tag="tr"
                      :to="'/sales_return/' + sales_return.id"
                      v-for="sales_return in sales_returns"
                      :key="sales_return.id"
                      style="cursor: pointer;"
                    >
                      <td>{{ sales_return.id }}</td>
                      <td>{{ sales_return.from_name }}</td>
                      <td>{{ sales_return.returnee }}</td>
                      <td>{{ sales_return.status }}</td>
                      <td>{{ sales_return.remarks }}</td>
                      <td>{{ sales_return.date_return }}</td>
                    </router-link>
                    <tr v-if="sales_returns.length == 0">
                      <td colspan="6" class="text-center">
                        <small class="col-red">
                          <i>No Sales Return found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- END ORDER LIST TABLE-->
            <br />
            <p>{{ sales_returns.length }} transmittal found.</p>
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
      showLoading: false,
      sales_returns: [],
      roles: [],
      authenticatedUser: [],
      search: {
        text: "",
        sort: "1",
        filter: "client",
        number: "",
        returnee: "",
        statusSelected: "",
        date_from: "",
        date_to: ""
      }
    };
  },

  created() {
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
    this.loadReturns();
  },
  methods: {
    loadReturns() {
      this.showLoading = true;
      this.$http.get("api/SalesReturns").then(response => {
        console.log(response.body);
        this.sales_returns = response.body;
        this.showLoading = false;
      });
    },
    searchSalesReturnText() {
      var filter, table, tr, targetTableColCount;
      filter = this.search.text.toUpperCase();
      table = document.getElementById("SalesReturnTable");
      tr = table.getElementsByTagName("tr");
      for (var i = 0; i < tr.length; i++) {
        var rowData = "";

        if (i == 0) {
          targetTableColCount = table.rows.item(i).cells.length;
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
    resetSearch() {
      this.search.text = "";
      this.search.sort = "1";
      this.search.filter = "number";
      this.search.number = "";
      this.search.returnee = "";
      this.search.statusSelected = "";
      this.search.date_from = "";
      this.search.date_to = "";
      this.searchSalesReturnText();
    },
    searchSalesReturn() {
      this.showLoading = true;
      this.$http.post("api/sales_return/search", this.search).then(response => {
        this.showLoading = false;
        this.sales_returns = response.body;
        console.log(response.body);
      });
    },

    createSalesReturn() {
      swal("Create a new sales return?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/sales_return"
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
</style>
