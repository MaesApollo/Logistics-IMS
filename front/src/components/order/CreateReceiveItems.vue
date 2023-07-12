<template>
  <div class="container-fluid">
    <!-- <pre-loader></pre-loader> -->
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-md-12">
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
            class="btn btn-lg btn-info waves-effect"
            @click="receive"
            v-if="data.receives.length >= 1 && data.supplier != null"
          >
            <span>Receive items</span>
          </button>
          <!-- SELECT SUPPLIER -->
          <button
            class="btn btn-lg btn-default waves-effect"
            data-toggle="modal"
            data-target="#supplierModal"
          >
            <span>Select Supplier</span>
          </button>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="body">
        <div class="row clearfix">
          <div class="col-md-6 col-sm-12">
            <h4>Direct Receive Items</h4>
          </div>
        </div>
        <div class="row clearfix" style="margin-top:-20px;display:flex">
          <div style="width:15%">
            <img src="../../img/logo.jpg" width="100%" />
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
              <small style="color:red">
                <i>select supplier</i>
              </small>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-md-6 col-sm-12">
            <p>
              Date Received

              <b-form-datepicker
                id="datepicker-valid"
                v-model="data.date_receive"
                :state="true"
              ></b-form-datepicker>
              <small class="text-danger pull-left" v-show="errors.has('date')"
                >Date is required.</small
              >
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
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in data.receives" :key="item.id">
                    <td>{{ item.name }} - {{ item.description }}</td>
                    <td>{{ item.id }}</td>
                    <td>
                      <input
                        type="text"
                        v-model="item.qty"
                        @keyup="subtotal"
                        v-validate="'required|min_value:1|numeric'"
                        v-bind:name="item.id + 'qty'"
                      />
                      <p>
                        <small
                          class="text-danger"
                          v-show="errors.has(item.id + 'qty')"
                          >Qty is required</small
                        >
                      </p>
                    </td>

                    <td>
                      <input
                        type="text"
                        v-model="item.price"
                        v-validate="'required'"
                        v-bind:name="item.id + 'price'"
                      />
                      <p>
                        <small
                          class="text-danger"
                          v-show="errors.has(item.id + 'price')"
                          >Price is required</small
                        >
                      </p>
                    </td>
                    <td>
                      {{ item.price * item.qty }}
                    </td>
                    <td>
                      <select
                        v-model="item.received_to"
                        v-validate="'required'"
                        v-bind:name="item.id + 'receive_to'"
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
                          v-show="errors.has(item.id + 'receive_to')"
                          >Receiving is required</small
                        >
                      </p>
                    </td>
                    <td>
                      <a
                        href="javascript:void(0);"
                        @click="removeItem(item.id)"
                        title="Remove"
                      >
                        <i
                          class="material-icons text-danger"
                          style="font-size: 16px !important"
                          >delete</i
                        >
                      </a>
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
                      <input
                        type="file"
                        id="fileSelect"
                        name="fileSelect"
                        @change="previewFiles(item, $event)"
                        style="visibility:hidden;"
                      />
                    </td>
                  </tr>
                  <tr v-show="data.receives.length < 1">
                    <td class="text-center" colspan="5">
                      <small class="col-red">
                        <i>No items selected yet.</i>
                      </small>
                    </td>
                    <td></td>
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
              class="btn btn-sm btn-default waves-effect"
              data-toggle="modal"
              data-target="#itemModal"
            >
              <i class="material-icons">note_add</i>
              <span>Add Items</span>
            </button>
            <hr />
          </div>
        </div>
        <div class="col-md-6 col-xs-6">
          <b>Recipient:</b>
          <div class="input-group">
            <div class="form-line">
              <textarea
                type="text"
                class="form-control"
                v-model="data.class"
              ></textarea>
            </div>
          </div>
          <br />
          <b>Memo:</b>
          <div class="input-group">
            <div class="form-line">
              <textarea
                type="text"
                class="form-control"
                v-model="data.note"
              ></textarea>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xs-6">
          <div class="table-responsive">
            <table class="table">
              <tbody>
                <tr>
                  <th>Total Amount:</th>
                  <td>{{ formatPrice(data.total) }}</td>
                </tr>
              </tbody>
            </table>
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
            <div id="snackbar">Item Added.</div>
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
                        @keyup.delete="searchItem"
                      />
                    </div>
                  </div>
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
                          ADD
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
              <div class="col-md-5">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      id="search"
                      type="text"
                      class="form-control"
                      v-model="search.supplier"
                      autocomplete="off"
                      @keyup="searchSupplier"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-7">
                <br />
                <button
                  class="btn bg-black waves-effect waves-light"
                  @click="searchSupplier()"
                >
                  Search
                </button>
                <button
                  class="btn btn-success waves-effect"
                  @click="resetSupplier()"
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
import datePicker from "vue-bootstrap-datetimepicker";
import PreLoader from "../PreLoader.vue";
import { BModal, VBModal } from "bootstrap-vue";

