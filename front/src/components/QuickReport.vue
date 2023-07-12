<template>
  <div class="container-fluid">
    <!-- QUICK REPORT -->
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="card">
          <div class="header" style="display:block" id="header">
            <h2 style="float:left">Quick Report</h2>

            <div style="float:right">
              <button
                class="btn waves-effect toggle-btn"
                data-toggle="modal"
                data-target="#summary"
                title="Generate Summary"
              >
                <i class="material-icons">summarize</i>
              </button>

              <button
                type="submit"
                class="btn waves-effect waves-light toggle-btn"
                title="Print Preview"
                @click.prevent="print"
              >
                <i class="material-icons">print</i>
              </button>
              <button
                type="submit"
                class="btn waves-effect waves-light toggle-btn"
                title="Export to Excel"
                @click="deliveryExcel('summaryTable1')"
                v-if="filterBy == 'deliverySum'"
              >
                <i class="material-icons">file_download</i>
              </button>
              <button
                type="submit"
                class="btn waves-effect waves-light toggle-btn"
                title="Export to Excel"
                @click="returnExcel('summaryTable2')"
                v-if="filterBy == 'salesReturn'"
              >
                <i class="material-icons">file_download</i>
              </button>
              <button
                type="submit"
                class="btn waves-effect waves-light toggle-btn"
                title="Export to Excel"
                @click="itemsExcel('summaryTable3')"
                v-if="filterBy == 'items'"
              >
                <i class="material-icons">file_download</i>
              </button>
            </div>
          </div>

          <!-- TEMPLATES -->
          <div style="width:100%">
            <div
              class="modal fade"
              id="summary"
              tabindex="-1"
              role="dialog"
              data-keyboard="false"
            >
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4>GENERATE QUICK REPORT</h4>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                      id="dismiss"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form>
                      <div class="row" style="width:100%;display:flex">
                        <div style="width:20%">
                          <input
                            type="checkbox"
                            id="date_range"
                            class="filled-in chk-col-black"
                            v-model="showRange"
                            @change="showRange != showRange"
                          />
                          <label for="date_range">Select Date Range</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-25">
                          <label class="labelNew">Filter By</label>
                        </div>
                        <div class="col-75">
                          <select
                            v-model="filterBy"
                            style="background:#e4e4e4;"
                          >
                            <option value="purchaseSum">Purchase Orders</option>
                            <option value="deliverySum"
                              >Delivery Receipts</option
                            >
                            <option value="salesReturn">Sales Returns</option>
                            <option value="items">Items</option>
                            <option value="client">Client</option>
                          </select>
                        </div>
                      </div>
                      <div class="row" v-show="this.filterBy == 'items'">
                        <div class="col-25">
                          <label class="labelNew">Select Item</label>
                        </div>
                        <div class="col-75">
                          <div style="width:92%;float:left">
                            <model-list-select
                              :list="items"
                              v-model="itemSelected"
                              option-value="id"
                              option-text="description"
                              :custom-text="getItemDesc"
                              style="background:#e4e4e4;"
                            ></model-list-select>
                          </div>
                          <div style="width:8%;float:left">
                            <button
                              type="button"
                              class="btn btn-success waves-effect"
                              title="Clear Item"
                              @click="resetItem"
                            >
                              <i class="material-icons">backspace</i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row" v-show="this.filterBy == 'purchaseSum'">
                        <div class="col-25">
                          <label class="labelNew">Select Supplier</label>
                        </div>
                        <div class="col-75">
                          <div style="width:92%;float:left">
                            <model-list-select
                              :list="suppliers"
                              v-model="supplierSelected"
                              option-value="id"
                              option-text="name"
                              style="background:#e4e4e4;"
                            ></model-list-select>
                          </div>
                          <div style="width:8%;float:left">
                            <button
                              type="button"
                              class="btn btn-success waves-effect"
                              title="Clear Supplier"
                              @click="resetSupplier"
                            >
                              <i class="material-icons">backspace</i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <div
                        class="row"
                        v-show="
                          this.filterBy == 'deliverySum' ||
                            this.filterBy == 'client' ||
                            this.filterBy == 'salesReturn'
                        "
                      >
                        <div class="col-25">
                          <label class="labelNew">Select Client</label>
                        </div>
                        <div class="col-75">
                          <div style="width:92%;float:left">
                            <model-list-select
                              :list="clients"
                              v-model="clientSelected"
                              option-value="id"
                              option-text="name"
                              style="background:#e4e4e4;"
                            ></model-list-select>
                          </div>
                          <div style="width:8%;float:left">
                            <button
                              type="button"
                              class="btn btn-success waves-effect"
                              title="Clear Supplier"
                              @click="resetClient"
                            >
                              <i class="material-icons">backspace</i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <br />

                      <div class="row dropdown" v-if="showRange == true">
                        <div class="col-25">
                          <label for="subject">Date</label>
                        </div>
                        <div class="col-75">
                          <date-range-picker @update="onDateSelected" />
                        </div>
                      </div>
                      <br />

                      <div>
                        <button
                          type="button"
                          class="btn waves-effect waves-light toggle-btn pull-right"
                          :hidden="showRange == true"
                          @click="getSummary"
                        >
                          Submit
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="height:100%">
              <!-- PURCHASE ORDERS REPORT -->
              <div
                class="card qr-div printable"
                id="printable"
                v-if="filterBy == 'purchaseSum'"
              >
                <div class="header text-center">
                  <img src="./../img/logo.jpg" width="200px" />
                  <br />
                  <br />

                  <h4 style="color:black">
                    PURCHASE ORDERS
                  </h4>
                  <h6>
                    As of
                    {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                  </h6>
                </div>
                <div class="table-wrap">
                  <table
                    class="table table-striped table-condensed"
                    id="summaryTable1"
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
                      <template>
                        <h4>
                          {{ this.sumDateSelected.to | moment("MMMM YYYY") }}
                        </h4>
                      </template>

                      <!-- PURCHASE  -->
                      <tr v-for="report in reports.purchase" :key="report.id">
                        <td>
                          {{ report.id }}
                          <!-- <a
                            :href="'/purchase_order/' + report.id"
                            target="_blank"
                            >{{ report.id }}</a
                          > -->
                        </td>
                        <td>{{ report.supplier.name }}</td>
                        <td>{{ formatPrice(report.total) }}</td>
                        <td>{{ report.created_at }}</td>
                        <td>{{ report.user.name }}</td>
                        <td>{{ report.status }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="footer" id="footer">
                  <label>Encoded By:_________________________</label>
                  <label>Checked By:_________________________</label>
                  <label>Noted By:_________________________</label>
                </div>
              </div>
              <!-- DELIVERY RECEIPTS REPORT -->
              <div
                class="card qr-div printable"
                id="printable"
                v-if="filterBy == 'deliverySum'"
              >
                <div class="header text-center">
                  <img src="./../img/logo.jpg" width="200px" />
                  <br />
                  <br />

                  <h4 style="color:black">
                    FORMS AND DELIVERY RECEIPT - {{ clientName }}
                  </h4>
                  <h6>
                    As of
                    {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                  </h6>
                </div>
                <div class="table-wrap">
                  <table
                    class="table table-striped table-condensed "
                    id="summaryTable1"
                  >
                    <thead>
                      <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Name</th>
                        <!-- <th>Items</th>
                        <th>Qty</th> -->
                        <th>Memo</th>
                        <th>Class</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template>
                        <h4>
                          {{ this.sumDateSelected.to | moment("MMMM YYYY") }}
                        </h4>
                      </template>

                      <!-- DELIVERY RECEIPT  -->
                      <tr v-for="report in reports.delivery" :key="report.id">
                        <td></td>
                        <td>
                          <p>{{ report.created_at }}</p>
                        </td>

                        <td>
                          <!-- {{ report.id }} -->
                          <a
                            :href="'/delivery_receipt/' + report.id"
                            target="_blank"
                          >
                            {{ report.id }}</a
                          >
                        </td>

                        <td>
                          <p>{{ report.sales_order.client.name }}</p>
                        </td>
                        <!-- <td>
                          <ul v-for="item in report.delivery_receipt_details">
                            <li>
                              <p>{{ item.name }} - {{ item.description }}</p>
                            </li>
                          </ul>
                        </td>

                        <td>
                          <ul v-for="item in report.delivery_receipt_details">
                            <li v-text="item.pivot.qty"></li>
                          </ul>

                        </td> -->

                        <td>
                          <p>Note:{{ report.sales_order.note }}</p>
                        </td>
                        <td>{{ report.sales_order.client.class }}</td>
                      </tr>
                      <template> </template>
                    </tbody>
                  </table>
                </div>
                <div class="footer" id="footer">
                  <label>Encoded By:_________________________</label>
                  <label>Checked By:_________________________</label>
                  <label>Noted By:_________________________</label>
                </div>
              </div>
              <!-- SALES RETURN REPORT -->
              <div
                class="card qr-div printable"
                id="printable"
                v-if="filterBy == 'salesReturn'"
              >
                <div class="header text-center">
                  <img src="./../img/logo.jpg" width="200px" />
                  <br />
                  <br />

                  <h4 style="color:black">
                    SALES RETURNS REPORT
                  </h4>
                  <h6>
                    As of
                    {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                  </h6>
                </div>
                <div class="table-wrap">
                  <table
                    class="table table-striped table-condensed"
                    id="summaryTable2"
                  >
                    <thead>
                      <tr>
                        <th></th>

                        <th>ID</th>
                        <th>Date Returned</th>
                        <th>Items</th>
                        <th>Qty</th>
                        <th>Note</th>
                        <!-- <th>Status</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <template>
                        <h4>
                          {{ this.sumDateSelected.to | moment("MMMM YYYY") }}
                        </h4>
                      </template>

                      <!-- SALES RETURN  -->
                      <tr
                        v-for="(report, index) in reports.returns"
                        :key="index"
                        :hidden="filterBy != 'salesReturn'"
                      >
                        <td></td>
                        <td>
                          <a
                            :href="'/sales_return/' + report.id"
                            target="_blank"
                          >
                            {{ report.id }}</a
                          >
                        </td>
                        <td>
                          <p>{{ report.date_return }}</p>
                        </td>
                        <td>
                          <ul v-for="item in report.sales_return_details">
                            <li>
                              <p>{{ item.name }} - {{ item.description }}</p>
                            </li>
                          </ul>
                        </td>
                        <td>
                          <ul v-for="item in report.sales_return_details">
                            <li>
                              <p>{{ item.pivot.qty }}</p>
                            </li>
                          </ul>
                        </td>
                        <td>
                          <ul v-for="item in report.sales_return_details">
                            <li>
                              <p>{{ item.pivot.note }}</p>
                            </li>
                          </ul>
                        </td>
                        <!-- <td>
                          <ul v-for="item in report.sales_return_details">
                            <li>
                              <p>{{ item.pivot.status }}</p>
                            </li>
                          </ul>
                        </td> -->
                      </tr>

                      <template> </template>
                    </tbody>
                  </table>
                </div>
                <div class="footer" id="footer">
                  <label>Encoded By:_________________________</label>
                  <label>Checked By:_________________________</label>
                  <label>Noted By:_________________________</label>
                </div>
              </div>
              <!-- ALL ITEMS REPORT -->
              <div
                class="card qr-div printable"
                id="printable"
                v-if="filterBy == 'items'"
              >
                <div class="header text-center">
                  <img src="./../img/logo.jpg" width="200px" />
                  <br />
                  <br />

                  <h4 style="color:black">
                    PHYSICAL INVENTORY WORKSHEET
                  </h4>
                  <h6>
                    As of
                    {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                  </h6>
                </div>
                <div class="table-wrap">
                  <div
                    class="row clearfix"
                    style="width:100%"
                    v-if="showLoading == '1'"
                  >
                    <td colspan="14" class="text-center">
                      <img src="../img/bars.gif" height="50" />
                      <br />
                      Fetching list...
                    </td>
                  </div>
                  <table
                    class="table table-striped table-condensed"
                    id="summaryTable3"
                    v-else
                  >
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th>Total Qty Received</th>
                        <th>Qty On Hand</th>
                        <th>Qty Out</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template>
                        <h4>
                          {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                        </h4>
                      </template>
                      <tr
                        v-for="(report, index) in reports.items"
                        :key="index"
                        :hidden="filterBy != 'items'"
                      >
                        <td>{{ report.stock_id }}</td>
                        <td>{{ report.stock_desc }}</td>
                        <td style="text-align:left">
                          <span style="float:left;width:100px">{{
                            formatPrice(report.price)
                          }}</span>
                        </td>
                        <td style="text-align:left">
                          <span style="float:left;width:110px">{{
                            report.overall_qty
                          }}</span>
                        </td>
                        <td style="text-align:left">
                          <span style="float:left;width:100px">{{
                            report.stock_qty
                          }}</span>
                        </td>
                        <td
                          style="text-align:left"
                          v-if="report.deduct_qty != null"
                        >
                          <span style="float:left;width:100px">{{
                            report.deduct_qty
                          }}</span>
                        </td>
                        <td style="text-align:left" v-else>0</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="footer" id="footer">
                  <label>Encoded By:_________________________</label>
                  <label>Checked By:_________________________</label>
                  <label>Noted By:_________________________</label>
                </div>
              </div>
              <!-- CLIENT ITEMS -->
              <div
                class="card qr-div printable"
                id="printable"
                v-if="filterBy == 'client'"
              >
                <div class="header text-center">
                  <img src="./../img/logo.jpg" width="200px" />
                  <br />
                  <br />

                  <h4 style="color:black">ITEM REPORT - {{ clientName }}</h4>
                  <h6>
                    As of
                    {{ this.sumDateSelected.to | moment("MMMM Do YYYY") }}
                  </h6>
                </div>
                <div class="table-wrap">
                  <table
                    class="table table-striped table-condensed"
                    id="summaryTable1"
                  >
                    <thead>
                      <tr>
                        <th></th>
                        <th>DR ID</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Memo</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template>
                        <h4>
                          {{ this.sumDateSelected.to | moment("MMMM YYYY") }}
                        </h4>
                      </template>

                      <!-- DELIVERY RECEIPT  -->
                      <tr
                        v-for="(report, index) in reports.clientItems"
                        :key="index"
                      >
                        <td></td>

                        <td>
                          <a
                            :href="'/delivery_receipt/' + report.dr_id"
                            target="_blank"
                          >
                            {{ report.dr_id }}</a
                          >
                        </td>
                        <!-- <td>{{ report.item_id }}</td> -->
                        <td style="max-width:200px">
                          {{ report.item_name }} - {{ report.desc }}
                        </td>
                        <td>{{ report.total_qty }}</td>
                        <td>{{ report.memo }}</td>
                        <td>{{ report.created }}</td>
                      </tr>
                      <template> </template>
                    </tbody>
                  </table>
                </div>
                <div class="footer" id="footer">
                  <label>Encoded By:_________________________</label>
                  <label>Checked By:_________________________</label>
                  <label>Noted By:_________________________</label>
                </div>
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
import { ModelListSelect } from "vue-search-select";
import DateRangePicker from "vue-mj-daterangepicker";
import JsonExcel from "vue-json-excel";
import { ListGroupPlugin } from "bootstrap-vue";
import moment from "moment";

export default {
  components: {
    "pre-loader": PreLoader,
    "rangedate-picker": VueRangedatePicker,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "json-excel": JsonExcel
  },

  data() {
    return {
      loaded: false,
      reports: [],
      clients: [],
      suppliers: [],
      items: [],
      filterBy: null,
      clientSelected: null,
      supplierSelected: null,
      itemSelected: null,
      generate: null,
      sumDateSelected: {},
      value: "",
      users: [],
      successful_order: [],
      sales_returns: [],
      roles: [],
      dataForExcel: [],
      showLoading: "1",
      showRange: false,
      clientName: ""
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.suppliers = this.$global.getSupplier();
    this.loadItems();
    this.getClients();
  },
  mounted() {},

  methods: {
    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },
    getItemDesc(item) {
      return `${item.name} - ${item.description}`;
    },
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    print() {
      this.$htmlToPaper("printable");
      // $(".content").css("margin-left", "0px");
      // $(".content").css("margin-right", "0px");
      // $(".content").css("margin-top", "25px");
      // $("#printable").css("display", "block");
      // $("#leftsidebar").css("display", "none");
      // $("#del-num").css("display", "none");
      // $("#header").css("display", "none");
      // $(".navbar").css("display", "none");
      // $(".col-md-3").attr("class", "col-md-3 col-xs-3");

      // window.print();

      // $(".col-md-3 col-xs-3").attr("class", "col-md-3");
      // $(".content").css("margin-left", "315px");
      // $(".content").css("margin-right", "15px");
      // $(".content").css("margin-top", "100px");
      // $("#printable").css("display", "block");
      // $("#leftsidebar").css("display", "block");
      // $("#header").css("display", "block");
      // $(".navbar").css("display", "block");
    },
    deliveryExcel(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table><tr><th colspan='2' style='font-size: large;'>Forms and Delivery Receipts -   </th></tr>" +
              this.clientName +
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
    returnExcel(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table><tr><th colspan='2' style='font-size: large;'>Sales Returns Report</th></tr>" +
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
            // var tab1 = document.getElementById("summaryTable2");

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
    itemsExcel(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table><tr><th colspan='2' style='font-size: large;'>PHYSICAL INVENTORY WORKSHEET</th></tr>" +
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
            // var tab1 = document.getElementById("summaryTable3");

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
    onDateSelected(values) {
      this.showLoading = "1";
      this.sumDateSelected = values;
      this.getSummary();
    },
    getSummary() {
      //
      if (this.showRange == false) {
        this.sumDateSelected.from = null;
        this.sumDateSelected.end = null;
      }

      this.sumDateSelected.filterBy = this.filterBy;
      this.sumDateSelected.supplierSelected = this.supplierSelected;
      this.sumDateSelected.itemSelected = this.itemSelected;
      this.sumDateSelected.clientSelected = this.clientSelected;

      console.log(this.sumDateSelected);
      this.$http
        .post("api/dashboard/showItemInventoryReport", this.sumDateSelected)
        .then(response => {
          console.log(response.body);
          this.reports = response.body;
          this.clientName = response.body.clientName;
          this.showLoading = "2";
        });
      //
      document.getElementById("dismiss").click();
    },
    resetSupplier() {
      this.supplierSelected = null;
    },
    resetItem() {
      this.itemSelected = null;
    },
    resetClient() {
      this.clientSelected = null;
    },
    formatPrice(value) {
      var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2
      });
      return formatter.format(value);
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
  border-radius: 4px;
  resize: vertical;
}

.labelNew {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

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
.qr-div {
  box-shadow: none !important;
  border: 0;
}

.printable {
  height: 100%;
}

@media screen {
  .footer {
    position: fixed;
    display: none;
    bottom: 0;
    width: 100%;
    color: white;
    float: left;
  }
}
</style>
