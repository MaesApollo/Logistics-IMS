<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-md-3">
          <!-- NEW WAREHOUSE BUTTON -->
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="createNewWarehouse"
            :disabled="!roles.create_item"
          >
            <i class="material-icons">note_add</i>
            <span>Create New Warehouse</span>
          </button>
          <!--END NEW WAREHOUSE BUTTON -->
        </div>
      </div>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Warehouses</h2>
      </div>
      <div class="body">
        <form @submit.prevent="searchWarehouse">
          <br />
          <div class="row clearfix">
            <div class="col-md-3 col-md-offset-7">
              <div class="form-group">
                <span>Search</span>
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    v-model="search.warehouse"
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
                      <th>Warehouse Name</th>
                      <th>Address</th>
                      <th>Created at</th>
                      <th>Updated at</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="warehouse in warehouses"
                      :key="warehouse.id"
                      style="cursor: pointer;"
                      @click="getWarehouse(warehouse.id)"
                      data-toggle="modal"
                      data-target="#warehouseModal"
                    >
                      <td>{{ warehouse.name }}</td>
                      <td>
                        <small>
                          <i>{{ warehouse.address }}</i>
                        </small>
                      </td>
                      <td>
                        <small>{{ warehouse.created_at }}</small>
                      </td>
                      <td>
                        <small>{{ warehouse.updated_at }}</small>
                      </td>
                    </tr>
                    <tr v-show="warehouses.length == 0">
                      <td colspan="4" class="text-center col-red">
                        <small>
                          <i>No warehouse found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <p>{{ warehouses.length }} warehouses found.</p>
          </div>
        </div>

        <!-- WAREHOUSE MODAL -->
        <div
          id="warehouseModal"
          class="modal fade"
          tabindex="-1"
          role="dialog"
          data-backdrop="static"
          data-keyboard="false"
        >
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="header">
                <h2>
                  Warehouse
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </h2>
              </div>
              <form @submit.prevent="update">
                <div class="body">
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
                            v-model="warehouse.name"
                            autocomplete="off"
                            autofocus="on"
                            :disabled="!roles.update_item"
                          />
                        </div>
                        <small
                          class="text-danger pull-left"
                          v-show="errors.has('name')"
                          >Warehouse name is required.</small
                        >
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <label>Address</label>
                      <div class="input-group">
                        <div class="form-line">
                          <textarea
                            type="text"
                            class="form-control"
                            v-model="warehouse.address"
                            rows="2"
                            :disabled="!roles.update_item"
                          ></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <input
                    type="submit"
                    value="Save Changes"
                    class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    :disabled="!roles.update_item"
                  />
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- END WAREHOUSE MODAL -->
      </div>
    </div>
  </div>
</template>

<script>
import PreLoader from "../PreLoader.vue";
import swal from "sweetalert";

export default {
  components: {
    "pre-loader": PreLoader
  },

  data() {
    return {
      warehouses: [],
      warehouse: [],
      search: {
        warehouse: null
      },
      roles: []
    };
  },

  created() {
    this.warehouses = this.$global.getWarehouses();
    this.roles = this.$global.getRoles();
  },

  methods: {
    getWarehouse(id) {
      this.warehouse = [];

      this.$http.get("api/warehouse/" + id).then(response => {
        this.warehouse = response.body;
      });
    },

    update() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/warehouse/" + this.warehouse.id, this.warehouse)
            .then(response => {
              this.warehouses = response.body;
              this.$global.setWarehouses(response.body);
              // $("#warehouseModal").modal("hide");
              swal(this.warehouse.name, "was successfully created!", {
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

    searchWarehouse() {
      this.$http.post("api/warehouse/search", this.search).then(response => {
        this.warehouses = response.body;
      });
    },

    reset() {
      this.search.warehouse = "";
      this.searchWarehouse();
    },

    createNewWarehouse() {
      swal("Add a new warehouse?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/warehouse"
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