export default {
  components: {
    "date-picker": datePicker,
    "pre-loader": PreLoader,
    BModal
  },
  directives: { "b-modal": VBModal },

  data() {
    return {
      data: {
        user: [],
        supplier: null,
        date_receive: null,
        class: null,
        note: null,
        receives: [],
        barcodes: null,
        total: 0
      },
      search: {
        item: null
      },
      items: [],
      warehouses: [],
      suppliers: []
    };
  },

  beforeMount() {},

  created() {
    this.warehouses = this.$global.getWarehouses();
    this.authenticatedUser = this.$global.getUser();
    this.loadItems();
    this.loadSuppliers();
  },

  mounted() {},

  methods: {
    loadSuppliers() {
      this.$http.get("api/supplier").then(response => {
        this.suppliers = response.body;
      });
    },
    loadItems() {
      this.$http.post("api/items/limited").then(response => {
        this.items = response.body;
      });
    },
    searchSupplier() {
      this.$http.post("api/supplier/search", this.search).then(response => {
        this.suppliers = response.body;
      });
    },
    getReceives() {
      console.log(this.$route.params);
    },
    selectFile() {
      document.getElementById("fileSelect").click();
    },
    searchItem() {
      this.$http.post("api/items/search", this.search).then(response => {
        this.items = response.body;
      });
    },

    previewFiles(item, e) {
      console.log(item);
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
        item.barcodes = XLSX.utils.sheet_to_json(worksheet);
        item.qty = item.barcodes.length;
        this.data.barcodes = item.barcodes;

        document.getElementById("fileSelect").value = null;
      }.bind(this);

      reader.readAsArrayBuffer(f);
    },

    selectItem(item) {
      var exists = false;

      for (var i = 0; i < this.data.receives.length; i++) {
        if (this.data.receives[i].id == item.id) {
          exists = true;
        }
      }

      if (!exists) {
        this.$http.get("api/items/" + item.id).then(response => {
          this.data.receives.push(response.body);
        });
      }

      var x = document.getElementById("snackbar");
      x.className = "show";
      setTimeout(function() {
        x.className = x.className.replace("show", "");
      }, 2000);

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
          for (var index = 0; index < this.data.receives.length; index++) {
            if (this.data.receives[index].id == id) {
              this.data.receives.splice(index, 1);
            }
          }
        }
      });
    },

    receive() {
      console.log(this.data);
      if (this.data.supplier == null) {
        swal("Please Input Supplier!");
      } else this.data.user = this.authenticatedUser;
      swal("Are you sure you want to receive the items?", {
        buttons: {
          receive: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "receive":
            this.$validator.validateAll().then(result => {
              if (result) {
                this.$root.$emit("pageLoading");
                this.$http
                  .post("api/stocks/direct_receive", this.data)
                  .then(response => {
                    console.log(response.body);
                    this.$root.$emit("pageLoaded");
                    if (response.body == "Serials already exist!") {
                      swal({
                        title: "Error",
                        text: response.body,
                        icon: "error",
                        dangerMode: true
                      });
                    } else {
                      swal("Items Received Succesfully.", {
                        icon: "success"
                      });
                      this.$http.get("api/items").then(response => {
                        this.$global.setItems(response.body);
                      });

                      this.$router.push({
                        path: "/direct_receives"
                      });
                      this.data.receives = [];
                      this.data.barcodes = null;
                      this.data.date_receive = null;
                      this.data.class = null;
                      this.data.note = null;
                    }
                  });
              }
            });

            break;

          default:
            break;
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
    selectSupplier(supplier) {
      this.data.supplier = supplier;
      console.log(supplier);
      console.log(this.authenticatedUser);
    },
    subtotal() {
      console.log(this.data.receives);
      var total = 0;

      for (var index = 0; index < this.data.receives.length; index++) {
        if (this.data.receives[index].price && this.data.receives[index].qty) {
          total +=
            this.data.receives[index].price * this.data.receives[index].qty;
        }
      }

      // console.log(total);
      this.data.total = total;
    },
    formatPrice(value) {
      var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2
      });
      return formatter.format(value);
    },

    resetSupplier() {
      this.search.supplier = "";
      this.searchSupplier();
    }
  }
};
</script>

<style scoped>
.table-wrap {
  height: 300px;
  overflow-y: auto;
  overflow-x: hidden;
}

select {
  padding: 3px 0px !important;
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

@-webkit-keyframes fadein {
  from {
    top: 10px;
    opacity: 0;
  }
  to {
    top: 100px;
    opacity: 1;
  }
}

@keyframes fadein {
  from {
    top: 10px;
    opacity: 0;
  }
  to {
    top: 100px;
    opacity: 1;
  }
}

@-webkit-keyframes fadeout {
  from {
    top: 100px;
    opacity: 1;
  }
  to {
    top: 190px;
    opacity: 0;
  }
}

@keyframes fadeout {
  from {
    top: 100px;
    opacity: 1;
  }
  to {
    top: 190px;
    opacity: 0;
  }
}
</style>
