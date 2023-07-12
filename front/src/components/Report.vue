<template>
  <div class="container-fluid">
    <pre-loader></pre-loader>

    <!-- Summary reports-->

    <!-- QUICK REPORT -->

    <div class="row clearfix">
      <div class="col-md-12">
        <div class="card">
          <div class="header" style="display:block">
            <h2 style="float:left">Quick Report</h2>

            <div style="float:right">
              <button
                class="btn btn-success waves-effect"
                data-toggle="modal"
                data-target="#summary"
                title="Generate Summary"
              >
                <i class="material-icons">summarize</i>
              </button>
              <button
                type="submit"
                class="btn bg-black waves-effect waves-light"
                title="Print Preview"
                @click.prevent="print"
              >
                <i class="material-icons">print</i>
              </button>
              <button
                type="submit"
                class="btn bg-black waves-effect waves-light"
                title="Export to Excel"
                @click="purchaseExcel('summaryTable')"
              >
                <i class="material-icons">publish</i>
              </button>
            </div>
          </div>

          <!-- modal for create report -->
          <div
            class="modal fade"
            id="summary"
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
                  <form>
                    <div class="row">
                      <div class="col-25">
                        <label class="labelNew">Select Supplier</label>
                      </div>
                      <div class="col-75">
                        <select
                          class="dp"
                          v-model="supplierSelected"
                          @change="getSummary"
                        >
                          <option
                            v-for="supplier in suppliers"
                            :value="supplier.id"
                            :key="supplier.id"
                            :v-model="supplier.name"
                            >{{ supplier.name }}</option
                          >
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-25">
                        <label for="subject">Select Date Range</label>
                      </div>
                      <div class="col-75">
                        <rangedate-picker
                          class="dp"
                          i18n="EN"
                          @selected="onDateSelected"
                        ></rangedate-picker>
                      </div>
                    </div>

                    <br />

                    <div style="float:right">
                      <button
                        class="btn btn-success waves-effect"
                        @click="done"
                        data-dismiss="modal"
                      >
                        Done
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="card" id="printable">
              <div class="header text-center">
                <img src="./../img/logo.jpg" width="150px" />
                <br />``
                <br />
                <h4 style="color:black">
                  Item Received Quick Report
                </h4>

                <h6>
                  As of
                  {{ this.sumDateSelected.end | moment("MMMM Do YYYY") }}
                </h6>
              </div>
              <div class="table-wrap">
                <table
                  class="table table-striped table-condensed"
                  id="summaryTable"
                >
                  <thead>
                    <tr>
                      <th>P.O No.</th>
                      <th>Supplier</th>
                      <th>Total Amount</th>
                      <th>Created Date</th>
                      <th>Requested by</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="report in reports" :key="report.id">
                      <td>
                        <a
                          :href="'/purchase_order/' + report.id"
                          target="_blank"
                          >{{ report.id }}
                        </a>
                      </td>

                      <td>{{ report.created_at }}</td>
                      <td>{{ report.delivery_date }}</td>
                      <td
                        class="bg-yellow"
                        v-show="report.status == 'approval'"
                      >
                        <span>For Approval</span>
                      </td>
                      <td class="bg-green" v-show="report.status == 'approved'">
                        <span>Order Approved</span>
                      </td>
                      <td class="bg-red" v-show="report.status == 'declined'">
                        <span>Order Declined</span>
                      </td>
                      <td class="bg-green" v-show="report.status == 'on order'">
                        <span>On Order</span>
                      </td>
                      <td
                        class="bg-green"
                        v-show="report.status == 'order complete'"
                      >
                        <span>Order Fulfilled</span>
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
</template>

