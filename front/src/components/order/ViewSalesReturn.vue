<template>
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-lg-12 col-md-12">
        <div class="block-header" id="sales_return_button">
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
            class="btn btn-danger waves-effect"
            @click="deleteReturn(dataStock.id)"
            :hidden="dataStock.status != 'For Approval' || !roles.admin"
          >
            <i class="material-icons">delete_forever</i>
            <span>Delete</span>
          </button>
          <button class="btn btn-lg btn-default waves-effect" @click="printNow">
            Print Preview
          </button>

          <div
            :hidden="
              dataStock.status != 'For Approval' || !roles.approve_sales_return
            "
            style="float:right; display:block"
          >
            <button
              type="button"
              class="btn btn-lg btn-success waves-effect"
              @click="accept"
            >
              <span>Accept</span>
            </button>
            <!-- <button type="button" class="btn btn-lg btn-danger waves-effect">
              <span>Decline</span>
            </button> -->
          </div>
        </div>
        <div class="card" id="sales_return_form">
          <div class="body">
            <div class="row clearfix">
              <div class="col-md-12 col-sm-12">
                <h4>Sales Return #{{ dataStock.id }}</h4>
              </div>
            </div>
            <hr />
            <div class="row clearfix">
              <div class="col-md-2">
                <b>Date Receive:</b>
                <input
                  class="form-control"
                  type="text"
                  v-model="dataStock.date_recieve"
                  readonly
                />
              </div>

              <div class="col-md-2">
                <b>Returnee:</b>
                <br />
                <input
                  class="form-control"
                  type="text"
                  v-model="dataStock.returnee"
                  readonly
                />
              </div>
              <div class="col-md-3">
                <b>From:</b>
                <br />
                <strong>{{ dataStock.from_name }}</strong>
                <br />
                <span>{{ dataStock.from_address }}</span>
                <br />
                <span>{{ dataStock.from_contact }}</span>
              </div>

              <div class="col-md-3">
                <b>To:</b>
                <br />
                <strong>Logistics</strong>
              </div>
            </div>

            <!-- START Return TABLE -->
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap" style="height:auto;min-height:60vh">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Qty</th>
                          <th>Note</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in itemsDis" :key="index">
                          <td>
                            {{ item.id }}
                          </td>
                          <td>{{ item.name }}</td>
                          <td>{{ item.desc }}</td>
                          <td>{{ item.qty }}</td>
                          <td>{{ item.note }}</td>
                          <!-- <td>{{ item.status }}</td> -->
                          <td :hidden="dataStock.status != 'For Approval'">
                            <select
                              class="form-control"
                              v-model="item.status"
                              @change="updateStatus"
                              :disabled="!roles.update_sales_return"
                            >
                              <option value="Stocked in">Stocked in</option>
                              <option value="Defective">Defective</option>
                              <option value="For repair">For repair</option>
                              <option value="Refurbish">Refurbish</option>
                              <option value="Under observation"
                                >Under observation</option
                              >
                              <option value="Stock transfer"
                                >Stock transfer</option
                              >
                            </select>
                          </td>
                          <td :hidden="dataStock.status == 'For Approval'">
                            {{ item.status }}
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
                  type="text"
                  class="form-control"
                  rows="5"
                  v-model="dataStock.remarks"
                  readonly
                ></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="body" id="print_form">
          <div class="row clearfix">
            <div class="col-md-12 col-sm-12">
              <h4>Sales Return #{{ dataStock.id }}</h4>
            </div>
          </div>
          <hr />
          <div class="row clearfix">
            <div class="col-md-2">
              <b>Date Receive:</b>
              <input
                class="form-control"
                type="text"
                v-model="dataStock.date_recieve"
                readonly
              />
            </div>

            <div class="col-md-2">
              <b>Returnee:</b>
              <br />
              <input
                class="form-control"
                type="text"
                v-model="dataStock.returnee"
                readonly
              />
            </div>
            <div class="col-md-3">
              <b>From:</b>
              <br />
              <strong>{{ dataStock.from_name }}</strong>
              <br />
              <span>{{ dataStock.from_address }}</span>
              <br />
              <span>{{ dataStock.from_contact }}</span>
            </div>

            <div class="col-md-3">
              <b>To:</b>
              <br />
              <strong>Logistics</strong>
            </div>
          </div>

          <!-- START Return TABLE -->
          <div class="row clearfix">
            <div class="col-md-12">
              <div class="table-wrap" style="height:auto;min-height:60vh">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Note</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in itemsDis" :key="index">
                        <td>
                          {{ item.id }}
                        </td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.desc }}</td>
                        <td>{{ item.qty }}</td>
                        <td>{{ item.note }}</td>
                        <!-- <td>{{ item.status }}</td> -->
                        <td :hidden="dataStock.status != 'For Approval'">
                          <select
                            class="form-control"
                            v-model="item.status"
                            @change="updateStatus"
                            :disabled="!roles.update_sales_return"
                          >
                            <option value="Stocked in">Stocked in</option>
                            <option value="Defective">Defective</option>
                            <option value="For repair">For repair</option>
                            <option value="Refurbish">Refurbish</option>
                            <option value="Under observation"
                              >Under observation</option
                            >
                            <option value="Stock transfer"
                              >Stock transfer</option
                            >
                          </select>
                        </td>
                        <td :hidden="dataStock.status == 'For Approval'">
                          {{ item.status }}
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
                type="text"
                class="form-control"
                rows="5"
                v-model="dataStock.remarks"
                readonly
              ></textarea>
            </div>
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

