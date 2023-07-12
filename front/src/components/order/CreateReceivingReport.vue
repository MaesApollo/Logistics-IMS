<template>
  <div class="container-fluid">
    <pre-loader></pre-loader>
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-lg-9 col-md-9">
          <button
            type="button"
            class="btn btn-default waves-effect"
            style="margin-right: 10px"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>

          <button
            class="btn btn-lg btn-default waves-effect"
            @click.prevent="print"
          >
            Print Preview
          </button>

          <!-- SAVE BUTTON START -->
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect"
            @click="savePurchaseOrder"
            :hidden="data.status != 'draft'"
          >
            <span>Save</span>
          </button>

          <!-- SELECT SUPPLIER -->
          <button
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            data-target="#supplierModal"
            :hidden="
              data.status != 'draft' ||
                data.status == 'approval' ||
                data.status == 'declined' ||
                data.status == 'on order' ||
                data.status == 'approved' ||
                data.status == 'order complete'
            "
          >
            <span>Select Supplier</span>
          </button>

          <!-- APPROVAL BUTTON START -->
          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            @click="submitApproval"
            :hidden="
              data.status != 'draft' ||
                data.status == 'approval' ||
                data.status == 'declined' ||
                data.status == 'on order' ||
                data.status == 'approved' ||
                data.status == 'order complete'
            "
            :disabled="data.orders.length < 1"
          >
            <span>Submit For Approval</span>
          </button>
          <!-- SUPPLIER BUTTON START -->
          <button
            type="button"
            class="btn btn-lg btn-default waves-effect"
            @click="submitSupplier"
            :hidden="
              data.status == 'draft' ||
                data.status == 'approval' ||
                data.status == 'declined' ||
                data.status == 'on order' ||
                data.status == 'order complete' ||
                data.orders.length < 1
            "
          >
            <span>Submit For Supplier</span>
          </button>

          <button
            type="button"
            class="btn btn-lg btn-danger waves-effect"
            :hidden="data.status != 'draft'"
            @click="deleteDraft(data.id)"
          >
            <span>Delete Draft</span>
          </button>

          <div style="float:right; display:block;margin-right:-250px">
            <button
              type="button"
              class="btn btn-lg btn-success waves-effect"
              @click="acceptPurchaseOrder"
              v-show="
                data.status == 'approval' &&
                  roles.approved_purchase_order &&
                  data.orders.length > 0
              "
            >
              <span>Accept</span>
            </button>
            <button
              type="button"
              class="btn btn-lg btn-danger waves-effect"
              @click="declinePurchaseOrder"
              v-show="
                data.status == 'approval' && roles.approved_purchase_order
              "
            >
              <span>Decline</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card" id="printable">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-8 col-sm-12">
            <h4>Purchase Order #{{ $route.params.purchase_order }}</h4>
            <p>Date Created:{{ data.created_at }}</p>
          </div>
          <div class="col-md-4 col-sm-12">
            <div class="alert alert-warning" v-show="data.status == 'draft'">
              <b>Status:</b> Draft
            </div>
            <div
              class="alert alert-approval"
              v-show="data.status == 'approval'"
            >
              <b>Status:</b> For Approval
            </div>
            <div class="alert alert-success" v-show="data.status == 'approved'">
              <b>Status:</b> Order Approved
            </div>
            <div class="alert alert-danger" v-show="data.status == 'declined'">
              <b>Status:</b> Order Declined
            </div>
            <div class="alert alert-success" v-show="data.status == 'on order'">
              <b>Status:</b> On Order
            </div>
            <div
              class="alert alert-success"
              v-show="data.status == 'order complete'"
            >
              <b>Status:</b> Order fulfilled
            </div>
          </div>
        </div>

        <div class="row clearfix" style="margin-top:-20px;display:flex">
          <div style="width:15%">
            <img src="../../img/logo.jpg" />
          </div>
          <div style="margin-top:14px;line-height:100%;width:40%">
            Dctech Building, Shanghai Street,<br />Matina Aplaya, Davao City<br />Davao
            Del Sur 8000, Philippines<br />Tel #: (082) 221-2380<br />VAT
            Registered TIN: 003-375-571-000
          </div>

          <div
            style="margin-top:14px;line-height:100%;width:30%;margin-left:150px"
          >
            <div v-if="data.supplier != null">
              <b>{{ data.supplier.name }} </b><br />
              Tel #:{{ data.supplier.contact }} <br />
              {{ data.supplier.email }} <br />
              {{ data.supplier.address }}<br />
              Registered TIN:{{ data.supplier.tin }}
            </div>
            <div v-else>
              <small>
                <i>select supplier</i>
              </small>
            </div>
          </div>
        </div>

        <div class="row clearfix" style="margin-top:10px">
          <div style="height:40%;width:30%">
            <p>
              <b>Expected Delivery Date :</b>
              <b-form-datepicker
                id="datepicker-valid"
                :state="true"
                v-model="data.delivery_date"
                name="expected_date"
                :disabled="
                  data.status == 'approval' ||
                    data.status == 'approved' ||
                    data.status == 'declined' ||
                    data.status == 'on order' ||
                    data.status == 'order complete'
                "
              ></b-form-datepicker>
            </p>
          </div>
        </div>

        <div class="row clearfix">
          <div class="col-md-12">
            <!-- NAVIGATION -->
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
              <li role="presentation" class="active">
                <a href="#items" data-toggle="tab" aria-expanded="true"
                  >Line Items</a
                >
              </li>
              <li role="presentation" class>
                <a href="#costs" data-toggle="tab" aria-expanded="false"
                  >Additional Costs</a
                >
              </li>
              <li role="presentation" class>
                <a href="#general" data-toggle="tab" aria-expanded="false"
                  >General</a
                >
              </li>
            </ul>
            <div class="tab-content">
              <!-- ORDERED ITEM -->
              <div role="tabpanel" class="tab-pane fade active in" id="items">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div
                      class="alert alert-default"
                      v-show="
                        data.status == 'on order' &&
                          data_receives.receives.length < 1
                      "
                    >
                      <button
                        type="button"
                        class="btn btn-default waves-effect disabled"
                      >
                        <span>Receive checked items</span>
                      </button>
                    </div>
                    <div
                      class="alert alert-default"
                      v-show="
                        data.status == 'on order' &&
                          data_receives.receives.length > 0
                      "
                    >
                      <button
                        type="button"
                        class="btn btn-default waves-effect"
                        data-toggle="modal"
                        data-target="#receiveModal"
                      >
                        <span>Receive checked items</span>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <table class="table table-stripped">
                        <thead>
                          <tr>
                            <th v-show="data.status == 'on order'"></th>
                            <th>Requested Item</th>
                            <th>Code#</th>
                            <th>Qty</th>
                            <th>Received</th>
                            <th>Unit Price</th>
                            <th>Tax Rate (%)</th>
                            <th></th>
                            <th>Line Subtotal</th>
                            <th v-show="data.status == 'draft'"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="order in data.orders"
                            :key="order.id"
                            v-show="data.orders.length > 0"
                          >
                            <td v-show="data.status == 'on order'">
                              <input
                                type="checkbox"
                                v-bind:id="order.id"
                                class="chk-col-light-blue"
                                v-bind:value="order"
                                v-model="data_receives.receives"
                                v-show="
                                  order.pivot.qty != order.total_qty_received
                                "
                              />

                              <input
                                type="text"
                                v-model="order.pivot.qty"
                                @keyup="subtotal"
                                style="max-width: 40px"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:1|numeric'"
                                v-bind:name="order.id + 'qty'"
                              />

                              <label v-bind:for="order.id"></label>
                            </td>
                            <td>{{ order.name }} - {{ order.description }}</td>
                            <td>{{ order.id }}</td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.qty"
                                @keyup="subtotal"
                                style="max-width: 40px"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:1|numeric'"
                                v-bind:name="order.id + 'qty'"
                                :disabled="
                                  data.status == 'approval' ||
                                    data.status == 'approved' ||
                                    data.status == 'declined' ||
                                    data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              <span v-else>{{ order.pivot.qty }}</span>
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'qty')"
                                  >Qty is required</small
                                >
                              </p>
                            </td>
                            <td>{{ order.total_qty_received }}</td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.price"
                                style="max-width: 100px"
                                @keyup="subtotal"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:0|decimal:2'"
                                v-bind:name="order.id + 'price'"
                                :disabled="
                                  data.status == 'approval' ||
                                    data.status == 'approved' ||
                                    data.status == 'declined' ||
                                    data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              <span v-else>{{ order.pivot.price }}</span>
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'price')"
                                  >Price is required</small
                                >
                              </p>
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="order.pivot.tax_rate"
                                style="max-width: 40px"
                                @keyup="subtotal"
                                v-if="
                                  data.status != 'on order' &&
                                    data.status != 'order complete'
                                "
                                v-validate="'required|min_value:0|numeric'"
                                v-bind:name="order.id + 'tax'"
                                :disabled="
                                  data.status == 'approval' ||
                                    data.status == 'approved' ||
                                    data.status == 'declined' ||
                                    data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              <span v-else>{{ order.pivot.tax_rate }}</span>
                              <p>
                                <small
                                  class="text-danger"
                                  v-show="errors.has(order.id + 'tax')"
                                  >Tax is required</small
                                >
                              </p>
                            </td>
                            <td>
                              <span v-show="order.pivot.tax_rate > 0">{{
                                (
                                  order.pivot.price *
                                  (order.pivot.tax_rate / 100) *
                                  order.pivot.qty
                                ).toFixed(2)
                              }}</span>
                            </td>
                            <td>
                              <span
                                v-if="
                                  order.pivot.price > 0 && order.pivot.qty > 0
                                "
                                >{{
                                  (order.pivot.price * order.pivot.qty).toFixed(
                                    2
                                  )
                                }}</span
                              >
                              <span v-else>0.00</span>
                            </td>
                            <td v-show="data.status == 'draft'">
                              <a
                                href="javascript:void(0);"
                                title="remove"
                                @click="removeItem(order.id)"
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
                <div class="row clearfix">
                  <div class="col-md-12">
                    <button
                      type="button"
                      class="btn btn-sm btn-default waves-effect disabled"
                      v-if="
                        data.status == 'on order' ||
                          data.status == 'order complete'
                      "
                    >
                      <i class="material-icons">note_add</i>
                    </button>
                    <button
                      type="button"
                      class="btn btn-sm btn-default waves-effect"
                      data-toggle="modal"
                      data-target="#itemModal"
                      :hidden="data.status != 'draft'"
                      v-else
                    >
                      <i class="material-icons">note_add</i>
                    </button>
                    <span class="pull-right" v-show="data.orders.length < 1"
                      >{{ data.orders.length }} item.</span
                    >
                    <span class="pull-right" v-show="data.orders.length == 1"
                      >{{ data.orders.length }} item.</span
                    >
                    <span class="pull-right" v-show="data.orders.length > 1"
                      >{{ data.orders.length }} items.</span
                    >
                    <hr />
                  </div>
                </div>
              </div>

              <!-- COSTS -->
              <div role="tabpanel" class="tab-pane fade" id="costs">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <table class="table table-stripped">
                        <thead>
                          <tr>
                            <th>Purchase Order Additional Cost Type</th>
                            <th>Notes</th>
                            <th></th>
                            <th>Amount</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Tax</td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                              %
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.tax_overide"
                                @keyup="subtotal"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <div class="demo-checkbox">
                                <input
                                  type="checkbox"
                                  id="basic_checkbox_1"
                                  v-model="data.amount.overide"
                                  @keyup="subtotal"
                                  :disabled="
                                    data.status == 'on order' ||
                                      data.status == 'order complete'
                                  "
                                />
                                <label for="basic_checkbox_1"
                                  >Overide tax amount</label
                                >
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>Freight Cost</td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td>
                              <input
                                type="text"
                                v-model="data.amount.shipping_cost"
                                @keyup="subtotal"
                                :disabled="
                                  data.status == 'on order' ||
                                    data.status == 'order complete'
                                "
                              />
                            </td>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <br />
                <hr />
              </div>

              <!-- GENERAL -->
              <div role="tabpanel" class="tab-pane fade" id="general">
                <div class="row clearfix">
                  <div class="col-md-12">
                    <div class="table-wrap">
                      <p>
                        Created by: {{ data.user.name }}
                        <br />
                        <br />
                        Date created: {{ data.created_at }}
                        <br />
                        Last update: {{ data.updated_at }}
                      </p>
                    </div>
                  </div>
                </div>
                <hr />
              </div>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-5">
            <p>
              <small class="text-danger" v-show="errors.has('qty')"
                >Quantity is required, minimum value 1.</small
              >
              <small class="text-danger" v-show="errors.has('price')">
                <br />Price is required, minimum value 0.
              </small>
              <small class="text-danger" v-show="errors.has('tax')">
                <br />Tax is required, minimum value 0.
              </small>
            </p>
          </div>
          <div class="col-md-7">
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>Subtotal:</th>
                    <td>{{ data.amount.subtotal.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Freight Cost:</th>
                    <td>{{ data.amount.shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Tax:</th>
                    <td>{{ data.amount.tax.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>{{ data.amount.total.toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

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
            <form @submit.prevent="searchItem">
              <div class="row clearfix">
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="form-line">
                      <span>Search</span>
                      <input
                        id="search"
                        type="text"
                        class="form-control"
                        v-model="search.item"
                        autocomplete="off"
                      />
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <br />
                  <button class="btn btn-sm bg-black waves-effect waves-light">
                    Search
                  </button>
                  <button class="btn btn-sm btn-success waves-effect">
                    Reset
                  </button>
                </div>
              </div>
            </form>
            <div class="row clearfix">
              <div class="col-md-12">
                <table
                  id="table_id"
                  class="table table-bordered table-condensed table-hover"
                >
                  <thead>
                    <tr>
                      <th>Add</th>
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
                          @click="selectItem(item)"
                        >
                          Add
                        </button>
                      </td>
                      <td>{{ item.name }} - {{ item.description }}</td>
                      <td>{{ item.id }}</td>
                      <td>{{ item.category.name }}</td>
                    </tr>
                    <tr v-show="items.length < 1">
                      <td colspan="3" class="text-center">No results found.</td>
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
    <!-- START RECEIVE MODAL -->
    <div
      class="modal fade"
      id="receiveModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background:gray">
            <h4 class="modal-title">
              Receive Items
            </h4>
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
              <div class="col-md-2 col-sm-12">
                <p>
                  Purchase Order
                  <br />PO
                  <b>#{{ $route.params.purchase_order }}</b>
                </p>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-4 col-sm-12">
                <p>
                  Date Ordered
                  <br />
                  {{ data.created_at }}
                </p>
              </div>
              <div class="col-md-6 col-sm-12" style="margin-right:-10px">
                <p>
                  Date Received
                  <b-form-datepicker
                    id="datepicker-valid"
                    :state="true"
                    v-model="data_receives.date_receive"
                    v-validate="{
                      required: data.status === 'on order' ? true : false
                    }"
                  ></b-form-datepicker>
                  <small class="text-danger" v-show="errors.has('date_receive')"
                    >Date receive is required.</small
                  >
                </p>
              </div>
            </div>
            <hr />
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <table class="table table-stripped">
                    <thead>
                      <tr>
                        <th>Requested Item</th>
                        <th>Code#</th>
                        <th>Qty Ordered</th>
                        <th>Qty Received</th>
                        <th>Total Received</th>
                        <th>Unit Price</th>
                        <th>Receive To</th>
                        <th>File Upload</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="receive in data_receives.receives"
                        :key="receive.id"
                        v-show="data_receives.receives.length > 0"
                      >
                        <td>{{ receive.name }} - {{ receive.description }}</td>
                        <td>{{ receive.id }}</td>
                        <td>{{ receive.pivot.qty }}</td>
                        <td>
                          <input
                            type="text"
                            v-model="receive.qty_received"
                            style="max-width: 40px"
                            v-bind:name="receive.id + 'qty_receive'"
                            v-validate="{
                              required:
                                data.status === 'on order' ? true : false,
                              numeric:
                                data.status === 'on order' ? true : false,
                              min_value: 0,
                              max_value:
                                receive.pivot.qty - receive.total_qty_received
                            }"
                          />
                          <p>
                            <small
                              class="text-danger"
                              v-show="errors.has(receive.id + 'qty_receive')"
                              >Qty is required, also require less than value
                              needed.</small
                            >
                          </p>
                        </td>
                        <td>{{ receive.total_qty_received }}</td>
                        <td>{{ receive.pivot.price }}</td>
                        <td>
                          <select
                            v-model="receive.received_to"
                            v-bind:name="receive.id + 'received_to'"
                            v-validate="{
                              required:
                                data.status === 'on order' ? true : false
                            }"
                          >
                            <option
                              v-for="warehouse in warehouses"
                              :key="warehouse.id"
                              v-bind:value="warehouse.id"
                            >
                              {{ warehouse.name }}
                            </option>
                          </select>
                          <p>
                            <small
                              class="text-danger"
                              v-show="errors.has(receive.id + 'received_to')"
                              >Receiving is required</small
                            >
                          </p>
                        </td>
                        <td>
                          <a
                            href="javascript:void(0);"
                            @click="selectFile"
                            title="Import Serial"
                          >
                            <i
                              class="material-icons text-success"
                              style="font-size: 16px !important"
                              >publish</i
                            >
                          </a>
                          <h6>{{ file }}</h6>
                          <input
                            type="file"
                            id="fileSelect"
                            name="fileSelect"
                            @change="previewFiles(receive, $event)"
                            style="visibility:hidden;"
                          />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-default btn-lg waves-effect"
              @click="receiveItem"
            >
              Receive Items
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- END RECEIVE MODAL -->
    <!-- START SUPPLIER MODAL -->
    <div
      class="modal fade"
      id="supplierModal"
      tabindex="-1"
      role="dialog"
      data-keyboard="false"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header"></div>
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-12">
                <table
                  id="table_id"
                  class="table table-bordered table-condensed table-hover"
                >
                  <thead>
                    <tr>
                      <th class="text-center">Supplier</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="supplier in suppliers"
                      :key="supplier.id"
                      style="cursor: pointer"
                      @click="selectSupplier(supplier)"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <td>{{ supplier.name }}</td>
                    </tr>
                    <tr v-show="suppliers.length < 1">
                      <td class="text-center">No results found.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- <button
            type="button"
            class="btn btn-secondary"
            position="relative"
            data-dismiss="modal"
            left="80px"
            width="50px"
          >
            DONE
          </button> -->
        </div>
      </div>
    </div>
    <!-- END SUPPLIER MODAL-->
  </div>
</template>
<script>
var moment = require("moment");
moment().format();

import swal from "sweetalert";
import PreLoader from "../PreLoader.vue";

export default {
  components: {
    // "date-picker": datePicker,
    "pre-loader": PreLoader
  },

  data() {
    return {
      suppliers: [],
      items: [],
      authenticatedUser: [],
      roles: [],
      search: {
        item: null
      },
      data: {
        id: 0,
        supplier: [],
        orders: [],
        amount: {
          total: 0,
          subtotal: 0,
          shipping: 0,
          tax: 0
        },
        upload: [],
        user: [],
        delivery_date: "",
        status: "",
        created_at: "",
        updated_at: ""
      },
      data_receives: {
        purchase_order_id: null,
        date_receive: null,
        receives: [],
        barcodes: null
      },
      warehouses: [],
      file: "File"
    };
  },

  beforeMount() {},

  created() {
    this.getPurchaseOrder();
    this.loadItems();
    this.suppliers = this.$global.getSupplier();
    this.warehouses = this.$global.getWarehouses();
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
  },

  mounted() {},

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    getPurchaseOrder() {
      this.$http
        .get("api/purchase_order/" + this.$route.params.purchase_order)
        .then(response => {
          this.data.id = response.body.id;
          this.data.delivery_date = response.body.delivery_date;
          this.data.orders = response.body.item;
          this.data.supplier = response.body.supplier;
          this.data.amount.tax = response.body.tax;
          this.data.amount.shipping_cost = response.body.shipping_fee;
          this.data.status = response.body.status;
          this.data.user = response.body.user;
          this.data.created_at = response.body.created_at;
          this.data.updated_at = response.body.updated_at;
          this.subtotal();
          $(".page-loader-wrapper").fadeOut();
        });
    },
    clear() {
      this.purchase_order.orders = [];
    },
    acceptPurchaseOrder() {
      swal("Accept this order?", {
        buttons: {
          accept: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "accept":
            this.$validator.validateAll().then(result => {
              if (result) {
                this.$http
                  .put(
                    "api/purchase_order/" + this.$route.params.purchase_order,
                    this.data
                  )
                  .then(response => {});

                this.$http
                  .post(
                    "api/purchase_order/accept/" +
                      this.$route.params.purchase_order
                  )
                  .then(response => {
                    this.$http.get("api/purchase_order").then(response => {
                      this.$global.setPurchaseOrders(response.body);
                    });

                    this.$http
                      .get("api/users/" + this.authenticatedUser.id)
                      .then(response => {
                        this.$global.setRoles(response.body.roles);
                      });

                    this.getPurchaseOrder();
                    swal("Purchased order accepted!", {
                      icon: "success"
                    });
                  });
              }
            });
            break;

          default:
            break;
        }
      });
    },
    declinePurchaseOrder() {
      swal({
        text:
          "Decline Purchase Order #" + this.$route.params.purchase_order + "?",
        dangerMode: true,
        buttons: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .post(
              "api/purchase_order/decline/" + this.$route.params.purchase_order
            )
            .then(response => {
              this.$http.get("api/purchase_order").then(response => {
                this.$global.setPurchaseOrders(response.body);
              });

              this.$http
                .get("api/users/" + this.authenticatedUser.id)
                .then(response => {
                  this.$global.setRoles(response.body.roles);
                });

              this.getPurchaseOrder();
              swal("Purchase order declined!", {
                dangerMode: true
              });
            });
        }
      });
    },
    submitApproval() {
      swal("Submit now for approval?", {
        buttons: {
          submit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "submit":
            this.$validator.validateAll().then(result => {
              if (result) {
                this.$http
                  .put(
                    "api/purchase_order/" + this.$route.params.purchase_order,
                    this.data
                  )
                  .then(response => {});

                this.$http
                  .post(
                    "api/purchase_order/submit_approval/" +
                      this.$route.params.purchase_order
                  )
                  .then(response => {
                    this.$http.get("api/purchase_order").then(response => {
                      this.$global.setPurchaseOrders(response.body);
                    });

                    this.$http
                      .get("api/users/" + this.authenticatedUser.id)
                      .then(response => {
                        this.$global.setRoles(response.body.roles);
                      });

                    this.getPurchaseOrder();
                    swal("Submitted for approval!", {
                      icon: "success"
                    });
                  });
              }
            });

            break;

          default:
            break;
        }
      });
    },
    submitSupplier() {
      swal("Order now?", {
        buttons: {
          order: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "order":
            this.$http
              .post(
                "api/purchase_order/submit_supplier/" +
                  this.$route.params.purchase_order
              )
              .then(response => {
                this.$http.get("api/purchase_order").then(response => {
                  this.$global.setPurchaseOrders(response.body);
                });

                this.$http
                  .get("api/users/" + this.authenticatedUser.id)
                  .then(response => {
                    this.$global.setRoles(response.body.roles);
                  });

                this.getPurchaseOrder();
                swal("Submitted to supplier!", {
                  icon: "success"
                });
              });

            break;

          default:
            break;
        }
      });
    },
    savePurchaseOrder() {
      this.$http
        .put(
          "api/purchase_order/" + this.$route.params.purchase_order,
          this.data
        )
        .then(response => {
          this.$http.get("api/purchase_order").then(response => {
            this.$global.setPurchaseOrders(response.body);
          });

          swal(
            "Purchase Order #" +
              this.$route.params.purchase_order +
              " was succesfully updated!",
            {
              icon: "success"
            }
          );
        });
    },

    deleteDraft(id) {
      console.log(id);
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data.",
        icon: "warning",
        buttons: true,
        dangerMode: true
      })
        .then(willDelete => {
          if (willDelete) {
            this.$http
              .delete("api/purchase_order/" + id)
              .then(response => {
                console.log(response.body);
                this.$global.setPurchaseOrders(response.body);
                swal("Draft has been deleted!");
                this.$router.go(-1);
              })
              .catch(response => {
                swal({
                  title: "Error",
                  text: response.body.error,
                  icon: "error",
                  dangerMode: true
                });
              });
          }
        })
        .then(value => {
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
    receiveItem() {
      console.log(this.data_receives);
      this.$validator.validateAll().then(result => {
        if (result) {
          this.data_receives.purchase_order_id = this.$route.params.purchase_order;
          this.$http.post("api/stocks", this.data_receives).then(response => {
            console.log(response.body);
            if (response.body == "Serials already exist!") {
              swal({
                title: "Error",
                text: response.body,
                icon: "error",
                dangerMode: true
              });
            } else {
              this.getPurchaseOrder();
              swal("Ordered Item Successfully Received!", {
                icon: "success"
              });
            }

            // $("#receiveModal").modal("hide");

            this.data_receives.receives = [];
            this.data_receives.barcodes = null;

            this.$http.get("api/purchase_order").then(response => {
              this.$global.setPurchaseOrders(response.body);
            });

            this.$http.get("api/items").then(response => {
              this.$global.setItems(response.body);
            });
          });
        }
      });
    },
    pdfChanged(e) {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.data.upload = e.target.files[0];

          var fileReader = new FileReader();

          fileReader.readAsDataURL(e.target.files[0]);

          fileReader.onload = e => {
            this.data.upload.file = e.target.result;
          };
        }
      });
    },
    searchItem() {
      this.$http.post("api/items/search", this.search).then(response => {
        this.items = response.body;
      });
    },
    selectSupplier(supplier) {
      this.data.supplier = supplier;
    },

    selectItem(item) {
      var exists = false;

      for (var i = 0; i < this.data.orders.length; i++) {
        if (this.data.orders[i].id == item.id) {
          exists = true;
        }
      }

      if (!exists) {
        this.$http.get("api/items/" + item.id).then(response => {
          this.data.orders.push(response.body);
        });
      }

      // $("#itemModal").modal("hide");
    },
    removeItem(id) {
      swal({
        title: "",
        text: "Remove this item in the list?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          for (var index = 0; index < this.data.orders.length; index++) {
            if (this.data.orders[index].id == id) {
              this.data.orders.splice(index, 1);
            }
          }

          this.subtotal();
        }
      });
    },
    removeFile() {
      swal({
        title: "",
        text: "Remove uploaded file?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.data.upload = [];
        }
      });
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
            this.$router.go(-1);
            break;

          default:
            break;
        }
      });
    },
    subtotal() {
      var sum = 0,
        tax = 0,
        shipping = 0,
        temp_tax = 0;

      for (var index = 0; index < this.data.orders.length; index++) {
        if (
          this.data.orders[index].pivot.price &&
          this.data.orders[index].pivot.qty
        ) {
          sum +=
            this.data.orders[index].pivot.price *
            this.data.orders[index].pivot.qty;
        }
        if (this.data.orders[index].pivot.tax_rate) {
          temp_tax = this.data.orders[index].pivot.tax_rate / 100;
          tax +=
            temp_tax *
            this.data.orders[index].pivot.price *
            this.data.orders[index].pivot.qty;
        }
      }

      if (this.data.amount.overide) {
        tax = 0;

        if (this.data.amount.tax_overide) {
          tax = parseFloat(this.data.amount.tax_overide);
        }
      }

      if (this.data.amount.shipping_cost) {
        shipping = parseFloat(this.data.amount.shipping_cost);
      }

      console.log(sum);
      this.data.amount.subtotal = sum;
      this.data.amount.tax = tax;
      this.data.amount.shipping = shipping;
      this.data.amount.total = sum + tax + parseFloat(shipping);
    },
    print() {
      this.$htmlToPaper("printable");
    },
    selectFile() {
      document.getElementById("fileSelect").click();
    },
    previewFiles(receive, e) {
      console.log(receive);
      console.log(e);
      var files = e.target.files,
        f = files[0];
      var reader = new FileReader();

      reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: "array" });
        let sheetName = workbook.SheetNames[0];

        let worksheet = workbook.Sheets[sheetName];
        console.log(XLSX.utils.sheet_to_json(worksheet));
        console.log(this.data);
        this.file = workbook.SheetNames[0];
        receive.barcodes = XLSX.utils.sheet_to_json(worksheet);
        this.data_receives.barcodes = receive.barcodes;

        document.getElementById("fileSelect").value = null;
      }.bind(this);

      reader.readAsArrayBuffer(f);
    }
  }
};
</script>

<style scoped>
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

.table-wrap {
  height: 300px;
  overflow-y: auto;
  overflow-x: auto;
}

select {
  padding: 3px 0px !important;
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
