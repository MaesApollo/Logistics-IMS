<template>
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <div class="header">
            <h2>Manage Category</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <tbody>
                        <tr
                          v-for="(category, index) in categories"
                          :key="category.index"
                          style="cursor: pointer;"
                          @click="getCategory(index)"
                        >
                          <td>{{ category.name }}</td>
                        </tr>
                        <tr v-show="categories.length == 0">
                          <td colspan="5" class="text-center">
                            <small class="col-red">
                              <i>No category found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <button
                  type="button"
                  class="btn btn-sm btn-default waves-effect"
                  data-toggle="modal"
                  data-target="#modalAddCategory"
                  :disabled="!roles.create_item"
                >
                  <i class="material-icons">note_add</i>
                </button>
                <p class="pull-right">
                  {{ categories.length }} categories found.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-md-6">
        <div class="card">
          <div class="body">
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <span>Category Name</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      ref="name"
                      name="name"
                      class="form-control"
                      v-validate="'required'"
                      v-model="category.name"
                      autocomplete="off"
                      :disabled="!roles.update_item || !roles.delete_item"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12 col-lg-12">
                <div class="pull-right">
                  <button
                    class="btn btn-info waves-effect waves-light"
                    :disabled="!roles.update_item"
                    @click="update"
                  >
                    Save Changes
                  </button>
                  <button
                    class="btn btn-danger waves-effect waves-light"
                    @click="deleteCategory"
                    :disabled="!roles.delete_item"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <div class="header">
            <h2>Manage Asset Type</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <tbody>
                        <tr
                          v-for="(type, index) in types"
                          :key="type.index"
                          style="cursor: pointer;"
                          @click="getType(index)"
                        >
                          <td>{{ type.type_name }}</td>
                        </tr>
                        <tr v-show="type.length == 0">
                          <td colspan="5" class="text-center">
                            <small class="col-red">
                              <i>No asset type found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <button
                  type="button"
                  class="btn btn-sm btn-default waves-effect"
                  data-toggle="modal"
                  data-target="#modalAddType"
                  :disabled="!roles.create_item"
                >
                  <i class="material-icons">note_add</i>
                </button>
                <p class="pull-right">{{ types.length }} asset type found.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <div class="body">
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <span>Asset type name</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      ref="name"
                      name="name"
                      class="form-control"
                      v-validate="'required'"
                      v-model="type.type_name"
                      autocomplete="off"
                      :disabled="!roles.update_item || !roles.delete_item"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12 col-lg-12">
                <div class="pull-right">
                  <button
                    class="btn btn-info waves-effect waves-light"
                    :disabled="!roles.update_item"
                    @click="updateType"
                  >
                    Save Changes
                  </button>
                  <button
                    class="btn btn-danger waves-effect waves-light"
                    @click="deleteType"
                    :disabled="!roles.delete_item"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal ADD CATEGORY-->
    <div class="modal fade" id="modalAddCategory" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Category</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createCategory">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Category Name</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="name"
                          name="name"
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          v-model.trim="addCategory.name"
                          autocomplete="off"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('addCategory')"
                        >Category name is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <!-- Modal END ADD CATEGORY -->

    <!-- Modal ADD CATEGORY-->
    <div class="modal fade" id="modalAddType" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Asset type</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createType">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Asset type Name</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="type_name"
                          name="type_name"
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          v-model.trim="addType.type_name"
                          autocomplete="off"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('type_name')"
                        >Asset type name is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <!-- Modal END ADD CATEGORY -->
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      types: [],
      type: {},
      categories: [],
      category: {},
      roles: [],
      addType: {
        type_name: ""
      },
      addCategory: {
        name: ""
      }
    };
  },

  created() {
    this.categories = this.$global.getCategories();
    this.roles = this.$global.getRoles();
    this.loadTypes();
  },

  methods: {
    loadTypes() {
      this.$http.get("api/company_assets_type").then(response => {
        this.types = response.body;
      });
    },
    getType(index) {
      this.type = this.types[index];
    },
    updateType() {
      if (this.type.type_name != null) {
        this.$http
          .put("api/company_assets_type/" + this.type.id, this.type)
          .then(response => {
            this.loadTypes();
            swal(this.type.type_name, "has successfully updated!", {
              icon: "success"
            });

            this.type = {};
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
      } else {
        swal("type name is required.", "Information", "info");
      }
    },
    deleteType() {
      swal({
        title: "Delete " + this.type.type_name + "?",
        text: "Warning, this would delete the Asset type permanently!",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .delete("api/company_assets_type/" + this.type.id)
            .then(response => {
              swal(
                "Deleted!",
                "Sucessfully deleted " + this.type.type_name + "!",
                "success"
              );
              this.loadTypes();
              this.type = {};
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

    createType() {
      if (this.addType.type_name != "") {
        swal("Create new Asset type?", {
          buttons: {
            Yes: true,
            cancel: "Cancel"
          }
        }).then(value => {
          switch (value) {
            case "Yes":
              this.$http
                .post("api/company_assets_type", this.addType)
                .then(response => {
                  this.loadTypes();
                  swal(
                    this.addType.type_name,
                    "has successfully added!",
                    "success"
                  );
                });
              break;

            default:
              break;
          }
        });
      } else {
        swal("Asset type name is required.", "Information", "info");
      }
    },

    getCategory(index) {
      this.category = this.categories[index];
    },

    update() {
      if (this.category.name != null) {
        this.$http
          .put("api/category/" + this.category.id, this.category)
          .then(response => {
            this.categories = response.body;
            this.$global.setCategories(response.body);
            swal(this.category.name, "has successfully updated!", {
              icon: "success"
            });

            this.category = {};
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
      } else {
        swal("Category name is required.", "Information", "info");
      }
    },

    deleteCategory() {
      swal({
        title: "Delete " + this.category.name + "?",
        text: "Warning, this would delete the category permanently!",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .delete("api/category/" + this.category.id)
            .then(response => {
              swal(
                "Deleted!",
                "Sucessfully deleted " + this.category.name + "!",
                "success"
              );

              this.$global.setCategories(response.body);
              this.categories = this.$global.getCategories();
              this.category = [];
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

    createCategory() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/category", this.category)
            .then(response => {
              this.$http.get("api/warehouse").then(response => {
                this.$global.setCategories(response.body);
                swal(this.category.name, "was successfully added!", "success");
                this.$router.push({
                  path: "/manage/category"
                });
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
                  this.$refs.name.focus();
                }
              });
            });
        }
      });
    }
  }
};
</script>

<style scoped>
.table-wrap {
  height: 200px;
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