export default {
  data() {
    return {
      authenticatedUser: [],
      itemsDis: [],
      roles: [],
      dataStock: {
        id: null,
        from_name: "",
        from_contact: "",
        from_address: "",
        status: "",
        clientTo: [],
        date_recieve: "",
        returnee: "",
        remarks: ""
      },
      data: {
        status: ""
      }
    };
  },

  created() {
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
  },
  beforeMount() {},

  mounted() {
    this.loadData();
    // console.log(this.itemsDis);
    window.onafterprint = function() {
      $(".content").css("margin-left", "315px");
      $(".content").css("margin-right", "15px");
      $(".content").css("margin-top", "100px");
      $("#print_form").css("display", "none");
      $("#sales_return_button").css("display", "block");
      $("#sales_return_form").css("display", "block");
      $("#leftsidebar").css("display", "inline-block");
      $(".navbar").css("display", "block");
    };
  },

  methods: {
    loadData() {
      this.$http
        .get("api/SalesReturns/" + this.$route.params.sales_return_id)
        .then(response => {
          //console.log(response.body);
          this.dataStock.id = this.$route.params.sales_return_id;
          this.dataStock.from_id = response.body.from_id;
          this.dataStock.from_name = response.body.from_name;
          this.dataStock.from_contact = response.body.from_contact;
          this.dataStock.from_address = response.body.from_address;
          this.dataStock.status = response.body.status;

          this.dataStock.date_recieve = response.body.date_return;
          this.dataStock.returnee = response.body.returnee;
          this.dataStock.remarks = response.body.remarks;
          let items = [];
          response.body.items.forEach(function(item) {
            items.push({
              id: item.id,
              name: item.name,
              desc: item.description,
              qty: item.qty,
              status: item.status,
              type: item.type_id,
              note: item.note
            });
          });
          this.itemsDis = items;
        });
    },
    updateStatus() {
      var data = {
        id: this.dataStock.id,
        item: this.itemsDis,
        user: this.authenticatedUser
      };

      console.log(data);
      this.$http.post("api/sales_return/updateStatus", data).then(response => {
        console.log(response.body);
      });
    },

    accept() {
      this.$validator.validateAll().then(result => {
        if (result) {
          var data = {
            return_details: this.dataStock,
            return_items: this.itemsDis,
            user: this.authenticatedUser
          };
          this.$http.post("api/sales_return/accept", data).then(response => {
            console.log(response.body);
            // if (response.body == "Fail to return items!") {
            //   swal({
            //     title: "Error",
            //     text: response.body,
            //     icon: "error",
            //     dangerMode: true
            //   });
            // } else {
            // swal("Item Returned.", {
            //   icon: "success"
            // });

            this.loadData();
            this.$root.$emit("Sidebar");
            this.$global.setSalesReturn(response.body);

            swal("Sales Return #" + this.dataStock.id + " accepted!", {
              icon: "success"
            });
            // }
          });
        }
      });
    },

    deleteReturn(id) {
      console.log(id);
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data.",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http.delete("api/SalesReturns/" + id).then(response => {
            console.log(response.body);
            if (response.body == "ok") {
              swal("Transmittal has been deleted!", {
                icon: "success",
                window: history.back()
              });
            } else {
              swal("Cancelled Deletion");
            }
          });
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
            this.$router.push({
              path: "/list/sales_return"
            });
            break;

          default:
            break;
        }
      });
    },

    printNow() {
      $(".content").css("margin-left", "0px");
      $(".content").css("margin-right", "0px");
      $(".content").css("margin-top", "25px");
      $("#print_form").css("display", "block");
      $("#sales_return_button").css("display", "none");
      $("#sales_return_form").css("display", "none");
      $("#leftsidebar").css("display", "none");
      $(".navbar").css("display", "none");

      window.print();
    }
  }
};
</script>

<style scoped>
@media print {
  .printable {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
  }
}

#print_form {
  display: none;
}

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
</style>
