<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-md-4">
          <button
            class="btn btn-default waves-effect"
            @click="createCompanyAsset"
            :disabled="!roles.create_item"
          >
            <i class="material-icons">note_add</i>
            <span>Create New Company Asset</span>
          </button>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Company Assets</h2>
      </div>
      <div class="body">
        <!-- START SEARCH FORM -->
        <form @submit.prevent="searchAssets">
          <div class="row clearfix">
            <div class="col-md-5">
              <div class="form-group">
                <div class="form-line">
                  <span>Search</span>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.assets"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <span>Type</span>
                <div class="form-line">
                  <select class="form-control" v-model="search.type">
                    <option
                      v-for="type in types"
                      :key="type.id"
                      v-bind:value="type.id"
                      >{{ type.type_name }}</option
                    >
                    <option value selected>All</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <br />
              <button class="btn bg-black waves-effect waves-light bg-black">
                Filter
              </button>
              <button class="btn btn-success waves-effect" @click="resetSearch">
                Clear Filter
              </button>
            </div>
          </div>
        </form>
        <!-- END SEARCH FORM -->
        <div class="row clearfix">
          <div class="col-md-12">
            <!-- START ORDER LIST TABLE -->
            <div class="table-wrap">
              <div class="table-responsive">
                <table
                  class="table table-striped table-condensed table-hover"
                  id="SalesOrderTable"
                >
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Type</th>
                      <th>Model</th>
                      <th>Area</th>
                      <th>Date in</th>
                      <th>Accountable</th>
                      <th>Date Accounted</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <router-link
                      tag="tr"
                      :to="'/company_assets/' + asset.id + '/edit'"
                      v-for="asset in assets"
                      :key="asset.id"
                      style="cursor: pointer;"
                    >
                      <td>{{ asset.name }}</td>
                      <td>{{ asset.type_name }}</td>
                      <td>{{ asset.model }}</td>
                      <td>{{ asset.area }}</td>
                      <td>{{ asset.date_in }}</td>
                      <td>{{ asset.accountable_name }}</td>
                      <td>{{ asset.date_accounted }}</td>
                      <td>{{ asset.remarks }}</td>
                    </router-link>

                    <tr v-show="assets.length == 0">
                      <td colspan="8" class="text-center">
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
            <p>{{ assets.length }} orders found.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var moment = require("moment");
moment().format();

export default {
  data() {
    return {
      assets: [],
      roles: [],
      types: [],
      authenticatedUser: [],
      search: {
        assets: "",
        type: ""
      }
    };
  },

  created() {
    this.assets = this.$global.getCompanyAssets();
    this.roles = this.$global.getRoles();
    this.authenticatedUser = this.$global.getUser();
    this.$http.get("api/company_assets_type").then(response => {
      this.types = response.body;
    });
  },

  mounted() {},

  methods: {
    searchAssets() {
      this.$http
        .post("api/company_assets/search", this.search)
        .then(response => {
          this.assets = response.body;
        });
    },

    resetSearch() {
      this.search.assets = "";
      this.search.type = "";
      this.searchAssets();
    },

    createCompanyAsset() {
      swal("Create new asset ?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/company_assets"
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
</style>
