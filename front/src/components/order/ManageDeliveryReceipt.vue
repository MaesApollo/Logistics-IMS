<template>
  <div class="container-fluid">
    <div class="card not-printable" id="not-printable">
      <div class="header">
        <h2>Manage Delivery Receipts</h2>
      </div>
      <div class="body">
        <form @submit.prevent="searchDelivery">
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
                      <option value="number">Delivery Receipt</option>
                      <option value="sales_no">Sales Order</option>
                      <option value="client">Client</option>
                      <option value="date">Date Created</option>
                      <option value="status">Status</option>
                      <option value="note">Note/Serial</option>
                      <option value="memo">Memo</option>
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
              <div class="col-md-4" v-else-if="search.filter == 'sales_no'">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      v-model="search.sales_no"
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
                      <option value="for delivery">For Delivery</option>
                      <option value="delivering">On Shipped</option>
                      <option value="delivered">Delivered</option>
                      <option selected>All</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4" v-else-if="search.filter == 'note'">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      v-model="search.note"
                      placeholder="Type note or serial"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-4" v-else-if="search.filter == 'memo'">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      v-model="search.memo"
                      placeholder="Type memo"
                    />
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
            <span>Showing {{ delivery_receipts.length }} entries</span>
          </div>
        </div>
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
                  id="itemTable"
                  ref="itemTable"
                >
                  <thead>
                    <tr>
                      <th>Delivery Receipt #</th>
                      <th>Client</th>
                      <th>Sales Order #</th>
                      <th>Status</th>
                      <th>Last Date Updated</th>
                      <th>Date Created</th>
                      <th>Created by</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- <router-link
                      tag="tr"
                      :to="'/delivery_receipt/' + delivery_receipt.id"
                      v-for="delivery_receipt in delivery_receipts"
                      :key="delivery_receipt.id"
                      style="cursor: pointer;"
                    > -->
                    <tr
                      v-for="delivery_receipt in delivery_receipts"
                      :key="delivery_receipt.id"
                      style="cursor: pointer;"
                      @click="getIndex(delivery_receipt.id)"
                      data-toggle="modal"
                      data-target="#modalDeliveryReceipt"
                    >
                      <td>{{ delivery_receipt.id }}</td>
                      <td>
                        <span v-if="delivery_receipt.client.length != 0">{{
                          delivery_receipt.client.name
                        }}</span>
                        <span v-else>
                          <small class="col-red">
                            <i>no client selected</i>
                          </small>
                        </span>
                      </td>
                      <td>{{ delivery_receipt.sales_order_id }}</td>
                      <td
                        class="bg-grey"
                        v-show="delivery_receipt.status == 'for delivery'"
                      >
                        <span>Order: For Delivery</span>
                      </td>
                      <td
                        class="bg-green"
                        v-show="delivery_receipt.status == 'delivering'"
                      >
                        <span>Order: On Shipped</span>
                      </td>
                      <td
                        class="bg-blue"
                        v-show="delivery_receipt.status == 'delivered'"
                      >
                        <span>Order: Delivered</span>
                      </td>
                      <td>{{ delivery_receipt.updated_at }}</td>
                      <td>{{ delivery_receipt.created_at }}</td>
                      <td>{{ delivery_receipt.user.name }}</td>
                    </tr>

                    <!-- </router-link> -->
                    <tr v-show="delivery_receipts.length == 0">
                      <td colspan="7" class="text-center">
                        <small class="col-red">
                          <i>No receipts found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- END ORDER LIST TABLE-->
            <br />
            <p>{{ delivery_receipts.length }} receipts displayed.</p>
          </div>
        </div>
      </div>

      <!-- delivery modal -->
      <div
        id="modalDeliveryReceipt"
        class="modal fade"
        tabindex="-1"
        style="margin-top:-20px"
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
                  id="delivery-dismiss"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body delivery-body">
                <div class="body">
                  <div class="row clearfix">
                    <div class="col-md-6 col-sm-6">
                      <h4>DELIVERY RECEIPT</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
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
                  <br />
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
                    <div style="width:25%;text-align:left">
                      From
                      <br />
                      <address>
                        <strong>Dctech Microservices, Inc.</strong>
                        <br />Dctech Building, Ponciano Reyes Street <br />Davao
                        City, 8000, Philippines
                      </address>
                    </div>
                    <div style="width:25%;text-align:left">
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
                    <div style="width:20%;text-align:left">
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
                  <br />
                  <!-- START ORDER TABLE -->
                  <div class="row clearfix">
                    <div class="col-md-12 col-xs-12">
                      <div class="table-wrap" style="height:auto">
                        <div class="table-responsive">
                          <table
                            class="table table-striped table-condensed"
                            style="height:auto"
                          >
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <!-- <th>Qty Return</th> -->
                                <th>Note</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody
                              v-if="this.delivery_receipt.orders.length == 0"
                            >
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
                                v-for="(order,
                                index) in delivery_receipt.orders"
                                :key="index"
                              >
                                <td>{{ order.id }}</td>
                                <td>{{ order.name }}</td>
                                <td>{{ order.description }}</td>

                                <!-- <td v-if="order.type.name == 'Serialize'">
                                  <span
                                    v-for="(serial,
                                    index) in order.ordered_serial.slice(0, 5)"
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
                                <td v-else>
                                  {{ order.ordered_serial.length }}
                                </td>
                                <!-- <td>
                                  {{ order.return_qty }}
                                </td> -->
                                <td v-if="order.saveVisibility == 'hide'">
                                  {{ order.note }}
                                </td>
                                <td v-else>
                                  <textarea v-model="order.note"></textarea>
                                </td>

                                <td>{{ order.price }}</td>
                                <td v-if="order.type.name == 'Consumable'">
                                  {{ order.price * order.delivering_qty }}
                                </td>
                                <td v-else>
                                  {{
                                    order.price * order.ordered_serial.length
                                  }}
                                </td>
                                <td>
                                  <a
                                    href="javascript:void(0);"
                                    title="Edit Row"
                                    @click="order.saveVisibility = 'show'"
                                    v-show="order.saveVisibility == 'hide'"
                                  >
                                    <i
                                      class="material-icons text-success"
                                      style="font-size: 16px !important"
                                      >mode_edit</i
                                    >
                                  </a>
                                  <a
                                    href="javascript:void(0);"
                                    title="Save Row"
                                    @click="
                                      order.saveVisibility = 'hide';
                                      editRow(order, delivery_receipt.id);
                                    "
                                    v-show="order.saveVisibility == 'show'"
                                  >
                                    <i
                                      class="material-icons text-success"
                                      style="font-size: 16px !important"
                                      >check</i
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

                  <div class="signatories">
                    <div class="row clearfix">
                      <!-- REMARKS -->
                      <div class="col-md-6 col-xs-6">
                        <div class="row clearfix">
                          <div
                            class="col-md-8 col-xs-8"
                            style="text-align:left"
                          >
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
                                <td>
                                  {{
                                    delivery_receipt.amount.subtotal.toFixed(2)
                                  }}
                                </td>
                              </tr>
                              <tr>
                                <th>Tax (0%):</th>
                                <td>
                                  {{ delivery_receipt.amount.tax.toFixed(2) }}
                                </td>
                              </tr>
                              <tr>
                                <th>Shipping:</th>
                                <td>
                                  {{
                                    delivery_receipt.amount.shipping.toFixed(2)
                                  }}
                                </td>
                              </tr>
                              <tr>
                                <th>Total:</th>
                                <td>
                                  {{ delivery_receipt.amount.total.toFixed(2) }}
                                </td>
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
                            >RECEIVED ABOVE ITEMS IN GOOD ORDER AND
                            CONDITION</em
                          ></strong
                        >
                      </center>
                    </div>
                    <hr />
                    <br />

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
                        <b
                          v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
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
              <div class="modal-footer" v-show="roles.create_delivery_receipt">
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
    <!-- print area -->
    <div class="printable">
      <center>
        <div style="width:85%">
          <div class="body delivery-body">
            <div class="row clearfix">
              <h4>DELIVERY RECEIPT</h4>
            </div>
            <br />
            <div class="row clearfix">
              <div
                class="col-md-3 "
                style="margin-top:-10px;"
                v-if="
                  sales_order.client.class == 'INET CLIENTS' ||
                    sales_order.client.class == 'DCTECH'
                "
              >
                <img src="../../img/new_email.gif" width="100%" />
              </div>
              <div
                class="col-md-3 "
                style="margin-top:-10px;"
                v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'"
              >
                <img src="../../img/soln.gif" width="100%" />
              </div>
              <div class="col-md-3 " style="text-align:left">
                From
                <br />
                <address>
                  <strong>Dctech Microservices, Inc.</strong>
                  <br />Dctech Building, Ponciano Reyes Street <br />Davao City,
                  8000, Philippines
                </address>
              </div>
              <div class="col-md-3 " style="text-align:left">
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
              <div class="col-md-3 " style="text-align:left">
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
            <br />
            <!-- START ORDER TABLE -->
            <div class="row clearfix">
              <div class="col-md-12 col-xs-12">
                <div class="table-wrap" style="height:auto;min-height:40vh">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed"
                      style="height:auto"
                    >
                      <thead>
                        <tr>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Qty</th>
                          <!-- <th>Qty Return</th> -->
                          <th>Note</th>
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
                          <!-- <td v-if="order.type.name == 'Serialize'">
                            <span
                              v-for="(serial,
                              index) in order.ordered_serial.slice(0, 5)"
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
                          <td v-else>
                            {{ order.ordered_serial.length }}
                          </td>
                          <!-- <td>
                            {{ order.return_qty }}
                          </td> -->
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
                    <div class="col-md-8 col-xs-8" style="text-align:left">
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
                          <td>
                            {{ delivery_receipt.amount.subtotal.toFixed(2) }}
                          </td>
                        </tr>
                        <tr>
                          <th>Tax (0%):</th>
                          <td>
                            {{ delivery_receipt.amount.tax.toFixed(2) }}
                          </td>
                        </tr>
                        <tr>
                          <th>Shipping:</th>
                          <td>
                            {{ delivery_receipt.amount.shipping.toFixed(2) }}
                          </td>
                        </tr>
                        <tr>
                          <th>Total:</th>
                          <td>
                            {{ delivery_receipt.amount.total.toFixed(2) }}
                          </td>
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
              <br />
              <br />
              <div class="row clearfix">
                <div class="col-xs-2">
                  <label>Encoded By:</label>
                  <br />
                  <br />
                  <br />

                  <label>Checked By:</label>
                </div>
                <div class="col-xs-4">
                  <label>{{ delivery_receipt.user.name }}</label>
                  <br />
                  <br />
                  <br />
                  <label>Alvin Jay P. Angcon</label>
                </div>
                <div class="col-xs-2">
                  <label>Released By:</label>
                  <br />
                  <br />
                  <br />
                  <label>Received By:</label>
                </div>
                <div class="col-xs-4">
                  <label
                    v-if="
                      sales_order.client.class == 'INET CLIENTS' ||
                        sales_order.client.class == 'DCTECH'
                    "
                  >
                    Emmanuel G. Llabore Jr.
                  </label>
                  <label v-if="sales_order.client.class == 'SOLUTIONS CLIENTS'">
                    Gabriel Sanchez
                  </label>
                  <br />
                  <br />
                  <br />
                  <br />_________________________________
                  <br />
                  <label>Signature over Printed Name/Date</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </center>
    </div>
  </div>