<script>
import PreLoader from "./PreLoader.vue";
import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "pre-loader": PreLoader,
    "rangedate-picker": VueRangedatePicker
  },

  data() {
    return {
      reports: [],
      clients: [],
      suppliers: [],
      filterBy: "supplierSum",
      supplierSelected: null,
      sumDateSelected: {},
      value: "",
      users: [],
      items: [],
      successful_order: [],
      sales_returns: [],

      items: [],
      alerts: [],
      notifications: [],
      roles: [],
      user: [],
      alerts: [],
      purchase_orders: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.load();
    this.roles = this.$global.getRoles();
    this.getClients();
    this.suppliers = this.$global.getSupplier();
    this.purchase_orders = this.$global.getPurchaseOrders();
  },
  mounted() {},

  methods: {
    load() {
      this.$http.post("api/notification/alert").then(response => {
        this.alerts = response.body;
      });

      this.$http.get("api/users/" + this.user.id).then(response => {
        this.$global.setRoles(response.body.roles);
        this.roles = this.$global.getRoles();
        $(".page-loader-wrapper").fadeOut();
      });
    },

    print() {
      this.$htmlToPaper("printable");
    },
    purchaseExcel(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table><tr><th colspan='2' style='font-size: large;'>Item Received Quick Report</th></tr>" +
              "<tr></tr><tr>" +
              "<td>From: " +
              moment(String(this.sumDateSelected.from)).format("MM/DD/YYYY") +
              " To: " +
              moment(String(this.sumDateSelected.to)).format("MM/DD/YYYY") +
              "</td>" +
              "</tr>" +
              "<tr>" +
              "<td>" +
              "</td>" +
              "</tr>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table
            // var tab1 = document.getElementById("summaryTable1");

            // for (j = 0; j < tab1.rows.length; j++) {
            //   tab_text = tab_text + tab1.rows[j].innerHTML + "</tr>";
            // }
            tab_text = tab_text + "<tr></tr> <tr></tr>";
            var j = 0;
            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
              // If Internet Explorer
              txtArea1.document.open("txt/html", "replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus();
              var sa = txtArea1.document.execCommand(
                "SaveAs",
                true,
                "Say Thanks to Sumit.xls"
              );
            } //other browser not tested on IE 11
            else
              var sa = window.open(
                "data:application/vnd.ms-excel," + encodeURIComponent(tab_text)
              );
            return sa;
          }.bind(this),
          1000
        );
      });
    },

    linkGen(pageNum) {
      return pageNum === 1 ? "?" : `?page=${pageNum}`;
    },

    done() {
      $("#summary").modal("hide");
    },

    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },

    // viewDR() {
    //   // let routeData = this.$router.resolve({
    //   //   name: "http://google.com",
    //   //   query: { data: "" }
    //   // });
    //   window.open("http://google.com".href, "_blank");
    // },

    onDateSelected(dateSelected) {
      this.sumDateSelected = dateSelected;
      console.log(this.sumDateSelected.end);
      this.getSummary();
    },

    getSummary() {
      this.sumDateSelected.filterBy = this.filterBy;

      console.log(this.supplierSelected);
      console.log(this.sumDateSelected);

      if (this.sumDateSelected.start != null) {
        if (this.filterBy == "supplierSum" && this.supplierSelected != null) {
          this.sumDateSelected.supplierSelected = this.supplierSelected;
          this.$http
            .post("api/dashboard/showItemInventoryReport", this.sumDateSelected)
            .then(response => {
              console.log(response.body);
              this.reports = response.body;
            });
        }
      }
    }
  }
};
</script>

<style>
* {
  box-sizing: border-box;
}

.input-date {
  display: block;
  border: 1px solid rgb(1, 235, 71) !important;
  padding: 5px;
  font-size: 15px !important;
  width: 100% !important;
  cursor: pointer;
}

select,
textarea {
  width: 100%;
  padding: 5px;
  border: 1px solid rgb(1, 235, 71);
  border-radius: 4px;
  resize: vertical;
}

.labelNew {
  padding: 12px 12px 12px 0;
  display: inline-block;
}
/*
input[type="submit"] {
  background-color: #4caf50;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: right;
}

input[type="submit"]:hover {
  background-color: #45a049;
} */

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}

.col-25 {
  float: left;
  width: 25%;
  margin-top: 6px;
}

.col-75 {
  float: left;
  width: 75%;
  margin-top: 6px;
}

/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-25,
  .col-75,
  input[type="submit"] {
    width: 100%;
    margin-top: 0;
  }
}

.button {
  background-color: #4caf50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  -webkit-transition-duration: 0.4s; /* Safari */
  transition-duration: 0.4s;
}
</style>
