<template>
  <div class="container-fluid">
    <div class="not-printable" id="not-printable">
      <div class="block-header" id="block-header">
        <div class="row clearfix">
          <div class="col-md-12" style="display:block;margin-top:-20px">
            <button
              class="btn btn-default waves-effect"
              @click="createSalesOrder"
              data-toggle="modal"
              data-target="#modalAddRequest"
              v-show="roles.create_sales_order"
              v-if="roles.create_sales_order"
            >
              <i class="material-icons">note_add</i>
              <span>Create New</span>
            </button>
          </div>
        </div>
      </div>
      <div class="card" id="card">
        <div class="header">
          <h2>Manage Material Requests</h2>
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
                      <!-- <model-list-select
                      class="search-list"
                      :list="clients"
                      v-model="search.clientSelected"
                      option-value="id"
                      option-text="name"
                    >
                    </model-list-select> -->
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
              <span>Showing {{ sales_orders.length }} entries</span>
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
                        <th>Request No.</th>
                        <th>Received</th>
                        <th>Client</th>
                        <th>Created by</th>
                        <th>Date Created</th>
                        <th>Date Updated</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- <router-link
                        tag="tr"
                        v-for="sales_order in sales_orders"
                        :key="sales_order.id"
                        :to="'/sales_order/' + sales_order.id"
                        style="cursor: pointer"
                      > -->
                      <tr
                        v-for="sales_order in sales_orders"
                        :key="sales_order.id"
                        style="cursor: pointer;"
                        @click="getIndex(sales_order.id)"
                        data-toggle="modal"
                        data-target="#modalAddRequest"
                      >
                        <td>
                          <b>{{ sales_order.id }}</b>
                        </td>
                        <td
                          v-if="
                            sales_order.total_delivered_qty > 0 &&
                              sales_order.total_sales_ordered_qty > 0
                          "
                        >
                          {{ sales_order.percent }}%
                        </td>
                        <td v-else>0%</td>
                        <td v-if="sales_order.client != null">
                          {{ sales_order.client.name }}
                        </td>
                        <td v-else>
                          <small class="col-red">
                            <i>no client selected</i>
                          </small>
                        </td>
                        <td>{{ sales_order.user.name }}</td>
                        <td>
                          <small>{{ sales_order.created_at }}</small>
                        </td>
                        <td>
                          <small>{{ sales_order.updated_at }}</small>
                        </td>
                        <td
                          class="bg-grey"
                          v-show="sales_order.status == 'draft'"
                        >
                          <span>Draft</span>
                        </td>
                        <td
                          class="bg-yellow"
                          v-show="sales_order.status == 'approval'"
                        >
                          <span>For Approval</span>
                        </td>
                        <td
                          class="bg-green"
                          v-show="sales_order.status == 'approved'"
                        >
                          <span>Order: Ongoing</span>
                        </td>
                        <td
                          class="bg-red"
                          v-show="sales_order.status == 'declined'"
                        >
                          <span>Order Declined</span>
                        </td>

                        <td
                          class="bg-blue"
                          v-show="sales_order.status == 'order complete'"
                        >
                          <span>Order Fulfilled</span>
                        </td>
                      </tr>
                      <!-- </router-link> -->
                      <tr v-show="sales_orders.length == 0">
                        <td colspan="7" class="text-center">
                          <small class="col-red">
                            <i>No orders found.</i>
                          </small>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- END ORDER LIST TABLE-->
              <br />
              <p>{{ sales_orders.length }} orders found.</p>
            </div>
          </div>
        </div>
      </div>
      <!-- create material request -->
      <div
        id="modalAddRequest"
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
                <div class="body body-material" id="email-body">
                  <div class="row clearfix">
                    <div class="col-md-6 col-sm-6">
                      <h4 style="text-align:left">MATERIAL REQUEST</h4>
                    </div>

                    <div class="col-md-6 col-sm-6">
                      <div
                        class="alert alert-default"
                        v-show="request.status == 'draft'"
                      >
                        <b>Status:</b> Draft
                      </div>
                      <div
                        class="alert alert-approval"
                        v-show="request.status == 'approval'"
                      >
                        <b>Status:</b> For Approval
                      </div>
                      <div
                        class="alert alert-success"
                        v-show="request.status == 'approved'"
                      >
                        <b>Status:</b> Order Approved
                      </div>
                      <div
                        class="alert alert-danger"
                        v-show="request.status == 'declined'"
                      >
                        <b>Status:</b> Order Declined
                      </div>
                      <div
                        class="alert alert-success"
                        v-show="request.status == 'on order'"
                      >
                        <b>Status:</b> On Order
                      </div>
                      <div
                        class="alert alert-success"
                        v-show="request.status == 'order complete'"
                      >
                        <b>Status:</b> Order fulfilled
                      </div>
                    </div>
                  </div>
                  <br />
                  <div class="row clearfix">
                    <div
                      class="col-md-3 col-sm-12"
                      style="margin-top:-10px"
                      v-if="
                        request.client.class == 'INET CLIENTS' ||
                          request.client.class == 'DCTECH'
                      "
                    >
                      <img src="../../img/new_email.gif" style="width:100%" />
                    </div>
                    <div
                      class="col-md-3 col-sm-12"
                      style="margin-top:-10px"
                      v-else
                    >
                      <img src="../../img/soln.gif" style="width:100%" />
                    </div>
                    <div class="col-md-3">
                      <br />
                      <address style="text-align:left">
                        <strong>Dctech Microservices, Inc.</strong>
                        <br />Dctech Bldg., C. Bangoy Street <br />Davao City,
                        8000, Philippines
                      </address>
                    </div>
                    <div
                      class="col-md-3"
                      v-if="
                        roles.create_sales_order &&
                          request.status != 'order complete'
                      "
                    >
                      <br />

                      <address style="text-align:left">
                        <div class="form-line">
                          <model-list-select
                            class="search-list"
                            :list="clients"
                            option-value="id"
                            option-text="name"
                            v-model="client"
                            @input="selectClient(client)"
                            name="client_name"
                            v-validate="'required'"
                            placeholder="Please select client .."
                          >
                          </model-list-select>
                        </div>
                        <small
                          class="text-danger pull-left"
                          v-show="errors.has('client_name')"
                          >Client is required.</small
                        >
                      </address>
                    </div>
                    <div class="col-md-3" v-else>
                      <br />
                      <address style="text-align:left">
                        <strong>{{ request.client.name }}</strong>
                        <br />
                        <span>{{ request.client.location }}</span>
                        <br />
                        <span>{{ request.client.contact }}</span>
                      </address>
                    </div>
                    <div class="col-md-3">
                      <br />
                      <p style="text-align:left">
                        M.R
                        <b>#{{ request.id }}</b>
                        <br />
                        Date Created: {{ request.created_at }}
                      </p>
                    </div>
                  </div>
                  <br />
                  <br />

                  <div
                    class="row clearfix"
                    v-if="
                      (roles.create_sales_order && request.status == 'draft') ||
                        request.status == 'approval'
                    "
                  >
                    <div class="col-md-6"></div>

                    <div class="col-md-6">
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

                    <!-- <div class="col-md-1">
                      <button
                        class="btn btn-success waves-effect"
                        title="Add this item"
                        @click="selectItem(item)"
                      >
                        <i class="material-icons">note_add</i>Add
                      </button>
                    </div> -->
                  </div>
                  <br />
                  <br />
                  <div class="row clearfix">
                    <div class="col-md-12 col-sm-12">
                      <div class="table-wrap" style="height:auto;">
                        <table class="table table-stripped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Name</th>
                              <th>Description</th>
                              <th>Qty</th>
                              <th>Delivered Qty</th>
                              <th>Price</th>
                              <th>Amount</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody v-if="this.request.orders.length == 0">
                            <tr>
                              <td colspan="9" class="text-center">
                                <small class="col-red">
                                  <i>No orders yet.</i>
                                </small>
                              </td>
                            </tr>
                          </tbody>
                          <tbody v-else>
                            <tr v-for="order in request.orders" :key="order.id">
                              <td>{{ order.id }}</td>
                              <td>{{ order.name }}</td>
                              <td>{{ order.description }}</td>
                              <td>
                                <input
                                  type="text"
                                  v-model="order.ordered_qty"
                                  @keyup="compute"
                                  v-validate="'required|min_value:1|numeric'"
                                  v-bind:name="order.id + 'qty'"
                                  :disabled="
                                    request.status == 'approved' ||
                                      request.status == 'order complete' ||
                                      !roles.create_sales_order
                                  "
                                />
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
                                  v-validate="'required|min_value:0|decimal:2'"
                                  v-bind:name="order.id + 'price'"
                                  :disabled="
                                    request.status == 'approved' ||
                                      request.status == 'order complete' ||
                                      !roles.create_sales_order
                                  "
                                />
                                <p>
                                  <small
                                    class="text-danger"
                                    v-show="errors.has(order.id + 'price')"
                                    >Price is required</small
                                  >
                                </p>
                              </td>
                              <td class="amount">
                                {{
                                  (order.price * order.ordered_qty).toFixed(2)
                                }}
                              </td>
                              <td v-if="roles.create_sales_order">
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

                  <div class="row clearfix signatories" style="width:100%">
                    <!-- NOTES -->
                    <div class="col-md-6 col-xs-6" style="text-align:left">
                      <b>Note:</b>
                      <div class="input-group">
                        <div class="form-line">
                          <textarea
                            type="text"
                            class="form-control"
                            v-model="request.note"
                            :disabled="
                              request.status == 'order complete' ||
                                !roles.create_sales_order
                            "
                          ></textarea>
                        </div>
                      </div>
                      <br />
                      <b>Requested By:</b>
                      <div class="input-group">
                        <div class="form-line">
                          <textarea
                            type="text"
                            class="form-control"
                            v-model="request.requestor"
                            :disabled="
                              request.status == 'order complete' ||
                                !roles.create_sales_order
                            "
                          ></textarea>
                        </div>
                      </div>
                      <br />
                      <span>Encoded By:{{ request.user.name }}</span>
                      <br />
                    </div>

                    <!-- AMOUNT -->
                    <div class="col-md-6 col-xs-6">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tbody>
                            <tr>
                              <th>Subtotal:</th>
                              <td>
                                {{ request.amount.subtotal.toFixed(2) }}
                              </td>
                            </tr>
                            <tr>
                              <th>Tax:</th>
                              <td>{{ request.amount.tax.toFixed(2) }}</td>
                            </tr>
                            <tr>
                              <th>Shipping:</th>
                              <td>
                                {{ request.amount.shipping.toFixed(2) }}
                              </td>
                            </tr>
                            <tr>
                              <th>Total:</th>
                              <td>{{ request.amount.total.toFixed(2) }}</td>
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
                  class="btn btn-lg btn-info waves-effect pull-left"
                  @click="update()"
                  v-if="request.status != 'draft' && roles.update_sales_order"
                >
                  Save Changes
                </button>
                <button
                  class="btn btn-lg btn-default waves-effect pull-left"
                  @click="copy()"
                  v-if="request.status != 'draft' && roles.create_sales_order"
                >
                  Duplicate
                </button>
                <button
                  class="btn btn-lg btn-success waves-effect pull-left"
                  v-show="roles.send_mr_email && request.status == 'approval'"
                  @click="email"
                >
                  Email For Approval
                </button>
                <button
                  class="btn btn-lg btn-success waves-effect pull-left"
                  v-show="
                    request.status == 'approval' && roles.approved_sales_order
                  "
                  @click="accept"
                >
                  Accept
                </button>
                <button
                  class="btn btn-lg btn-warning waves-effect pull-left"
                  v-show="
                    request.status == 'approval' && roles.approved_sales_order
                  "
                  @click="decline"
                >
                  Decline
                </button>
                <button
                  class="btn btn-lg btn-info waves-effect"
                  @click="createRequest"
                  v-show="request.status == 'draft'"
                >
                  Send for approval
                </button>
                <button
                  type="button"
                  class="btn btn-lg btn-success waves-effect"
                  @click="createDeliveryReceipt"
                  data-toggle="modal"
                  data-target="#modalAddDelivery"
                  v-show="
                    request.status == 'approved' &&
                      roles.create_delivery_receipt &&
                      roles.approved_sales_order
                  "
                >
                  Create Delivery Receipt
                </button>
                <button
                  type="button"
                  class="btn btn-lg btn-danger waves-effect"
                  @click="cancelRequest"
                  v-show="
                    request.status == 'approved' &&
                      roles.create_delivery_receipt &&
                      roles.approved_sales_order
                  "
                >
                  Cancel
                </button>
                <button
                  class="btn btn-lg btn-info waves-effect"
                  @click="printPreview"
                  v-if="
                    request.status == 'approved' ||
                      request.status == 'order complete'
                  "
                >
                  Print Preview
                </button>
              </div>
            </div>
          </div>
        </center>
      </div>
      <!-- create delivery receipt -->
      <div
        id="modalAddDelivery"
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
                  @click="resetDelivery"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="body">
                  <div class="row clearfix">
                    <div class="col-md-6 col-sm-6">
                      <h4 style="text-align:left">DELIVERY RECEIPT</h4>
                    </div>

                    <div class="col-md-6 col-sm-6">
                      <div
                        class="alert alert-warning"
                        v-show="delivery.status == 'for delivery'"
                      >
                        <b>Status:</b> For Delivery
                      </div>
                      <div
                        class="alert alert-success"
                        v-show="delivery.status == 'delivering'"
                      >
                        <b>Status:</b> On Shipped
                      </div>
                      <div
                        class="alert alert-success"
                        v-show="delivery.status == 'delivered'"
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
                        request.client.class == 'INET CLIENTS' ||
                          request.client.class == 'DCTECH'
                      "
                    >
                      <img src="../../img/new_email.gif" style="width:100%" />
                    </div>
                    <div
                      class="col-md-3 col-sm-12"
                      style="margin-top:-10px"
                      v-else
                    >
                      <img src="../../img/soln.gif" style="width:100%" />
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
                        <strong>{{ request.client.name }}</strong>
                        <br />
                        <span>{{ request.client.location }}</span>
                        <br />
                        <span>{{ request.client.contact }}</span>
                      </address>
                    </div>
                    <div class="col-md-3">
                      <br />
                      <p style="text-align:left">
                        M.R
                        <b>#{{ request.id }}</b>
                        <br />
                        Date Created: {{ request.created_at }}
                      </p>
                    </div>
                  </div>
                  <br />
                  <br />

                  <div class="row clearfix">
                    <div class="col-md-12 col-sm-12">
                      <div class="table-wrap" style="height:auto">
                        <table class="table table-stripped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Name</th>
                              <th>Description</th>
                              <th>Ordered Qty</th>
                              <th>Released Qty</th>
                              <th>Delivered Qty</th>
                              <th>Remarks</th>
                              <th>Price</th>
                              <th>Amount</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(order, index) in delivery.orders"
                              :key="index"
                            >
                              <td>{{ order.id }}</td>
                              <td>{{ order.name }}</td>
                              <td>{{ order.description }}</td>
                              <td>{{ order.ordered_qty }}</td>
                              <td>
                                <input
                                  type="text"
                                  v-model="order.delivering_qty"
                                  v-validate="'required|min_value:1|numeric'"
                                  v-bind:name="order.id + 'qty'"
                                  :disabled="
                                    delivery.status != 'for delivery' ||
                                      order.delivered_qty == order.ordered_qty
                                  "
                                />
                                <p>
                                  <small
                                    class="text-danger"
                                    v-if="errors.has(order.id + 'qty')"
                                    >Qty is required</small
                                  >
                                  <small
                                    class="text-danger"
                                    v-if="
                                      order.total_qty < order.delivering_qty
                                    "
                                    >Total On-Hand is only
                                    {{ order.total_qty }}</small
                                  >
                                  <small
                                    class="text-danger"
                                    v-if="
                                      order.ordered_qty - order.delivered_qty <
                                        order.delivering_qty
                                    "
                                    >Value can't be greater than remaining order
                                  </small>
                                </p>
                              </td>
                              <td>{{ order.delivered_qty }}</td>
                              <td>
                                <textarea
                                  v-model="order.remarks"
                                  :disabled="delivery.status != 'for delivery'"
                                ></textarea>
                              </td>
                              <td>
                                {{ order.price }}
                              </td>
                              <td>
                                {{ order.price * order.delivering_qty }}
                              </td>
                              <td
                                v-show="
                                  delivery.status == 'for delivery' ||
                                    order.delivered_qty == order.ordered_qty
                                "
                              >
                                <a
                                  href="javascript:void(0);"
                                  title="remove"
                                  @click="removefromDelivery(order.id)"
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

                  <div class="row clearfix" style="margin-top:100px">
                    <!-- NOTES -->
                    <div class="col-md-6 col-xs-6" style="text-align:left">
                      <b>Note:</b>
                      <br />
                      {{ request.note }}
                      <br />
                      <br />
                      <br />
                      <b>Requested By:</b>
                      <br />
                      {{ request.requestor }}

                      <br />
                    </div>

                    <!-- AMOUNT -->
                    <div class="col-md-6 col-xs-6">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tbody>
                            <tr>
                              <th>Subtotal:</th>
                              <td>{{ request.amount.subtotal.toFixed(2) }}</td>
                            </tr>
                            <tr>
                              <th>Tax:</th>
                              <td>{{ request.amount.tax.toFixed(2) }}</td>
                            </tr>
                            <tr>
                              <th>Shipping:</th>
                              <td>{{ request.amount.shipping.toFixed(2) }}</td>
                            </tr>
                            <tr>
                              <th>Total:</th>
                              <td>{{ request.amount.total.toFixed(2) }}</td>
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
                      {{ delivery.user.name }}
                      <br />
                      <br />
                      <br />
                      <b> Alvin Jay P. Angcon</b>
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
                          request.client.class == 'INET CLIENTS' ||
                            request.client.class == 'DCTECH'
                        "
                        >Emmanuel G. Llabore Jr.</b
                      >
                      <b v-if="request.client.class == 'SOLUTIONS CLIENTS'"
                        >Gabriel Sanchez</b
                      >
                      <br />
                      <br />
                      <br />______________________________________
                      <br />
                      Signature over Printed Name/Date
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button
                  class="btn btn-lg btn-success waves-effect"
                  @click="save"
                >
                  Create Receipt
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
        <div style="width:100%">
          <div class="body body-material">
            <div class="row clearfix">
              <h4>MATERIAL REQUEST</h4>
            </div>
            <br />
            <div class="row clearfix">
              <div
                class="col-md-3 "
                style="margin-top:-10px"
                v-if="
                  request.client.class == 'INET CLIENTS' ||
                    request.client.class == 'DCTECH'
                "
              >
                <!-- <img src="../../img/new_email.gif" style="width:100%" /> -->
                <img
                  src="https://i.ibb.co/QMmWPdX/email-logo.png"
                  style="width:100%"
                />
              </div>
              <div class="col-md-3" style="margin-top:-10px" v-else>
                <!-- <img src="../../img/soln.gif" style="width:100%" /> -->
                <img
                  src="https://i.ibb.co/tBmbSBG/soln.png"
                  style="width:100%"
                />
              </div>
              <div class="col-md-3">
                <address style="text-align:left">
                  <strong>Dctech Microservices, Inc.</strong>
                  <br />Dctech Bldg., C. Bangoy Street <br />Davao City, 8000,
                  Philippines
                </address>
              </div>
              <div class="col-md-3">
                <address style="text-align:left">
                  <strong>{{ request.client.name }}</strong>
                  <br />
                  <span>{{ request.client.location }}</span>
                  <br />
                  <span>{{ request.client.contact }}</span>
                </address>
              </div>
              <div class="col-md-3" v-show="request.status != 'draft'">
                <p style="text-align:left">
                  M.R
                  <b>#{{ request.id }}</b>
                  <br />
                  Date Created: {{ request.created_at }}
                </p>
              </div>
            </div>
            <br />
            <div class="row clearfix">
              <div class="col-md-12 col-sm-12">
                <div class="table-wrap" style="height:auto;min-height:60vh">
                  <table class="table table-stripped">
                    <thead>
                      <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Delivered Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody v-if="this.request.orders.length == 0">
                      <tr>
                        <td colspan="9" class="text-center">
                          <small class="col-red">
                            <i>No orders yet.</i>
                          </small>
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-else>
                      <tr v-for="order in request.orders" :key="order.id">
                        <td>{{ order.id }}</td>
                        <td>{{ order.name }}</td>
                        <td>{{ order.description }}</td>
                        <td>{{ order.ordered_qty }}</td>
                        <td>{{ order.delivered_qty }}</td>
                        <td>{{ order.price }}</td>
                        <td class="amount">
                          {{ (order.price * order.ordered_qty).toFixed(2) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="row clearfix signatories">
              <!-- NOTES -->
              <div class="col-md-6 col-xs-6" style="text-align:left">
                <b>Note:</b>

                <br />
                {{ request.note }}
                <br />
                <b>Requested By:</b>

                <br />
                {{ request.requestor }}
                <br />
                <b>Encoded By:</b>
                <br />
                {{ request.user.name }}
                <br />
              </div>

              <!-- AMOUNT -->
              <div class="col-md-6 col-xs-6">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <th>Subtotal:</th>
                        <td>{{ request.amount.subtotal.toFixed(2) }}</td>
                      </tr>
                      <tr>
                        <th>Tax:</th>
                        <td>{{ request.amount.tax.toFixed(2) }}</td>
                      </tr>
                      <tr>
                        <th>Shipping:</th>
                        <td>{{ request.amount.shipping.toFixed(2) }}</td>
                      </tr>
                      <tr>
                        <th>Total:</th>
                        <td>{{ request.amount.total.toFixed(2) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </center>
    </div>

    <!-- email area -->

    <div id="trial" style="display:none">
      <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
          <tr class="top">
            <td colspan="8">
              <table>
                <tr>
                  <td
                    class="title"
                    v-if="
                      request.client.class == 'INET CLIENTS' ||
                        request.client.class == 'DCTECH'
                    "
                  >
                    <img
                      src="https://i.ibb.co/QMmWPdX/email-logo.png"
                      style="width:100%; max-width:300px;"
                    />
                  </td>
                  <td class="title" v-else>
                    <img
                      src="https://i.ibb.co/tBmbSBG/soln.png"
                      style="width:100%; max-width:250px;"
                    />
                  </td>

                  <td style="float:right;font-size:13px">
                    Material Request #: {{ request.id }}<br />
                    Created: {{ request.created_at }}<br />
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr class="information">
            <td colspan="12">
              <table>
                <tr style="font-size:13px">
                  <td>
                    Dctech Building, Shanghai Street,<br />
                    Matina Aplaya, Davao City<br />
                    Davao Del Sur 8000, Philippines<br />
                    Tel #: (082) 221-2380<br />
                    VAT Registered TIN: 003-375-571-000
                  </td>

                  <td>
                    {{ request.client.name }}<br />
                    {{ request.client.location }}<br />
                    {{ request.client.contact }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr class="heading" style="font-size:13px">
            <td>Code</td>
            <td>Name</td>
            <td>Description</td>
            <td>Qty</td>
            <td>Delivered Qty</td>
            <td>Price</td>
            <td>Amount</td>
          </tr>
          <tr
            v-for="order in request.orders"
            :key="order.id"
            style="font-size:13px"
          >
            <td>{{ order.id }}</td>
            <td>{{ order.name }}</td>
            <td>{{ order.description }}</td>
            <td>{{ order.ordered_qty }}</td>
            <td>{{ order.delivered_qty }}</td>
            <td>{{ order.price }}</td>
            <td class="amount">
              {{ (order.price * order.ordered_qty).toFixed(2) }}
            </td>
          </tr>

          <tr class="notes">
            <td colspan="12">
              <table>
                <tr style="font-size:13px">
                  <td>
                    Note: {{ request.note }}<br />
                    Requested By: {{ request.requestor }}<br />
                    Encoded By: {{ request.user.name }}
                  </td>
                  <td>
                    Subtotal: {{ request.amount.subtotal.toFixed(2) }}<br />
                    Tax: {{ request.amount.tax.toFixed(2) }}<br />
                    Shipping : {{ request.amount.shipping.toFixed(2) }} <br />
                    Total:{{ request.amount.total.toFixed(2) }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr class="notes">
            <td>
              <table>
                <tr>
                  <td colspan="1" style="padding:0;">
                    <!-- <a
                      :href="
                        'http://localhost:8000/api/sales_order/public_accept/' +
                          request.id
                      "
                    > -->
                    <a
                      :href="
                        $back_url +
                          'api/sales_order/public_accept/' +
                          request.id
                      "
                    >
                      <button
                        style="
                            margin-right:0;
                            background:#4CAF50;
                            color:white;
                            cursor:pointer;
                            padding:10px;
                            padding-left:15px;
                            padding-right:15px;
                            border:0;
                            border-radius:3px;
                          "
                        type="submit"
                      >
                        Accept
                      </button>
                    </a>
                    <!-- <a
                      :href="
                        'http://localhost:8000/api/sales_order/public_decline/' +
                          request.id
                      "
                    > -->
                    <a
                      :href="
                        $back_url +
                          'api/sales_order/public_decline/' +
                          request.id
                      "
                    >
                      <button
                        style="

                            background:#f44336;
                            color:white;
                            cursor:pointer;
                            padding:10px;
                            padding-left:15px;
                            padding-right:15px;
                            border:0;
                            border-radius:3px
                          "
                        type="submit"
                      >
                        Decline
                      </button></a
                    >
                    <!-- <form
                      action=""
                      target="_blank"
                    >
                      <button
                        style="
                            margin-right:0;
                            background:#4CAF50;
                            color:white;
                            cursor:pointer;
                            padding:10px;
                            padding-left:15px;
                            padding-right:15px;
                            border:0;
                            border-radius:3px;
                          "
                        type="submit"
                      >
                        Accept
                      </button>
                    </form> -->
                  </td>

                  <td colspan="12"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
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
      sales_orders: [],
      roles: [],
      authenticatedUser: [],
      clients: [],
      items: [],
      itemGroups: [],
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
      delivery: {
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
      client: ""
    };
  },

  created() {
    this.getSalesOrder();
    this.loadItems();
    this.getGroups();
    this.getClients();
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
    console.log(this.authenticatedUser);
  },

  mounted() {
    //channel.bind("NotifyCreatedSalesOrder", this.liveUpdate);
    //channel.bind("NotifyUpdatedSalesOrder", this.liveUpdate);
  },

  beforeDestroy() {
    this.$validator.pause();
  },

  methods: {
    getClients() {
      this.$http.get("api/client").then(response => {
        this.clients = response.body;
      });
    },
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    getGroups() {
      this.$http.post("api/items/showItemGroup").then(response => {
        this.itemGroups = response.body;
      });
    },
    getSalesOrder() {
      this.showLoading = true;
      this.$http.get("api/sales_order").then(response => {
        console.log("material requests");
        console.log(response.body);
        this.showLoading = false;
        this.sales_orders = response.body;
      });
    },
    liveUpdate() {
      this.$http.get("api/sales_order").then(response => {
        this.sales_orders = response.body;
      });
    },
    searchSalesOrder() {
      this.showLoading = true;
      console.log(this.search);
      this.$http.post("api/sales_order/search", this.search).then(response => {
        this.showLoading = false;
        this.sales_orders = response.body;
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
    createSalesOrder() {
      this.client = "";
      this.request.client = [];
      this.request.orders = [];
      this.request.user = [];
      this.request.note = "";
      this.request.requestor = "";
      this.request.status = "draft";
      this.request.created_at = "";
      this.request.remarks = "";
      this.request.id = "";
    },

    printPreview() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      $("#leftsidebar").css("display", "none");
      $("#block-header").css("display", "none");
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
      $("#block-header").css("display", "block");
      // $("#modalAddRequest").css("display", "block");
      // $("#card").css("display", "block");
      $(".navbar").css("display", "block");
    },

    selectClient(id) {
      console.log(id);
      this.$http.get("api/client/" + id).then(response => {
        this.request.client = response.body;
      });
      // document.getElementById("client-dismiss").click();
    },
    selectItem(item) {
      console.log(item);
      var item_id = 0;
      var item_name = "";
      var item_des = "";
      var item_qty = "";

      item_id = item.id;
      item_name = item.name;
      item_des = item.description;
      item_qty = "0";

      this.execute(item, item_id, item_name, item_des, item_qty);
    },
    execute(item, item_id, item_name, item_des, item_qty) {
      if (!this.item_exist(item)) {
        this.request.orders.push({
          id: item_id,
          name: item_name,
          description: item_des,
          ordered_qty: item_qty,
          delivered_qty: "",
          price: "0",
          amount: "0"
        });
        this.compute();
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

      for (var i = 0; i < this.request.orders.length; i++) {
        if (this.request.orders[i].id == item.id) {
          bool = true;
        }
      }

      return bool;
    },

    compute() {
      var sum = 0;

      for (var index = 0; index < this.request.orders.length; index++) {
        sum +=
          this.request.orders[index].price *
          this.request.orders[index].ordered_qty;
      }

      this.request.amount.shipping = 0;
      this.request.amount.tax = 0;
      this.request.amount.subtotal = sum;
      this.request.amount.total = sum;
    },
    removefromOrder(id) {
      for (var index = 0; index < this.request.orders.length; index++) {
        if (this.request.orders[index].id == id) {
          this.request.orders.splice(index, 1);
        }
      }
    },
    email() {
      swal({
        title: "Are you sure?",
        text: "Once sent, your manager/supervisor will receive the request!",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willEmail => {
        if (willEmail) {
          this.request.email_body = document.getElementById("trial").innerHTML;
          this.$http
            .post("api/sales_order/email", this.request)
            .then(response => {
              console.log(response.body);

              swal("Email Sent to Approver!", {
                icon: "success"
              });
            });
        } else {
          swal("Material Request not sent!");
        }
      });
      // this.request.email_body = document.getElementById("trial").innerHTML;
      // this.$http.post("api/sales_order/email", this.request).then(response => {
      //   console.log(response.body);

      //   swal("Email Sent to Approver!", {
      //     icon: "success"
      //   });
      // });
    },
    createRequest() {
      this.request.user = this.authenticatedUser;

      if (this.request.orders.length > 0) {
        console.log("not empty");

        this.$validator.validateAll().then(result => {
          if (result) {
            this.$http
              .post("api/sales_order/submit_approval", this.request)
              .then(response => {
                console.log(response.body);

                swal("Submitted for approval!", {
                  icon: "success"
                });

                document.getElementById("request-dismiss").click();
                this.getSalesOrder();
                this.$root.$emit("Sidebar");
              });
          }
        });
      } else
        swal("Orders cannot be Empty!", {
          icon: "error"
        });
    },
    getIndex(id) {
      console.log(id);

      this.$http.get("api/sales_order/" + id).then(response => {
        console.log(response.body);
        if (response.body.client != null) this.client = response.body.client_id;
        this.request.client = response.body.client;
        this.request.orders = response.body.sales_order_details;
        this.request.user = response.body.user;
        this.request.note = response.body.note;
        this.request.requestor = response.body.requestor;
        this.request.status = response.body.status;
        this.request.created_at = response.body.created_at;
        this.request.remarks = response.body.remarks;
        this.request.id = response.body.id;

        this.compute();
        // $(".page-loader-wrapper").fadeOut();
      });
    },
    accept() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/sales_order/accept", this.request)
            .then(response => {
              console.log(response.body);
              swal("Material Request #" + this.request.id + " accepted!", {
                icon: "success"
              });
              document.getElementById("request-dismiss").click();

              this.getSalesOrder();
              this.$root.$emit("Sidebar");
            });
        }
      });
    },

    decline() {
      this.$http
        .post("api/sales_order/decline/" + this.request.id)
        .then(response => {
          swal("Material Request #" + this.request.id + " declined!", {
            dangerMode: true
          });
          document.getElementById("request-dismiss").click();
          this.getSalesOrder();
          $("#modalAddRequest").modal("hide");
        });
    },
    copy() {
      this.client = "";
      this.request.client = [];
      this.request.user = [];
      this.request.note = this.request.note;
      this.request.requestor = this.request.requestor;
      this.request.status = "draft";
      this.request.created_at = "";
      this.request.remarks = "";
      this.request.id = "";
      let items = [];
      this.request.orders.forEach(item => {
        items.push({
          id: item.id,
          name: item.name,
          description: item.description,
          type: item.type,
          price: item.price,
          ordered_qty: item.ordered_qty
        });
      });
      this.request.orders = items;
    },
    cancelRequest() {
      this.$http
        .post("api/sales_order/cancel/" + this.request.id)
        .then(response => {
          swal("Material Request #" + this.request.id + " cancelled!", {
            dangerMode: true
          });
          document.getElementById("request-dismiss").click();
          this.getSalesOrder();
          $("#modalAddRequest").modal("hide");
        });
    },
    createDeliveryReceipt() {
      console.log(this.delivery.orders);
      // this.delivery.orders = this.request.orders;
      this.delivery.status = "for delivery";

      let items = [];
      this.request.orders.forEach(item => {
        items.push({
          id: item.id,
          name: item.name,
          description: item.description,
          type: item.type,
          price: item.price,
          total_qty: item.total_qty,
          delivered_qty: item.delivered_qty,
          ordered_qty: item.ordered_qty,
          delivering_qty: item.delivering_qty,
          remarks: item.remarks
        });
      });
      this.delivery.orders = items;
    },
    removefromDelivery(id) {
      for (var index = 0; index < this.delivery.orders.length; index++) {
        if (this.delivery.orders[index].id == id) {
          this.delivery.orders.splice(index, 1);
        }
      }
    },
    resetDelivery() {
      this.delivery.orders = [];
      document.getElementById("request-dismiss").click();
    },
    save() {
      console.log(this.delivery);
      this.$validator.validateAll().then(result => {
        if (result) {
          this.delivery.user = this.authenticatedUser;

          swal("Create a Delivery Receipt for M.R #" + this.request.id + "?", {
            buttons: {
              Yes: true,
              cancel: "Cancel"
            }
          }).then(value => {
            switch (value) {
              case "Yes":
                var collection = {
                  material_request: this.request,
                  delivery_receipt: this.delivery,
                  user: this.delivery.user
                };
                this.$http
                  .post("api/delivery_receipt/deliver", collection)
                  .then(response => {
                    console.log(response.body);
                    swal("Delivery Receipt Saved!", {
                      icon: "success"
                    });
                    document.getElementById("delivery-dismiss").click();
                    this.getDelivery(response.body);
                    this.$root.$emit("Sidebar");
                  });

                break;

              default:
                break;
            }
          });
        }
      });
    },
    getDelivery(id) {
      this.$router.push({
        path: "/delivery_receipt/" + id
      });
    },
    getItemDesc(item) {
      return `${item.name} - ${item.description}`;
    },
    update() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/sales_order/" + this.request.id, this.request)
            .then(response => {
              console.log(response.body);
              swal("Material Request #" + this.request.id + " updated!", {
                icon: "success"
              });

              document.getElementById("request-dismiss").click();

              this.getSalesOrder();
              this.$root.$emit("Sidebar");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                this.getSalesOrder();
                this.$root.$emit("Sidebar");
              });
            });
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
    font-size: 15px;
  }
}
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
