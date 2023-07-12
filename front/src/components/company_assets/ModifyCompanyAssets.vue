<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-lg-10 col-md-10">
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-8">
        <div class="card">
          <div class="header">
            <h2>Update Asset</h2>
          </div>
          <div class="card">
            <div class="body">
              <div class="row clearfix">
                <div class="col-sm-6">
                  <span>Date In</span>
                  <date-picker
                    v-model="asset.date_in"
                    :config="options"
                    v-validate="'required'"
                    name="date_in"
                    autocomplete="off"
                  ></date-picker>

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('date_in')"
                    >Date is required.</small
                  >
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-6">
                  <br />
                  <span>Asset Name</span>

                  <input
                    ref="name"
                    name="name"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.name"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >Asset name is required.</small
                  >
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Model</span>

                  <input
                    ref="model"
                    name="model"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.model"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('model')"
                    >Model is required.</small
                  >
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-6">
                  <br />
                  <span>Area</span>

                  <input
                    ref="area"
                    name="area"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.area"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >Area name is required.</small
                  >
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Type</span>

                  <select
                    class="form-control"
                    v-model="asset.company_assets_types_id"
                  >
                    <option
                      v-for="type in types"
                      :key="type.id"
                      v-bind:value="type.id"
                      >{{ type.type_name }}</option
                    >
                  </select>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-sm-12">
                  <br />
                  <span>Note/Remarks</span>

                  <textarea
                    type="text"
                    class="form-control"
                    rows="5"
                    name="remarks"
                    v-model="asset.remarks"
                  ></textarea>
                </div>
              </div>

              <div class="row clearfix">
                <div class="header">
                  <h5>Accountable</h5>
                </div>
                <div class="col-sm-6">
                  <br />
                  <span>Accountable name</span>

                  <input
                    type="text"
                    class="form-control"
                    v-model="asset.accountable_name"
                    autocomplete="off"
                    autofocus="on"
                  />
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Date Accounted</span>
                  <date-picker
                    v-model="asset.date_accounted"
                    :config="options"
                    name="accounted_date"
                    autocomplete="off"
                  ></date-picker>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-6 col-md-offset-6">
                  <br />
                  <div class="pull-right">
                    <button
                      @click="updateAsset"
                      class="btn btn-lg btn-info waves-effect waves-light"
                      v-show="roles.update_comp"
                    >
                      Save Changes
                    </button>

                    <button
                      class="btn btn-lg btn-danger waves-effect waves-light"
                      @click="deleteAsset"
                      v-show="roles.delete_comp"
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
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    datePicker
  },
  data() {
    return {
      categories: [],
      roles: [],
      types: [],
      asset: {
        accountable_name: "",
        area: "",
        company_assets_types_id: "",
        created_at: "",
        date_accounted: "",
        date_in: "",
        model: "",
        name: "",
        remarks: "",
        updated_at: "",
        id: ""
      },
      options: {
        format: "YYYY-MM-DD",
        useCurrent: false
      }
    };
  },
  mounted() {
    this.loadData();
    this.roles = this.$global.getRoles();
  },

  created() {
    this.$http.get("api/company_assets_type").then(response => {
      this.types = response.body;
    });
  },

  methods: {
    loadData() {
      this.$http
        .get("api/company_assets/" + this.$route.params.asset_id)
        .then(response => {
          //console.log(response.body);
          this.asset.accountable_name = response.body.accountable_name;
          this.asset.area = response.body.area;
          this.asset.company_assets_types_id =
            response.body.company_assets_types_id;
          this.asset.created_at = response.body.created_at;
          this.asset.date_accounted = response.body.date_accounted;
          this.asset.date_in = response.body.date_in;
          this.asset.id = response.body.id;
          this.asset.model = response.body.model;
          this.asset.name = response.body.name;
          this.asset.remarks = response.body.remarks;
          this.asset.updated_at = response.body.updated_at;
        });
    },
    deleteAsset() {
      swal({
        title: "Delete this asset?",
        text: "Warning, this would delete the asset permanently!",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .delete("api/company_assets/" + this.asset.id)
            .then(response => {
              swal("Deleted!", "Sucessfully deleted!", "success");
              this.$http.get("api/company_assets").then(response => {
                this.$global.setCompanyAssets(response.body);
                this.$router.go(-1);
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
    updateAsset() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal("Update this Asset?", {
            buttons: {
              yes: "yes",
              cancel: "cancel"
            }
          }).then(value => {
            switch (value) {
              case "yes":
                this.$http
                  .post("api/company_assets/update", this.asset)
                  .then(response => {
                    if (response.body != null) {
                      this.$http.get("api/company_assets").then(response => {
                        this.$global.setCompanyAssets(response.body);
                      });
                      swal("Asset Updated.", {
                        icon: "success"
                      });
                    }
                  });
                break;
              default:
                break;
            }
          });
        }
      });
    }
  }
};
</script>
