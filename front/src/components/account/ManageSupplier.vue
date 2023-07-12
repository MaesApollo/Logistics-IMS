<template>
  <div class="container-fluid">
    <div class="block-header">
      <button
        type="button"
        class="btn btn-default waves-effect"
        @click="createNewSupplier"
        v-show="roles.create_supplier"
      >
        <i class="material-icons">note_add</i>
        <span>Create New Supplier</span>
      </button>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Suppliers</h2>
      </div>
      <div class="body">
        <form @submit.prevent="searchSupplier">
          <div class="row clearfix">
            <div class="col-md-3 col-md-offset-7">
              <div class="form-group">
                <span>Search</span>
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    v-model="search.supplier"
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
                <table class="table table-striped table-condensed table-hover">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Contact</th>
                      <th>Created at</th>
                      <th>Updated at</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="supplier in suppliers"
                      :key="supplier.id"
                      style="cursor: pointer;"
                      @click="getSupplier(supplier.id)"
                      data-toggle="modal"
                      data-target="#supplierModal"
                      title="click more details"
                    >
                      <td>{{ supplier.name }}</td>
                      <td>{{ supplier.email }}</td>
                      <td>{{ supplier.contact }}</td>
                      <td>
                        <small>{{ supplier.created_at }}</small>
                      </td>
                      <td>
                        <small>{{ supplier.updated_at }}</small>
                      </td>
                    </tr>
                    <tr v-show="suppliers.length == 0">
                      <td colspan="5" class="text-center">
                        <small class="col-red">
                          <i>No supplier found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <hr />
            <p>{{ suppliers.length }} supplier displayed.</p>
          </div>
        </div>
      </div>

      <!-- SALES ORDER MODAL -->
      <div
        id="supplierModal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        data-keyboard="false"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="header">
              <div class="row clearfix">
                <div class="col-md-6">
                  <h2>
                    <span v-show="roles.update_supplier">Edit</span> Supplier
                    Details
                  </h2>
                </div>
                <div class="col-md-6">
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="name"
                        name="name"
                        class="form-control"
                        v-validate="'required'"
                        v-model="supplier.name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.update_supplier"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
                      >Name is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Registered TIN</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        class="form-control"
                        v-model="supplier.tin"
                        autocomplete="off"
                        :disabled="!roles.update_supplier"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Contact</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        class="form-control"
                        v-model="supplier.contact"
                        autocomplete="off"
                        :disabled="!roles.update_supplier"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Email</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        class="form-control"
                        name="email"
                        v-validate="{ email: true }"
                        v-model="supplier.email"
                        autocomplete="off"
                        :disabled="!roles.update_supplier"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('email')"
                      >{{ errors.first("email") }}</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Address</span>
                  <div class="input-group">
                    <div class="form-line">
                      <textarea
                        type="text"
                        class="form-control"
                        v-model="supplier.address"
                        :disabled="!roles.update_supplier"
                        rows="4"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12 col-sm-12">
                  <div class="input-group">
                    <span>Locale</span>
                    <div class="form-line">
                      <select
                        name="locale"
                        class="form-control"
                        v-validate="'required'"
                        v-model="supplier.locale_id"
                        :disabled="!roles.update_supplier"
                      >
                        <option
                          v-for="locale in locales"
                          :key="locale.id"
                          v-bind:value="locale.id"
                          >{{ locale.name }}</option
                        >
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                class="btn btn-lg btn-info waves-effect waves-light"
                @click="update"
                v-show="roles.update_supplier"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- END SALES ORDER MODAL -->
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      locales: null,
      authenticatedUser: [],
      roles: [],
      suppliers: [],
      supplier: [],
      search: {
        supplier: null
      }
    };
  },

  created() {
    this.suppliers = this.$global.getSupplier();
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.getLocale();
  },

  methods: {
    getLocale() {
      this.$http.get("api/locale").then(response => {
        this.locales = response.body;
      });
    },

    getSupplier(id) {
      this.supplier = [];

      this.$http.get("api/supplier/" + id).then(response => {
        this.supplier = response.body;
      });
    },

    update() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/supplier/" + this.supplier.id, this.supplier)
            .then(response => {
              this.suppliers = response.body;
              this.$global.setSupplier(response.body);
              // $("#supplierModal").modal("hide");
              swal(this.supplier.name, "was successfully updated!", {
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

    searchSupplier() {
      this.$http.post("api/supplier/search", this.search).then(response => {
        this.suppliers = response.body;
      });
    },

    reset() {
      this.search.supplier = "";
      this.searchSupplier();
    },

    createNewSupplier() {
      swal("Create a new supplier?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/supplier"
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
</style>
