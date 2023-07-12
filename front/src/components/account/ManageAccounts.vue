<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-md-1">
          <!-- NEW ITEM BUTTON -->
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="createNewUser"
            v-if="roles.create_account"
          >
            <i class="material-icons">note_add</i>
            <span>Create New User</span>
          </button>
          <button
            type="button"
            class="btn btn-default waves-effect disabled"
            v-else
          >
            <i class="material-icons">note_add</i>
            <span>Create New User</span>
          </button>
          <!--END NEW ITEM BUTTON -->
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="card">
          <div class="header">
            <h2>Manage Users Account</h2>
          </div>
          <div class="body">
            <form @submit.prevent="searchUser">
              <br />
              <div class="row clearfix">
                <div class="col-md-4 col-md-offset-6">
                  <div class="form-group">
                    <span>Search</span>
                    <div class="form-line">
                      <input
                        type="text"
                        class="form-control"
                        v-model="search.user"
                        autocomplete="off"
                      />
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <br />
                  <button
                    type="submit"
                    class="btn bg-black waves-effect waves-light"
                  >
                    Search
                  </button>
                  <button class="btn btn-success waves-effect" @click="reset">
                    Reset
                  </button>
                </div>
              </div>
            </form>
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                    >
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th width="20%">Created at</th>
                          <th width="20%">Updated at</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="user in users"
                          :key="user.id"
                          style="cursor: pointer;"
                          @click="getUser(user.id)"
                          data-toggle="modal"
                          data-target="#userModal"
                        >
                          <td>{{ user.name }}</td>
                          <td>{{ user.email }}</td>
                          <td>
                            <small>{{ user.created_at }}</small>
                          </td>
                          <td>
                            <small>{{ user.updated_at }}</small>
                          </td>
                        </tr>
                        <tr v-show="users.length < 1">
                          <td colspan="5" class="text-center">
                            No user found.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <hr />
                <p>{{ users.length }} users found.</p>
              </div>
            </div>
          </div>

          <!-- USER MODAL -->
          <div
            id="userModal"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            data-backdrop="static"
            data-keyboard="false"
          >
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="header">
                  <div class="row clearfix">
                    <div class="col-md-6">
                      <h2>Edit User Details</h2>
                    </div>
                    <div class="col-md-6">
                      <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                        @click="clearUser"
                      >
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="body">
                  <div class="row clearfix">
                    <div class="col-md-4">
                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <h3>Personal</h3>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Name</label>
                          <div class="input-group">
                            <div class="form-line">
                              <input
                                type="text"
                                ref="name"
                                name="name"
                                class="form-control"
                                v-validate="'required'"
                                v-model.trim="user.name"
                                autocomplete="off"
                                autofocus="on"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Department</label>
                          <div class="input-group">
                            <div class="form-line">
                              <model-list-select
                                class="search-list"
                                :list="departments"
                                option-value="id"
                                option-text="name"
                                v-model="user.department_id"
                                v-validate="'required'"
                                placeholder="Please select department .."
                              >
                              </model-list-select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Email</label>
                          <div class="input-group">
                            <div class="form-line">
                              <input
                                type="email"
                                class="form-control"
                                v-validate="{ required: true, email: true }"
                                v-model.trim="user.email"
                                autocomplete="off"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Password</label>
                          <div class="input-group">
                            <div class="form-line">
                              <input
                                name="password"
                                type="password"
                                class="form-control"
                                v-model.trim="user.password"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-7 col-md-offset-1">
                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <h3>Roles</h3>
                        </div>
                      </div>

                      <!-- ACCOUNT -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Account</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_account"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_account"
                          />
                          <label for="create_account">Create Account</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_account"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_account"
                          />
                          <label for="update_account">Update Account</label>
                        </div>
                      </div>
                      <hr />

                      <!-- CLIENT -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Client</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_client"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_client"
                          />
                          <label for="create_client">Create Client</label>
                        </div>
                        <div class="col-sm-6">
                          <input
                            type="checkbox"
                            id="update_client"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_client"
                          />
                          <label for="update_client">Update Client List</label>
                        </div>
                      </div>
                      <hr />

                      <!-- MANAGE SUPPLIER -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Supplier</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_supplier"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_supplier"
                          />
                          <label for="create_supplier">Create Supplier</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_supplier"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_supplier"
                          />
                          <label for="update_supplier">Update Supplier</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="delete_supplier"
                            class="filled-in chk-col-black"
                            v-model="user.roles.delete_supplier"
                          />
                          <label for="delete_supplier">Delete Supplier</label>
                        </div>
                      </div>
                      <hr />

                      <!-- INVENTORY -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Inventory</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_item"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_item"
                          />
                          <label for="create_item">Create Item</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_item"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_item"
                          />
                          <label for="update_item">Update Item</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="delete_item"
                            class="filled-in chk-col-black"
                            v-model="user.roles.delete_item"
                          />
                          <label for="delete_item">Delete Item</label>
                        </div>
                      </div>
                      <hr />

                      <!-- COMPONENTS -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Components</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_comp"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_comp"
                          />
                          <label for="create_comp">Create Component</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_comp"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_comp"
                          />
                          <label for="update_comp">Update Component</label>
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="delete_comp"
                            class="filled-in chk-col-black"
                            v-model="user.roles.delete_comp"
                          />
                          <label for="delete_comp">Delete Component</label>
                        </div>
                      </div>
                      <hr />

                      <!-- DIRECT RECEIVE -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Direct Receive</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-6">
                          <input
                            type="checkbox"
                            id="create_direct_receive"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_direct_receive"
                          />
                          <label for="create_direct_receive"
                            >Create Direct Receive</label
                          >
                        </div>
                        <div class="col-sm-6">
                          <input
                            type="checkbox"
                            id="update_direct_receive"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_direct_receive"
                          />
                          <label for="update_direct_receive"
                            >Update Direct Receive</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- PURCHASE ORDER -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Purchase Order</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="view_purchase_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.view_purchase_order"
                          />
                          <label for="view_purchase_order"
                            >View Purchase Order</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_purchase_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_purchase_order"
                          />
                          <label for="create_purchase_order"
                            >Create Purchase Order</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_purchase_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_purchase_order"
                          />
                          <label for="update_purchase_order"
                            >Update Purchase Order</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="approved_purchase_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.approved_purchase_order"
                          />
                          <label for="approved_purchase_order"
                            >Approved Purchase Order</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- SALES ORDER -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Sales Order</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_sales_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_sales_order"
                          />
                          <label for="create_sales_order"
                            >Create Sales Order</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_sales_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_sales_order"
                          />
                          <label for="update_sales_order"
                            >Update Sales Order</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="approved_sales_order"
                            class="filled-in chk-col-black"
                            v-model="user.roles.approved_sales_order"
                          />
                          <label for="approved_sales_order"
                            >Approved Sales Order</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- DELIVERY RECEIPT -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Delivery Receipt</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_delivery_receipt"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_delivery_receipt"
                          />
                          <label for="create_delivery_receipt"
                            >Create Delivery Receipt</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_delivery_receipt"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_delivery_receipt"
                          />
                          <label for="update_delivery_receipt"
                            >Update Delivery Receipt</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- SALES RETURN -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Sales Return</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_sales_return"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_sales_return"
                          />
                          <label for="create_sales_return"
                            >Create Sales Return</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_sales_return"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_sales_return"
                          />
                          <label for="update_sales_return"
                            >Update Sales Return</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="approve_sales_return"
                            class="filled-in chk-col-black"
                            v-model="user.roles.approve_sales_return"
                          />
                          <label for="approve_sales_return"
                            >Approve Sales Return</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- ACCOUNTABILITY -->
                      <div class="row">
                        <div class="col-md-12">
                          <h4>Manage Accountability</h4>
                        </div>
                      </div>
                      <div class="row clearfix">
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="create_accountability"
                            class="filled-in chk-col-black"
                            v-model="user.roles.create_accountability"
                          />
                          <label for="create_accountability"
                            >Create Accountability</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="update_accountability"
                            class="filled-in chk-col-black"
                            v-model="user.roles.update_accountability"
                          />
                          <label for="update_accountability"
                            >Update Accountability</label
                          >
                        </div>
                        <div class="col-sm-4">
                          <input
                            type="checkbox"
                            id="delete_accountability"
                            class="filled-in chk-col-black"
                            v-model="user.roles.delete_accountability"
                          />
                          <label for="delete_accountability"
                            >Delete Accountability</label
                          >
                        </div>
                      </div>
                      <hr />

                      <!-- EMAIL  -->
                      <div v-if="roles.admin">
                        <div class="row">
                          <div class="col-md-12">
                            <h4>Email</h4>
                          </div>
                        </div>
                        <div class="row clearfix">
                          <div class="col-sm-4">
                            <input
                              type="checkbox"
                              id="send_mr_email"
                              class="filled-in chk-col-black"
                              v-model="user.roles.send_mr_email"
                            />
                            <label for="send_mr_email"
                              >Email Material Request</label
                            >
                          </div>
                          <div class="col-sm-4">
                            <input
                              type="checkbox"
                              id="send_po_email"
                              class="filled-in chk-col-black"
                              v-model="user.roles.send_po_email"
                            />
                            <label for="send_po_email"
                              >Email Purchase Order</label
                            >
                          </div>
                        </div>
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button
                    class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    @click="update"
                    v-show="roles.create_account && roles.update_account"
                  >
                    Save Changes
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- END USER MODAL -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      users: [],
      user: {
        roles: {
          create_account: false
        }
      },
      search: {
        user: null
      },
      roles: [],
      departments: []
    };
  },

  created() {
    this.users = this.$global.getUsers();
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
    this.departments = this.$global.getDepartment();
  },

  methods: {
    getUser(id) {
      this.$http.get("api/users/" + id).then(response => {
        console.log(response.body);
        this.user = response.body;
        this.user.password = null;
      });
    },

    update() {
      console.log(this.user.roles);
      this.$validator.validateAll().then(result => {
        this.user.authenticatedUser = this.authenticatedUser;
        if (!result) {
          swal("Ops! The was something wrong updating this user.", {
            dangerMode: true
          });
        } else {
          this.$http
            .put("api/users/" + this.user.id, this.user)
            .then(response => {
              console.log(response.body);
              this.$global.setUsers(response.body.original);
              this.users = this.$global.getUsers();

              // $("#userModal").modal("hide");
              swal(this.user.name, "was successfully updated!", {
                icon: "success"
              });
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },

    searchUser() {
      this.$http.post("api/users/search", this.search).then(response => {
        this.users = response.body;
      });
    },

    reset() {
      this.search.user = "";
      this.searchUser();
    },

    clearUser() {
      this.user = {
        roles: {
          create_account: false
        }
      };
    },

    createNewUser() {
      swal("Create a new user?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/account"
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
textarea {
  resize: none;
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
</style>