</template>

<script>
var moment = require("moment");
import JsonExcel from "vue-json-excel";
moment().format();

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import VueRangedatePicker from "vue-rangedate-picker";
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "json-excel": JsonExcel,
    datePicker,
    "rangedate-picker": VueRangedatePicker,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      clients: [],
      filterBy: "",
      printData: {
        id: true,
        client: true,
        soNum: true,
        status: true,
        updated_at: false,
        created_at: false,
        created_by: false
      },
      filter: {
        updated_at: "",
        created_at: ""
      },
      search: {
        text: "",
        sort: "1",
        filter: "client",
        number: "",
        sales_no: "",
        statusSelected: "",
        date_from: "",
        date_to: "",
        note: "",
        memo: ""
      },
      options: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      delivery_receipts: [],
      roles: [],
      dataForExcel: [],
      showLoading: false,
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
      enableEdit: false,
      authenticatedUser: []
    };
  },

  beforeMount() {},
  mounted() {
    this.getClients();
  },

  beforeDestroy() {
    this.$validator.pause();
  },

  created() {
    this.getDeliveryReceipts();
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
  },

  methods: {
    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },
    getDeliveryReceipts() {
      this.showLoading = true;
      this.$http.get("api/delivery_receipt").then(response => {
        console.log("delivery receipts");
        console.log(response.body);
        this.delivery_receipts = response.body;
        this.showLoading = false;
        this.fetchDataaa();
      });
    },
    editRow(order, id) {
      var data = {
        id: id,
        order: order,
        user: this.authenticatedUser
      };

      this.$http
        .post("api/delivery_receipt/update_row", data)
        .then(response => {
          console.log(response.body);
        });
      swal("Item details was succesfully changed!", {
        icon: "success"
      });
    },
    fetchDataaa() {
      this.dataForExcel = [];
      for (var i = 0; i < this.delivery_receipts.length; i++) {
        var status = "";
        var client = "No client selected";
        if (this.delivery_receipts[i].client.length != 0) {
          client = this.delivery_receipts[i].client.name;
        }
        if (this.delivery_receipts[i].status == "for delivery")
          status = "Order: For Delivery";
        if (this.delivery_receipts[i].status == "delivering")
          status = "Order: On Shipped";
        if (this.delivery_receipts[i].status == "delivered")
          status = "Order: Delivered";
        this.dataForExcel.push({
          "Delivery Receipt #": this.delivery_receipts[i].id,
          Client: client,
          "Sales Order #": this.delivery_receipts[i].sales_order_id,
          Status: status,
          "Last Date Updated": this.delivery_receipts[i].updated_at,
          "Date Created": this.delivery_receipts[i].created_at,
          "Created by": this.delivery_receipts[i].user.name
        });
      }
    },
    searchDelivery() {
      console.log(this.search);
      this.showLoading = true;
      this.$http
        .post("api/delivery_receipt/search", this.search)
        .then(response => {
          console.log(response.body);
          this.delivery_receipts = response.body;
          this.fetchDataaa();
          this.showLoading = false;
        });
    },
    resetSearch() {
      this.search.text = "";
      this.search.sort = "1";
      this.search.filter = "number";
      this.search.number = "";
      this.search.sales_no = "";
      this.search.statusSelected = "";
      this.search.date_from = "";
      this.search.date_to = "";
      this.search.note = "";
      this.search.memo = "";
      this.searchText();
    },
    searchText() {
      var filter, table, tr, targetTableColCount;
      filter = this.search.text.toUpperCase();
      table = document.getElementById("itemTable");
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

    printPreview() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      $("#leftsidebar").css("display", "none");
      $("#header").css("display", "none");
      // $("#card").css("display", "none");
      // $("#print-area").css("display", "block");
      // $("#modalAddDelivery").css("display", "none");
      $(".navbar").css("display", "none");
      $(".col-md-3").attr("class", "col-md-3 col-xs-3");

      window.print();

      $(".col-md-3 col-xs-3").attr("class", "col-md-3");
      $(".content").css("margin-left", "315px");
      $(".content").css("margin-right", "15px");
      $(".content").css("margin-top", "100px");
      $("#leftsidebar").css("display", "block");
      $("#header").css("display", "block");
      // $("#modalAddRequest").css("display", "block");
      // $("#card").css("display", "block");
      $(".navbar").css("display", "block");
    },
    getIndex(id) {
      console.log(id);
      this.$http.get("api/delivery_receipt/" + id).then(response => {
        console.log(response.body);
        this.delivery_receipt.id = response.body.id;
        this.delivery_receipt.orders = response.body.orders;
        this.delivery_receipt.user = response.body.user;
        this.delivery_receipt.status = response.body.status;
        this.delivery_receipt.created_at = response.body.created_at;
        this.delivery_receipt.received_at = response.body.received_at;
        this.delivery_receipt.delivered_at = response.body.received_at;
        this.delivery_receipt.return_qty = response.body.return_qty;
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

                swal(
                  "Delivery Receipt #" +
                    this.delivery_receipt.id +
                    " was succesfully changed!",
                  {
                    icon: "success"
                  }
                );
                document.getElementById("delivery-dismiss").click();
                this.getDeliveryReceipts();
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
  .printable {
    display: block;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
  }
}
.signatories {
  margin-top: 100px;
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
  background-color: #d3d3d3;
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

.date-range {
  border: none !important;
  border-bottom: 1px solid black !important;
  box-shadow: none !important;
  width: 50%;
  margin-right: 5px;
  border-radius: 0 0 0 0 !important;
}
.note-text {
  margin: 0px 0px;
  padding: 5px;
  min-height: 16px;
  line-height: 16px;
  width: 96%;
  display: block;
  margin: 0px auto;
}
</style>
